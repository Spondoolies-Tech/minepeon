<?php

require_once('global.inc.php');
include('head.php');
include('menu.php');
?>

<script type="text/javascript" id="js">
    function post_to_url(path, params, method) {
        method = method || "post"; // Set method to post by default if not specified.

        $.ajax({
            url: path,
            type: method,
            dataType: 'json',
            data: params
        })
            .always(function(result) {
                //Refresh the WiFi list, and indicate the (probably) connected WiFi network
                scanWiFi();
        });

        bootbox.dialog({
            title: "Please wait",
            message: "Connecting to the chosen WiFi network...",
            closeButton: false
        });
    }


    function scanWiFi(){
        //Just reload the page to initiate WiFi scan
        window.location.replace("/wifi.php")
    }

    function connectToWiFi(wifiNameParam, keyMgmtParam, protocolParam, pairWiseParam, groupCiphersParam, encrypted){
        if(encrypted) {
            bootbox.prompt("Please enter the chosen WiFi network password", function(result) {
                if(result != null && result.length > 0) {
                    post_to_url('/wifi_connect.php', {connect: 'true', wifiName: wifiNameParam, password: result, keyMgmt: keyMgmtParam, protocol: protocolParam, pairWise: pairWiseParam, groupCiphers: groupCiphersParam});
                }
            });
        }
        else {
            post_to_url('/wifi_connect.php', {connect: 'true', wifiName: wifiNameParam, keyMgmt: keyMgmtParam, protocol: protocolParam, pairWise: pairWiseParam, groupCiphers: groupCiphersParam});
        }
    }
</script>

<?php
//Scan for WiFi on every page refresh
$cmdOutput = array();
exec("/sbin/iwlist wlan0 scan | /usr/local/bin/iwlist-scan-parse.awk 2>&1", $cmdOutput);
$wifiJson = join("", $cmdOutput);
$wifiNetworks = json_decode($wifiJson, true);

//Find the connected WiFi network (if any)
$connectedWiFi = exec("iwgetid wlan0 --raw --ap");
?>

    <div class="container">
        <center><h1>WiFi networks</h1></center>
        <br/><br/>
        <button class="btn btn-default" onclick="scanWiFi()">Re-scan</button>
        <input type='hidden' name='wifiScan'>
        <br><br>
<?php
if(isset($wifiNetworks) && isset($wifiNetworks["WiFi"]) && sizeof($wifiNetworks["WiFi"]) > 0){ ?>
        <table id="stats" class="tablesorter table table-striped table-hover stats">
            <thead>
            <tr>
                <th>SSID</th>
                <th>Signal Quality</th>
                <th>Secured</th>
                <th>Connect to WiFi</th>
            </tr>
            </thead>
            <tbody>

<?php
                foreach($wifiNetworks["WiFi"] as $wifi){
                    //Check if the iterated WiFi is the connected one
                    $connected = $connectedWiFi == $wifi["MAC"];

                    //Check signal quality
                    $signal = explode('/', $wifi["Quality"]);
                    $goodSignal = ($signal[0]/$signal[1] > WIFI_SIGNAL_THRESHOLD) ? true : false;

                    //Add visual indicators
                    $colorCSS = $goodSignal ? "" : "text-danger";
                    $colorCSS = $wifi["Enc"] ? $colorCSS : "text-danger";
                    $colorCSS = $connected ? "text-success" : $colorCSS;

                    //Sanitize the WiFi variables for the JS function
                    $wifi["KeyMgmt"] = isset($wifi["KeyMgmt"]) ? $wifi["KeyMgmt"] : "";
                    $wifi["Proto"] = isset($wifi["Proto"]) ? $wifi["Proto"] : "";
                    $wifi["Pairwise"] = isset($wifi["Pairwise"]) ? $wifi["Pairwise"] : "";
                    $wifi["Group"] = isset($wifi["Group"]) ? $wifi["Group"] : "";
                    $wifi["Enc"] = $wifi["Enc"] ? "true" : "false";
                    ?>
                    <tr>
                        <td><span class="<?php echo $colorCSS ?> "><?php echo $wifi["ESSID"]; echo $connected ? " (connected)" : "" ?></span></td>
                        <td><i class="fa <?php echo $goodSignal ? "fa-signal" : "fa-ban" ?> fa-lg <?php echo $colorCSS ?>"></i></td>


                        <td><i class="fa <?php echo $wifi["Enc"] == "true" ? "fa-lock" : "fa-unlock" ?> fa-lg <?php echo $colorCSS ?>"></i></td>

                        <td style="<?php echo !$connected ? "cursor: pointer;" : "" ?>" onclick="<?php echo !$connected ? "connectToWiFi('".$wifi["ESSID"]."', '".$wifi["KeyMgmt"]."', '".$wifi["Proto"]."', '".$wifi["Pairwise"]."', '".$wifi["Group"]."',".$wifi["Enc"].")" : "" ?>"><i class="fa <?php echo $connected ? "fa-check-square" : "fa-link" ?> fa-lg <?php echo $colorCSS ?>"></i></td>

                    </tr>
            </tbody>
        </table>
    </div>
<?php           }
}
else  { ?>
    <span class="text-danger">No WiFi networks found, please make sure your WiFi dongle is fully plugged in and re-scan.</span>
<?php } ?>

<?php
include('foot.php');


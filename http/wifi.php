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
            data: params,
            success: function(result) {
                if(result == 0){
                    //Reload the page to initiate WiFi scan and show connected networks
                    window.location.replace("/wifi.php");
                }
            }
        });

        bootbox.alert("Error connecting to WiFi network!", function() {});
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
$connectedWiFi = exec("iwgetid wlan0 --raw");
?>

    <div class="container">
        <center><h1>WiFi networks</h1></center>
        <br/><br/>
        <button class="btn btn-default" onclick="scanWiFi()">Scan for WiFi</button>
        <input type='hidden' name='wifiScan'>
        <br><br>
        <table id="stats" class="tablesorter table table-striped table-hover stats">
            <thead>
            <tr>
                <th>SSID</th>
                <th>Good Signal</th>
                <th>Secured</th>
                <th>Connect to WiFi</th>
            </tr>
            </thead>
            <tbody>

            <?php
            if(isset($wifiNetworks) && isset($wifiNetworks["WiFi"]) && sizeof($wifiNetworks["WiFi"]) > 0)
                foreach($wifiNetworks["WiFi"] as $wifi){
                    //If the current WiFi is connected, indicate it visually
                    $connected = $connectedWiFi == $wifi["ESSID"];
                    $connectedCSS = $connected ? "text-success" : "";
                    ?>
                    <tr>
                        <td><span class="<?php echo $connectedCSS ?> "><?php echo $wifi["ESSID"]; echo $connected ? " (connected)" : "" ?></span></td>
                        <td><i class="fa <?php $signal = explode('/', $wifi["Quality"]); echo $signal[0]/$signal[1] > WIFI_SIGNAL_THRESHOLD ? "fa-signal" : "fa-ban" ?> fa-lg <?php echo $connectedCSS ?>"></i></td>


                        <td><i class="fa <?php echo $wifi["Enc"] ? "fa-lock" : "fa-unlock" ?> fa-lg <?php echo $connectedCSS ?>"></i></td>

                        <td style="<?php echo !$connected ? "cursor: pointer;" : "" ?>" onclick="<?php echo !$connected ? "connectToWiFi('".$wifi["ESSID"]."', '".$wifi["KeyMgmt"]."', '".$wifi["Proto"]."', '".$wifi["Pairwise"]."', '".$wifi["Group"]."',".$wifi["Enc"].")" : "" ?>"><i class="fa <?php echo $connected ? "fa-check-square" : "fa-link" ?> fa-lg <?php echo $connectedCSS ?>"></i></td>

                    </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>

<?php
include('foot.php');


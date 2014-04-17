<?php

require_once('global.inc.php');
include('head.php');
include('menu.php');
?>

<script type="text/javascript" id="js">
    function post_to_url(path, params, method) {
        method = method || "post"; // Set method to post by default if not specified.

        // The rest of this code assumes you are not using a library.
        // It can be made less wordy if you use one.
        var form = document.createElement("form");
        form.setAttribute("method", method);
        form.setAttribute("action", path);

        for(var key in params) {
            if(params.hasOwnProperty(key)) {
                var hiddenField = document.createElement("input");
                hiddenField.setAttribute("type", "hidden");
                hiddenField.setAttribute("name", key);
                hiddenField.setAttribute("value", params[key]);

                form.appendChild(hiddenField);
            }
        }

        document.body.appendChild(form);
        form.submit();
    }


    function scanWiFi(){
        //Just reload the page to initiate WiFi scan
        window.location.replace("/wifi.php")
    }

    function connectToWiFi(wifiNameParam, keyMgmtParam, protocolParam, pairWiseParam, groupCiphersParam, encrypted){
        if(encrypted) {
            bootbox.prompt("Please enter the chosen WiFi network password", function(result) {
                if (result === null) {
                } else if(result.length > 0) {
                    post_to_url('/wifi.php', {connect: 'true', wifiName: wifiNameParam, password: result, keyMgmt: keyMgmtParam, protocol: protocolParam, pairWise: pairWiseParam, groupCiphers: groupCiphersParam});
                }
            });
        }
        else {
            post_to_url('/wifi.php', {connect: 'true', wifiName: wifiNameParam, keyMgmt: keyMgmtParam, protocol: protocolParam, pairWise: pairWiseParam, groupCiphers: groupCiphersParam});
        }
    }
</script>

<?php
    //Scan for WiFi on every page refresh
    //exec("iwlist wlan0 scan | iwlist-scan-parse.awk > " . WIFI_NETWORKS_FILE); //TODO: Re-enable on miner
    $wifiJson = file_get_contents(WIFI_NETWORKS_FILE);
    $wifiNetworks = json_decode($wifiJson, true);

    if (isset($_POST['connect'])) {
        exec('ESSID="'.$_POST['wifiName'].'" KEY_MGMT"'.$_POST['keyMgmt'].'" PROTO="'.$_POST['protocol'].'" PAIRWISE_CIPHERS="'.$_POST['pairWise'].'" GROUP_CIPHERS="'.$_POST['groupCiphers'].'" wifi-conf-create.sh');

        header('Location: /wifi.php');
        exit;
    }
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
                <th>Connect</th>
            </tr>
            </thead>
            <tbody>

            <?php
            if(isset($wifiNetworks) && isset($wifiNetworks["WiFi"]) && sizeof($wifiNetworks["WiFi"]) > 0)
                foreach($wifiNetworks["WiFi"] as $wifi){
                    $connected = $wifi["Chan"] == 1;
                    $connectedCSS = $connected ? "text-success" : "";
                    ?>
                    <tr>
                        <td><span class="<?php echo $connectedCSS ?> "><?php echo $wifi["ESSID"]; echo $connected ? " (connected)" : "" ?></span></td>
                        <td><i class="fa fa-signal fa-lg <?php echo $connectedCSS ?>"></i></td>


                        <td><i class="fa <?php echo $wifi["Enc"] ? "fa-lock" : "fa-unlock" ?> fa-lg <?php echo $connectedCSS ?>"></i></td>

                        <td style="<?php echo !$connected ? "cursor: pointer;" : "" ?>" onclick="<?php echo !$connected ? "connectToWiFi('".$wifi["ESSID"]."', '".$wifi["KeyMgmt"]."', '".$wifi["Proto"]."', '".$wifi["Pairwise"]."', '".$wifi["Group"]."',".$wifi["Enc"].")" : "" ?>"><i class="fa <?php echo $connected ? "fa-check-square" : "fa-link" ?> fa-lg <?php echo $connectedCSS ?>"></i></td>

                    </tr>
            <?php } ?>
<!--            <tr>

                <td><span class="text-success">Spond1 (connected)</span></td>
                <td><i class="fa fa-signal fa-lg text-success"></i></td>


                <td><i class="fa fa-lock fa-lg text-success"></i></td>

                <td><i class="fa fa-check-square fa-lg text-success"></i></td>

            </tr>

            <tr>

                <td>Spond2</td>
                <td><i class="fa fa-ban fa-lg text-danger"></i></td>


                <td><i class="fa fa-lock fa-lg text-info"></i></td>

                <td><i class="fa fa-link fa-lg text-info"></i></td>

            </tr>

            <tr>

                <td>Spond3</td>
                <td><i class="fa fa-signal fa-lg text-info"></i></td>


                <td><i class="fa fa-unlock fa-lg text-danger"></i></td>

                <td><i class="fa fa-link fa-lg text-info"></i></td>

            </tr>

            <tr>

                <td>Spond4</td>
                <td><i class="fa fa-signal fa-lg text-info"></i></td>


                <td><i class="fa fa-lock fa-lg text-info"></i></td>

                <td><i class="fa fa-link fa-lg text-info"></i></td>

            </tr>
-->
            </tbody>
        </table>
    </div>

<?php
include('foot.php');


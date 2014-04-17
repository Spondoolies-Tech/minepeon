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
    //exec("iwlist wlan0 scan | iwlist-scan-parse.awk > " . WIFI_NETWORKS_FILE);
    $wifiJson = file_get_contents(WIFI_NETWORKS_FILE);
    $wifiNetworks = json_decode($wifiJson, true);

    //Handle the connect command
    if (isset($_POST['connect'])) {
        //Preparation
        $descriptorspec = array(
            0 => array("pipe", "r"),  // stdin is a pipe that the child will read from
            1 => array("pipe", "w"),  // stdout is a pipe that the child will write to
            2 => array("file", "/tmp/wifi-conf-create.txt", "a") // stderr is a file to write to
        );

        //Create and open new process
        $cmd = 'ESSID="'.$_POST['wifiName'].'" KEY_MGMT"'.$_POST['keyMgmt'].'" PROTO="'.$_POST['protocol'].'" PAIRWISE_CIPHERS="'.$_POST['pairWise'].'" GROUP_CIPHERS="'.$_POST['groupCiphers'].'" wifi-conf-create.sh';
        $process = proc_open($cmd, $desc, $pipes);

        //Input password, if defined
        if(isset($_POST['password'])) {
            fwrite($pipes[0], $_POST['password']);
        }
        //Close input to start command
        fclose($pipes[0]);

        $result = stream_get_contents($pipes[1]);

        //Clean-up
        fclose($pipes[1]);
        fclose($pipes[2]);
        proc_close($process);

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
                    $connected = false;
                    //$connected = $wifi["Connected"]; //TODO: Enable once connected indicator is added
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


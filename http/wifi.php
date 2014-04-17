<?php

require_once('global.inc.php');
include('head.php');
include('menu.php');
?>

<?php
if (isset($_POST['wifiScan']))
    header('Location: /wifi.php');

    //Scan for WiFi on every page refresh
    //exec("iwlist wlan0 scan | iwlist-scan-parse.awk > " . WIFI_NETWORKS_FILE); //TODO: Re-enable on miner
    $wifiJson = file_get_contents(WIFI_NETWORKS_FILE);
    $wifiNetworks = json_decode($wifiJson, true);
?>

    <div class="container">
        <center><h1>WiFi networks</h1></center>
        <br/><br/>
        <form name="wifi" action="/wifi.php" method="post" class="form-horizontal">
        <button type="submit" class="btn btn-default">Scan for WiFi</button>
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
                    $connected = $wifi["Chan"] == 6;
                    $connectedCSS = $connected ? "text-success" : "";
                    ?>
                    <tr>
                        <td><span class="<?php echo $connectedCSS ?> "><?php echo $wifi["ESSID"]; echo $connected ? " (connected)" : "" ?></span></td>
                        <td><i class="fa fa-signal fa-lg <?php echo $connectedCSS ?>"></i></td>


                        <td><i class="fa <?php echo $wifi["Enc"] ? "fa-lock" : "fa-unlock" ?> fa-lg <?php echo $connectedCSS ?>"></i></td>

                        <td style="<?php echo !$connected ? "cursor: pointer;" : "" ?>"><i class="fa <?php echo $connected ? "fa-check-square" : "fa-link" ?> fa-lg <?php echo $connectedCSS ?>"></i></td>

                    </tr>
            <?php } ?>
        </form>
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


<?php

require_once('global.inc.php');
include('head.php');
include('menu.php');
//Update watchdog monitored file (to prevent reboots)
//file_put_contents('/var/run/dont_reboot', "php_active_fw");
//
if (isset($_FILES["image"]["tmp_name"])) {
	rename($_FILES['image']['tmp_name'], '/tmp/image.tar');
	$file_upgrade = true;
}else{
	$file_upgrade = false;
}
?>

    <script type="text/javascript">
        function upgradeFirmware(src)
        {
            bootbox.confirm("Press Ok to continue SW upgrade. Your pool settings will not be affected. Note that power interruption during the upgrade may brick your unit and require restore procedure with microSD card.", function(result) {
                if (!result) return;
                $('.miner-action').addClass('disabled');
                var o = $('#upgrade_output');

                var xhr = new XMLHttpRequest();
                if(src == "file")
                    xhr.open("GET", "upgrade.php?source=file&targetVersion=from_file", true);
                else if($('#settings_view_name').text()=="Manual")
                    xhr.open("GET", "upgrade.php", true);
                else
                    xhr.open("GET", "upgrade.php?targetVersion=" + $('#selectedVersion').val(), true);

                xhr.onreadystatechange = function(){
                    if(xhr.readyState > 2){
                        if(xhr.responseText)
                            o.html(xhr.responseText);
                        else if(xhr.response)
                            o.html(xhr.response);
                    }

                    if(xhr.readyState == 4){
                        $('.miner-action').removeClass('disabled');

                        if(o.text().toLowerCase().indexOf("please reboot") >= 0){ // return code, trailing number characters of response
                            $('#reboot').removeClass('hidden');
                        }
                    }
                };

                xhr.send();
            });

        }

        var oneTimeWarning = true;
        function toggleCustomVersionSelection(){
            var settingsView = $('#settings_view_name');

            if (oneTimeWarning) {
                bootbox.confirm("Choosing an outdated or test version might cause a lower perfromance for your miner or cause it to restart unexpectedly.<br/><br/>Do you want to continue?",
                    function (confirm) {
                        if (confirm) {
                            oneTimeWarning = false;
                            toggleCustomVersionSelection();
                        }
                    });
                return false;
            }

            $('.view-alternative').toggle();
            settingsView.text(settingsView.text()=="Manual"?"Automatic":"Manual");

            return false;
        }
    </script>


    <div class="container">
        <!-- <p class="alert"><b>WARNING:</b> Power interruption during the upgrade may brick your unit  and will require microSD restore procedure.</p> -->
        <h2>Settings</h2>

        <fieldset>
            <legend></legend>

            <label for="currentVersion">Current firmware version:</label>
            <b><?php echo(file_get_contents(CURRENT_VERSION_FILE)) ?></b>
            <br/>

            <div class="basic view-alternative">
                <label for="latestVersion">Latest available firmware version:</label>
                <b><?php echo(file_get_contents(LATEST_VERSION_FILE)) ?></b>
            </div>

            <div class="basic view-alternative" style="display:none">
                <label for="selectedVersion">Available versions:</label>
                <select class="form-control" id="selectedVersion">
                    <?php
                    $fwVersionsJson = file_get_contents(FIRMWARE_AVAILABLE_VERSIONS.$model_id);
                    $fwVersions = json_decode($fwVersionsJson, true);

                    $selected = " selected ";
                    foreach($fwVersions as $fwVersion) {
                        $testVersionLabel = $fwVersion["isTestVersion"] ? " - TEST VERSION" : "";
                        echo('<option value="'. $fwVersion["firmwareVersion"] . '"' . $selected . '>' . $fwVersion["firmwareVersion"] . $testVersionLabel . '</option>');
                        $selected = "";
                    }
                    ?>
                </select>
            </div>

            <br/>
            <div class="buttons">
                <button class="btn btn-default" onclick="return upgradeFirmware()">Upgrade</button>

                <button class="btn btn-default col-offset-1" onclick="return toggleCustomVersionSelection()"><span id="settings_view_name">Manual</span> Selection</button>

                <button class="btn btn-default col-offset-1" onclick="$('.manual_upgrade').show(800); return false;">Upload FW file</button>
		<div class="manual_upgrade" style="display:none">
		<br/><hr/>

		<form name="backup" action="" method="post" enctype="multipart/form-data" class="form-horizontal">
			<label for="image_upload">Upload Image: <input type="file" name="image" ></label>
			<input type="submit" value="Upgrade from file" class="btn btn-default" />
		</form>
		</div>
            </div>

            <br><br>
            <pre>
                <div id="upgrade_output"></div>
                <span id="upgrade_scroller"></span>
            </pre>

            <div class="buttons">
                <br><br>
                <a class="btn btn-default hidden" id="reboot" name="reboot" href='/reboot.php'>Reboot</a>
            </div>
        </fieldset>
    </div>
<?php
include('foot.php');
?>
<script type="text/javascript">
	<?php if($file_upgrade){ ?>
		upgradeFirmware("file");
	<?php } ?>
</script>

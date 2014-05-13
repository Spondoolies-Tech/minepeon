<?php

require_once('global.inc.php');
include('head.php');
include('menu.php');
?>
    <script type="text/javascript">
        function upgradeFirmware()
        {
            bootbox.confirm("Press Ok to continue SW upgrade. Your pool settings will not be affected. Note that power interruption during the upgrade may brick your unit and require restore procedure with microSD card.", function(result) {
                if (!result) return;
                $('.miner-action').addClass('disabled');

                var o = $('#upgrade_output');

                var xhr = new XMLHttpRequest();
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

        advanced_warning = false;
        function toggleCustomVersionSelection(force){
            var advanced_warning;
            var settingsView = $('#settings_view_name');

            if (!force && !advanced_warning && settingsView.text() == "Advanced") {
                advanced_warning = true;
                bootbox.confirm("Choosing an outdated or test version might cause a lower perfromance for your miner or cause it to restart unexpectedly.<br/><br/>Do you want to continue?",
                    function (confirm) {
                        if (confirm) toggleCustomVersionSelection(true);
                        else advanced_warning = false;
                    });
                return false;
            }

            $('.view-alternative').toggle();
            settingsView.text(settingsView.text()=="Advanced"?"Basic":"Advanced");
            $('#selectedVersion').val("");

            return false;
        }
    </script>


    <div class="container">
        <!-- <p class="alert"><b>WARNING:</b> Power interruption during the upgrade may brick your unit  and will require microSD restore procedure.</p> -->
        <h2>Settings</h2>

        <fieldset>
            <legend></legend>

            <label for="selectedVersion">Current firmware version:</label>
            <b><?php echo(file_get_contents(CURRENT_VERSION_FILE)) ?></b>
            <br/>

            <div class="basic view-alternative">
                <b><?php $version = file_get_contents(LATEST_VERSION_FILE);
                    if ($version) {
                        ?>
                        <label for="selectedVersion">Latest available firmware version:</label>
                        <?php
                        echo($version);
                    }?></b>
            </div>

            <div class="basic view-alternative" style="display:none">
                <label for="selectedVersion">Available versions:</label>
                <select class="form-control" id="selectedVersion">
                    <option value="">Please select a target firmware version</option>
                    <?php
                    $fwVersionsJson = file_get_contents(FIRMWARE_AVAILABLE_VERSIONS);
                    $fwVersions = json_decode($fwVersionsJson, true);

                    foreach($fwVersions as $fwVersion) {
                        $testVersionLabel = $fwVersion["isTestVersion"] ? " - TEST VERSION" : "";
                        echo('<option value="'. $fwVersion["firmwareVersion"] . '">' . $fwVersion["firmwareVersion"] . $testVersionLabel . '</option>');
                    }
                    ?>
                </select>
            </div>

            <br/>
            <div class="buttons">
                <button class="btn btn-default" onclick="return upgradeFirmware()">Upgrade Now</button>
                <button class="btn btn-default col-offset-2" onclick="return toggleCustomVersionSelection()"><span id="settings_view_name">Advanced</span> firmware selection</button>
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


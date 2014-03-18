<?php

require_once('global.inc.php');
include('head.php');
include('menu.php');
?>
    <script type="text/javascript">
        function upgradeFirmware()
        {
            bootbox.confirm("Are you sure you want to upgrade?", function(result) {
                if (!result) return;
                $('.miner-action').addClass('disabled');

                var o = $('#upgrade_output');
                //var s = $('#upgrade_scroller');

                var xhr = new XMLHttpRequest();
                xhr.open("GET", "upgrade.php", true);

                xhr.onreadystatechange = function(){
                    if(xhr.readyState > 2){
                        if(xhr.responseText)
                            o.html(xhr.responseText);
                        else if(xhr.response)
                            o.html(xhr.response);
                    }

                    if(xhr.readyState == 4){
                        $('.miner-action').removeClass('disabled');

                        if(o.text().search("Reboot your miner") >= 0){ // return code, trailing number characters of response
                            $('#reboot').removeClass('hidden');
                        }
                    }
                };

                xhr.send();
            });

        }

    </script>

    <div>

        <div class="container">
            <p class="alert"><b>WARNING:</b> Power interruption during the upgrade may brick your unit  and will require microSD restore procedure.</p>
            <h1>Firmware upgrade</h1>
            <p class="help-block">Your current firmware version is <b><? echo(file_get_contents("/etc/fw_ver")) ?></b>.</p>
            <br>
            <a name="upgrade" class="btn btn-default miner-action" onclick="upgradeFirmware()">Upgrade Now</a>
            <br><br>
            <pre>
                <div id="upgrade_output"></div>
                <span id="upgrade_scroller"></span>
            </pre>
        </div>
    </div>

    <center>
        <br><br>
        <a class="btn btn-default hidden" id="reboot" name="reboot" href='/reboot.php'>Reboot</a>
    </center>
<?php
include('foot.php');


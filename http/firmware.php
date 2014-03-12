<?php

include('head.php');
include('menu.php');
?>
    <script type="text/javascript">
        function upgradeFirmware()
        {
            bootbox.confirm("Are you sure you want to upgrade?", function(result) {
                if (!result) return;

                var o = $('#upgrade_output');
                var s = $('#upgrade_scroller');

                var xhr = new XMLHttpRequest();
                xhr.open("GET", "upgrade.php", true);
                xhr.onreadystatechange = function(){
                    if(xhr.readyState > 2){
                        if(xhr.responseText)
                            o.html(xhr.responseText);
                        else if(xhr.response)
                            s.html(xhr.response);
                        s.scrollIntoView();
                    }
                }
                xhr.send();
            });

        }

    </script>

    <div>

        <div class="container">
            <p class="alert"><b>WARNING:</b>Power interruption during the upgrade may brick your unit!</p>
            <h1>Firmware upgrade</h1>
            <button name="upgrade" class="btn btn-default" onclick="upgradeFirmware()">Upgrade Now</button>
            <br><br>
            <pre>
                <div id="upgrade_output"></div>
                <span id="upgrade_scroller"></span>
            </pre>
        </div>
    </div>

<?php
include('foot.php');


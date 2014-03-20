<?php
//Remove the stats file
exec('/usr/local/bin/spond-manager stop > /dev/null 2>&1');
sleep(5);
exec('rm /etc/mg_nvm.bin > /dev/null 2>&1 &');
exec('/usr/local/bin/spond-manager start > /dev/null 2>&1');

//Return to settings page
header('Location: /settings.php');
<?php
require_once('miner.inc.php');

//Remove the custom cgminer settings file
unlink('/etc/cgminer.conf');

//Write the default settings
file_put_contents("/etc/cgminer.conf", '{"api-listen":true,"api-allow":"W:127.0.0.1","pools":[{"url":"stratum.btcguild.com:3333","user":"user","pass":"pass"}]}');

//Restart CGMiner (by forcing service reload)
$ret = miner_restart(false);

//Reload the settings page
header('Location: /settings.php');

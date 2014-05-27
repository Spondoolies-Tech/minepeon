<?php
require_once('miner.inc.php');

//Remove the custom cgminer settings file
unlink('/etc/cgminer.conf');

//Restart CGMiner (by forcing service reload)
$ret = miner_restart(false);

//Reboot the machine
header('Location: /settings.php');

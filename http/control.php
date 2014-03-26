<?php
require_once('global.inc.php');

header('Expires: Sat 26 Jul 1997 05:00:00 GMT');
header('Last-Modified: '.gmdate('D, d M Y H:i:i').' GMT');
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Cache-control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');


switch($_GET['op']){
case 'mining_restart':
	require_once('miner.inc.php');
	$nice = isset($_GET['nice']);
	//$ret = miner_service('restart');
	$ret = miner_restart($nice);
	break;
case 'indicate':
	// flash LED's 
	require_once('leds.inc.php');
	$times = $_GET['times'];
	if(!is_numeric($times)) $times = 3;
	led_flash(YELLOW_LED, $times, .5);
	break;
default:
	$ret = 'Error: Unknown operation';
}
 echo $ret;

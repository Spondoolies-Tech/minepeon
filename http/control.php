<?php
header('Expires: Sat 26 Jul 1997 05:00:00 GMT');
header('Last-Modified: '.gmdate('D, d M Y H:i:i').' GMT');
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Cache-control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');

switch($_GET['op']){
case 'mining_restart':
	require_once('miner.inc.php');
	$ret = miner_service('restart');
	break;
default:
	$ret = 'Error: Unknown operation';
}
 echo $ret;

<?php
require_once('constants.php');
//header("Content-length: ");
//header("Content-type: text/javascript");
header("Content-type: text/text");
header("Transfer-Encoding: chunked");
//no cache! copied from http://php.netmanual/en/function.header.php
header('Expires: Sat 26 Jul 1997 05:00:00 GMT');
header('Last-Modified: '.gmdate('D, d M Y H:i:i').' GMT');
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Cache-control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');

$p = popen(FIRMWARE_UPGRADE_SCRIPT." ".FIRMWARE_UPGRADE_PARAMS, 'r');
while($line = fgets($p)){
	echo $line;
	//echo "document.write(\"".trim($line)."\");";
	ob_flush();
	flush();
}
$status = pclose($p);
if($status){
require_once("errors.php");
if($status < 100) $type = "cURL";
else $type = "Spondoolies Upgrade";
echo "A ".$type." error was encountered. The upgrade processes returned: ".$status." (".$errors[$status].")";
}
else echo "Firmware uprgade succesful";
?>

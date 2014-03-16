<?php
require_once('constants.php');
header("Content-type: text/text");
header("Transfer-Encoding: chunked");

//no cache! copied from http://php.netmanual/en/function.header.php
header('Expires: Sat 26 Jul 1997 05:00:00 GMT');
header('Last-Modified: '.gmdate('D, d M Y H:i:i').' GMT');
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Cache-control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');

echo "Beginning upgrade....\n";
ob_flush();
flush();

//Start the  process
passthru(FIRMWARE_UPGRADE_SCRIPT . " --url " . FIRMWARE_UPGRADE_URL, $result);

echo "result:" . $result;

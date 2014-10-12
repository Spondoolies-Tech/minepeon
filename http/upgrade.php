<?php
require_once('inc/global.inc.php');
header("Content-type: text/text");
header("Transfer-Encoding: chunked");

//no cache! copied from http://php.netmanual/en/function.header.php
header('Expires: Sat 26 Jul 1997 05:00:00 GMT');
header('Last-Modified: '.gmdate('D, d M Y H:i:i').' GMT');
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Cache-control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');

echo "Beginning upgrade ".$_GET["targetVersion"].". Please wait, this can take up to 1 minute.\n";
ob_flush();
flush();
//Update watchdog monitored file (to prevent reboots)
file_put_contents('/var/run/dont_reboot', "minepeon php_active_upgrade");

//Start the  process
//If not version set, download the latest one available
if(isset($_GET['source']) && $_GET['source'] == "file"){
    echo "Loading file...";
	passthru(FIRMWARE_FILE_UPGRADE_SCRIPT." --file=/tmp/image.tar", $result);
    echo "Loading file done.";
}
elseif(!isset($_GET["targetVersion"]))
    passthru(FIRMWARE_UPGRADE_SCRIPT . " --url " . FIRMWARE_UPGRADE_URL, $result);
//Otherwise use the target version
else
    passthru(FIRMWARE_UPGRADE_SCRIPT . " --url " . FIRMWARE_DOWNLOAD_VERSION . " --ver " . $_GET["targetVersion"], $result);

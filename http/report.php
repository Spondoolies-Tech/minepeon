<?php

include('inc/global.inc.php');

$fileDate = date("YmdHis");

// got part of this from phpmyadmin.
header('Content-Type: application/x-gzip');
$content_disp = ( preg_match('/MSIE ([0-9].[0-9]{1,2})/', $_SERVER['HTTP_USER_AGENT']) == 'IE') ? 'inline' : 'attachment';
header('Content-Disposition: ' . $content_disp . '; filename="' . $fileDate . '_SpondMiner.tar.gz"');
header('Pragma: no-cache');
header('Expires: 0');

// create the gzipped tarfile.
// tar on miner does not support "z" flag
passthru( "tar c /mnt/mmc-config /tmp | gzip ");

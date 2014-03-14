<?php

include('settings.inc.php');

$fileDate = date("YmdHis");

// got part of this from phpmyadmin.
header('Content-Type: application/x-gzip');
$content_disp = ( preg_match('/MSIE ([0-9].[0-9]{1,2})/', $_SERVER['HTTP_USER_AGENT']) == 'IE') ? 'inline' : 'attachment';
header('Content-Disposition: ' . $content_disp . '; filename="' . $fileDate . '_SP10_Dawson.tar.gz"');
header('Pragma: no-cache');
header('Expires: 0');

// create the gzipped tarfile.
passthru( "tar cz /etc/minepeon.conf /etc/cgminer.conf /etc/ui.pwd /mnt/mmc-config/rrd/*.rrd");
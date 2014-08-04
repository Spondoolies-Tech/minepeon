<?php
$dir = '/mnt/config/etc/';
$reboot = true;
//Remove all the custom settings files

$except = explode(',', $_REQUEST['except']);
foreach($except as $k=>$v){
	copy($dir.$v, '/tmp/'.basename($v));
}


exec('rm -rf /mnt/config/rrd/*');
exec('rm -rf /mnt/config/log/*');
exec('rm -rf /mnt/config/etc/*');

foreach($except as $k=>$v){
	@mkdir($dir.'/'.str_replace(basename($v), '', $v), 0777, true); // crate subdirectories, if necessary
	copy('/tmp/'.basename($v), $dir.$v);
}

//Reboot the machine
header('Location: /reboot.php');
die;


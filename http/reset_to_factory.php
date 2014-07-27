<?php
$dir = '/mnt/config/etc/';
//Remove all the custom settings files

$except = explode(',', $_REQUEST['except']);
foreach($except as $k=>$v){ $except[$k] = $dir.$v; }

$files = array_filter(explode(',', $_REQUEST['files']));
if($files || !empty($files)){
	foreach($files as $k=>$v){ $files[$k] = $dir.$v; }
}else{
	$files = glob($dir.'*'); // get all file names
}
foreach($files as $file){ // iterate files
    if(is_file($file) && !in_array($file, $except)){
	unlink($file); // delete file
    }
}

//Reboot the machine
header('Location: /reboot.php');

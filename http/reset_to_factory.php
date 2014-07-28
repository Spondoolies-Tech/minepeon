<?php
$dir = '/mnt/config/etc/';
$reboot = true;
//Remove all the custom settings files

$except = explode(',', $_REQUEST['except']);
foreach($except as $k=>$v){ $except[$k] = $dir.$v; }

$files = array_filter(explode(',', $_REQUEST['files']));
if($files && !empty($files)){
	foreach($files as $k=>$v){ $files[$k] = $dir.$v; }
	$reboot = false;
}else{
	$files = glob($dir.'*'); // get all file names
}

$del = array();
foreach($files as $file){ // iterate files
    if(is_file($file) && !in_array($file, $except)){
	unlink($file); // delete file
	$del[] = $file;
    }
}

//Reboot the machine
if($reboot){
	header('Location: /reboot.php');
	die;
}

?>
<?php include('head.php'); include('menu.php'); ?>
<div class="container notice">
<h3>The following file<?php echo (count($del) > 1)? "s were" : " was"?> deleted.</h3>
<ul><li><?php echo implode('</li><li>', $del)?></li></ul>
</div>
<div class="container">
	<div class="row"><a href="/reboot.php">Reboot</a></div>
	<div class="row"><a href="/">Homepage</a></div>
</div>


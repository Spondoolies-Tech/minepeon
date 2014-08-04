<?php

switch($_GET['op']){
	case 'del': // this is all we know how to do :(
	default:
		$dir = '';
		if($_GET['dir']){
			switch($_GET['dir']){
				case 'config':
					$dir = '/mnt/config/etc/';
			}
		}
		$del = array();
		$files = explode(',', $_REQUEST['files']);
		foreach($files as $file){ // iterate files
			unlink($dir.$file); // delete file
			$del[] = $file;
		}
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


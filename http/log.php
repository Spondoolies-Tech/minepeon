<?php
require_once('global.inc.php');
require('ansi.inc.php');
include('head.php');
include('menu.php');
$log_file = 'messages';
$log_id = $_GET['log'];
if(!is_null($log_id) && $log_id != '') $log_file .= '.'.$log_id;
?>
<h3>System events</h3>

<a class="clearlog" href='/control.php?op=clear_log'>Clear Events</a>

<pre style="padding:20px;font-size:85%">
<div style="padding:10px;color:white;background:#282828">
<?php
exec('cat /tmp/mg_event_log',$d);
echo $ansi->convert(implode("\n",$d));
$d="";
?>
</pre>
<h3>Log</h3>
<a href="?">Current</a>
<?php 
$h = opendir('/mnt/config/log/');
$logs = array();
while( ($file = readdir($h)) !== false){
	$matches = array();
	preg_match('/messages.(\d+)/', $file, $matches);
	if(!empty($matches)){
		$logs[] = $matches[1];
	}
}
sort($logs);
//$logs = array_slice($logs, 0, 21);
foreach($logs as $l){
	$style = '';
	if($l == $log_id) $style = 'color:red;font-weight:bold;';
	echo ' <a href="?log='.$l.'" style="'.$style.'">'.$l.'</a> ';
}
?>
<pre style="padding:20px;font-size:85%">
<div style="padding:10px;color:white;background:#282828">
    
    <?php
    exec('cat /mnt/config/log/'.$log_file, $d);
    echo $ansi->convert(str_replace('^[', chr(27), implode("\n",$d)));
    ?>
</pre>

<?php include('foot.php'); ?>
    <script>
	// same function is in spond.js using class 'ajax'. put here so customers will not have to clear cache?
	$(function(){
	$('a.clearlog').click(function(e){
	    $.get($(this).attr('href'), function(){
		document.location.reload();
	    });
	    e.preventDefault();
	    return false;
	});
	});

    </script>

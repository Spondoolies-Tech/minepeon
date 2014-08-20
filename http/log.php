<?php
require_once('global.inc.php');
require('ansi.inc.php');
include('head.php');
include('menu.php');
?>
<h3>System events</h3>
<pre style="padding:20px;font-size:85%">
<div style="padding:10px;color:white;background:#282828">
<?php
exec('cat /tmp/mg_event_log',$d);
echo $ansi->convert(implode("\n",$d));
$d="";
?>
</pre>
<a class="btn btn-default clearlog" href='/control.php?op=clear_log'>Clear Events</a>

<h3>Log</h3>
<pre style="padding:20px;font-size:85%">
<div style="padding:10px;color:white;background:#282828">
    <?php
    exec('cat /mnt/config/log/messages.0',$d);
    echo $ansi->convert(str_replace('^[', chr(27), implode("\n",$d)));
    $d="";
    exec('cat /mnt/config/log/messages',$d);
    echo $ansi->convert(str_replace('^[', chr(27), implode("\n",$d)));
    $d="";
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

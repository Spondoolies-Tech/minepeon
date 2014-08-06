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
exec('cat /tmp/mg_event',$d);
echo $ansi->convert(implode("\n",$d));
$d="";
?>
</pre>

<h3>Log</h3>
<pre style="padding:20px;font-size:85%">
<div style="padding:10px;color:white;background:#282828">
    <?php
    exec('cat /mnt/config/log/messages.2',$d);
    echo $ansi->convert(str_replace('^[', chr(27), implode("\n",$d)));
    $d="";
    exec('cat /mnt/config/log/messages.1',$d);
    echo $ansi->convert(str_replace('^[', chr(27), implode("\n",$d)));
    $d="";
    exec('cat /mnt/config/log/messages.0',$d);
    echo $ansi->convert(str_replace('^[', chr(27), implode("\n",$d)));
    $d="";
    ?>
</pre>

<?php include('foot.php');

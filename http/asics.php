<?php
require_once('global.inc.php');
require('ansi.inc.php');
include('head.php');
include('menu.php');
?>
<h3>Asic stats</h3>
<pre style="padding:20px;font-size:85%">
<div style="padding:10px;color:white;background:#282828">
<?php 
exec('cat /var/log/asics', $details);
echo $ansi->convert(implode("\n",$details));
echo "";
echo "";
exec('cat /etc/fet', $details2);
echo $ansi->convert(implode("\n",$details2));
?>
</div>
</pre>
<?php include('foot.php');

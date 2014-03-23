<?php
require('ansi.inc.php');
include('head.php');
include('menu.php');
?>
<h3>Hardware stats</h3>
<pre style="padding:20px;font-size:85%">
<div style="padding:10px;color:white;background:#282828">
<?php 
exec('cat /var/log/asics', $details);
echo $ansi->convert(implode("\n",$details)); 
?>
</div>
</pre>
<?php include('foot.php');

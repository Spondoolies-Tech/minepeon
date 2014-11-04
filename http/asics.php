<?php
require_once('inc/global.inc.php');
require('inc/ansi.inc.php');
include('head.php');
include('menu.php');
?>
<h3 class="asics">Asic stats</h3>

<pre style="padding:20px;font-size:85%">
<div style="padding:10px;color:white;background:#282828">
<?php 
exec('cat /var/log/asics', $details);
echo $ansi->convert(implode("\n",$details));
echo "";
echo $ansi->convert(implode("\n",$details2));
?>
</div>
</pre>

<?php include('foot.php'); ?>


<?php
include('inc/global.inc.php');
include('head.php');
include('menu.php');

$file_content = file_get_contents("mg_etc_help");
$hardware_tag = file_get_contents("/tmp/hwtag");
?>
<div class="container" >
    <h1>Readme</h1> 
    <div class="readme">
        <pre>
<?php echo $file_content; ?>
        </pre>
    </div>
</div>
<div class="container" >
    <h2>Hardware TAG</h2> 
    <div class="hardware serials">
        <pre>
<?php echo $hardware_tag; ?>
        </pre>
    </div>
</div>
<?php
include('foot.php'); 
?>

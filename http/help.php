<?php
include('inc/global.inc.php');



include('head.php');
include('menu.php');

$file_content = file_get_contents("mg_etc_help");
?>
<div class="container" >
    <h1>Readme</h1> 
    <div class="readme">
        <pre>
<?php echo $file_content; ?>
        </pre>
    </div>
</div>
<?php
include('foot.php');

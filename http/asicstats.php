<?php
include('head.php');
include('menu.php');
?>

<div class="container">
	<h1>ASIC Stats</h1>
    <div id="stats" name="stats">

    </div>
</div>
<?php
include('foot.php');
?>

<script src="js/ansi_up.js" type="text/javascript"></script>
<script type="text/javascript">
    var txt  = ansi_up.escape_for_html("<?php echo "" . file_get_contents("/var/run/asics"); ?>");

    var html = ansi_up.ansi_to_html(txt);

    var cdiv = document.getElementById("stats");

    cdiv.innerHTML = html;

</script>

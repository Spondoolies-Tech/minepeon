<?php
/**
 * show the user agreement
 * If the user has not yet agred, show a button to allow him to accept.
 */

require_once('global.inc.php');
$agreement = file_get_contents('inc/terms_and_conditions.txt');

require_once('head.php');
?>
<div class="text-center container">
<div class="text-left text-info">
<?=$agreement?>
</div>
<?php if(!array_key_exists('accept', $settings) || ! intval(time($settings['accept'])) ){ ?>
<a id="agree" onclick="agree()" class="btn btn-info">Yes, I agree to the terms and conditions.</a>
<script type="text/javascript">
function agree(){
// $('#agree').click(function(){
	$(this).addClass('disabled');
	$.post('settings.php', {agree:true}, function(){
		document.location.reload();
		});
	return false;
	} // );
</script>
<?php } ?>
</div>

<?php include('foot.php'); ?>


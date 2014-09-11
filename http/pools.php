<?php
require_once('global.inc.php');

// set the number of extra empty rows for adding pools
$extraPools = 1;

// Read miner config file
$data = json_decode(file_get_contents("/etc/cgminer.conf", true), true);

include('head.php');
include('menu.php');
?>

<div class="container">
	<h1>Pools</h1>
	<p>Miner will use the following pool (the order of pools defines priority):</p>
	<form id="formpools">
		<input type="hidden" name="saving" value="1">
		<?php
		// List populated pools
		$countOfPools = count($data['pools']);
		for ($i = 0; $i < $countOfPools; $i++) {
			?>
			<div class="form-group row">
				<div class="col-lg-5">
					<label for="URL<?php echo $i; ?>"><span class="label label-success">Enabled</span> URL</label>
					<input type="url" class="form-control" value="<?php echo $data['pools'][$i]['url']; ?>" name="URL<?php echo $i; ?>" id="URL<?php echo $i; ?>">
				</div>
				<div class="col-lg-5">
					<label for="USER<?php echo $i; ?>">Username (%i for ip, %h for hostname)</label>
					<input type="text" class="form-control" value="<?php echo $data['pools'][$i]['user']; ?>" name="USER<?php echo $i; ?>" id="USER<?php echo $i; ?>">
				</div>
				<div class="col-lg-2">
					<label for="PASS<?php echo $i; ?>">Password <small class="text-muted">(optional)</small></label>
					<input type="text" class="form-control" value="<?php echo $data['pools'][$i]['pass']; ?>" name="PASS<?php echo $i; ?>" id="PASS<?php echo $i; ?>">
				</div>
			</div>
			<?php
		}

    // Extra empty rows to accomodate adding more pools
		for ($i = $countOfPools; $i < $countOfPools+$extraPools; $i++) {
			?>

			<div class="form-group row">
				<div class="col-lg-5">
					<label for="URL<?php echo $i; ?>"><span class="label label-info">New</span> URL</label>
					<input type="url" class="form-control" name="URL<?php echo $i; ?>" id="URL<?php echo $i; ?>">
				</div>
				<div class="col-lg-5">
					<label for="USER<?php echo $i; ?>">Username</label>
					<input type="text" class="form-control" name="USER<?php echo $i; ?>" id="USER<?php echo $i; ?>">
				</div>
				<div class="col-lg-2">
					<label for="PASS<?php echo $i; ?>">Password <small class="text-muted">(optional)</small></label>
					<input type="text" class="form-control" name="PASS<?php echo $i; ?>" id="PASS<?php echo $i; ?>">
				</div>
			</div>
			<?php
		}
		?>
		<p>After saving, the miner will restart with the new configuration. This takes about 10 seconds.</p>
		<p><button type="button" class="btn btn-default" value="" id="save">Submit</button></p>
		<p class="save-msg"></p>
	</form>

    <!-- ######################## Cgminer.conf -->
    <form name="cgconf" action="/settings.php" method="post" class="form-horizontal">
        <fieldset>
            <legend>CGMiner Conf</legend>
            <div class="form-group">
                <label for="userTimezone" class="control-label"></label>
                <div class="col-lg-9" style="width:100%">
                    <?php echo json2html(file_get_contents('/etc/cgminer.conf', true)); ?>
                    <p class="help-block">To edit this file, please use SSH to log in to your miner, and then use vi /etc/cgminer.conf </p>
                </div>
            </div>
        </fieldset>
    </form>
    <!-- ######################## -->


</div>
<?php
include('foot.php');
?>

<script type="text/javascript">
$(document).ready(function() {
	if(window.location.search=="?sr"){
		$('.save-msg').addClass('alert-success alert').text("Pool data successfully saved and miner restarted.");
	}
	else if(window.location.search=="?s-"){
		$('.save-msg').addClass('alert-warning alert').text('Pool data successfully saved but failed to restart miner, please try again in 1 minute.');
	}

	$('#save').click( function() {

		console.log("Saving pool data");
		$('.save-msg').addClass('alert alert-info').text('Saving pool data');

		$.ajax({
			url: 'f_pools_save.php',
			type: 'post',
			dataType: 'json',
			data: $('#formpools').serialize(),
			success: function(data) {
				console.log("Debug: "+JSON.stringify(data.debug));

				if(data.success){
					$('.save-msg').text('Pool data successfully saved.');
					console.log("Pool data saved");
					console.log("Settings: "+data.written+" bytes");

					console.log("Restarting miner");
					var restarted=false;

					$.get('f_miner.php?command=restart', function(d) {
						console.log("Debug: "+JSON.stringify(d));
						if(d.success){
							restarted=true;
						}
					})
					.always(function() { 
						console.log("Reloading page");
						if(restarted){
							window.location="?sr";
						}
						else{
							window.location="?s-";
						}
					});
				}
				else{
					$('.save-msg').addClass('alert alert-danger').text('Could not save pool data.');
				}
			}
		});
	});
});
</script>

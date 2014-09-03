<?php
require_once('global.inc.php');
require('ansi.inc.php');
include('head.php');
include('menu.php');

$controls = ($model_id == 'SP30');


if(isset($_POST['asic'])){
	$asics = array();
	for($i = 0; $i < 30; $i++){
		if(isset($_POST['asic'][$i]))  $asics[$i] = $_POST['asic'][$i];
		else $asics[$i] = 1; // 1 = disabled, if checkbox was not checked
	}
	foreach($asics as $k=>$v) $asics[$k] = $k.':'.$v;
	$asics = array_chunk($asics, 3);
	file_put_contents(MG_DISABLED_ASICS, implode("\n", array_map(function($row){return implode(' ', $row);}, $asics)));
}

if($controls) $asics = array_map(function($row){return explode(" ", trim($row));}, file(MG_DISABLED_ASICS) );
?>
	<h3 class="asics">Asic stats<?php if($controls){ ?><button class="asics_control opener">ASICS Control Panel</button><?php } ?></h3>

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
<div class="hidden">
<div class="asics_control container">
	<?php if($controls) include('widgets/asics_control.html'); ?>
</div>
</div>
<?php include('foot.php'); ?>
<script type="text/javascript">
$('.asics_control.opener').click(function(){bootbox.dialog({
		message:$('.asics_control.container').clone().html(),
		buttons:{
			'Cancel': function(){},
				'Apply': function(){
				var form = $('.modal-content .asics_control.controller').find('input').serialize();
				console.log($(this), form);
				$.post("", form, function(data){
					send_command("mining_restart");
				});
			}
		}
		});
	});
$('body').on('change', '.asic input', function(){
	console.log(this, $(this).parents('.asic'));
	$(this).parents('.asic').removeClass('enabled disabled').addClass($(this).is(":checked")?"enabled":"disabled");
});
</script>

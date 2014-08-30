<?php
$is_blinking = file_exists("/tmp/blink_led");
$blink_text = $is_blinking ? "Stop Blinking LED" : "Blink LED"; 
?>
<span class="blinker wrapper ">
	<a class="btn btn-default  led_blinker" onclick="toggle_blink(this)"><?php echo $blink_text; ?></a>
<input type="hidden" id="blink_timer" value="10" size="2" />
</span>

<script type="text/javascript">
blink_state="<?php echo $is_blinking ? "on" : "off";?>";
function toggle_blink(e){
	if(blink_state == "on"){
		blink("end_blink_led");
		$(e).text("Blink LED");
		blink_state="off";
	}else{
		blink("blink_led");
		$(e).text("Stop Blinking LED");
		blink_state="on";
	}
}
</script>



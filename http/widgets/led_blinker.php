<span class="blinker wrapper ">
<a class="btn btn-default  led_blinker" onclick="toggle_blink(this)">Blink LED</a>
<input type="hidden" id="blink_timer" value="10" size="2" />
</span>

<script type="text/javascript">
//<img src="img/clock.png" onclick="setBlinkTime(this)" alt="Timer" title="Set Blink Period" width="26" height="26"/>
blink_state="off";
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
function setBlinkTime(e){
	$(e).hide();
	$('.led_blinker', $(e).parent('.blinker')).html("Blink Led for <span class='time_input'></span> Seconds");
	$('#blink_timer').attr('type', 'text')
		.click(function(ev){ev.stopPropagation();})
		.keypress(function(ev){if(ev.keyCode == 13){$(this).parents('.btn').trigger('click'); ev.preventDefault();}})
		.appendTo('.time_input', $(e).parent('.blinker'));
}
</script>


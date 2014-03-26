<div class="blinker wrapper ">
<a class="btn btn-default  led_blinker" onclick="blink($('#blink_timer').val())">Blink LED</a>
<img src="img/clock.png" onclick="setBlinkTime(this)" alt="Timer" title="Set Blink Period" width="26" height="26"/>
<input type="hidden" id="blink_timer" value="10" size="2" />
</div>

<script type="text/javascript">
function setBlinkTime(e){
	$(e).hide();
	$('.led_blinker', $(e).parent('.blinker')).html("Blink Led for <span class='time_input'></span> Seconds");
	$('#blink_timer').attr('type', 'text')
		.click(function(ev){ev.stopPropagation();})
		.keypress(function(ev){if(ev.keyCode == 13){$(this).parents('.btn').trigger('click'); ev.preventDefault();}})
		.appendTo('.time_input', $(e).parent('.blinker'));
}
</script>


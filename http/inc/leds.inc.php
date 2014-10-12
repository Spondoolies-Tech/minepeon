<?php
require_once('constants.inc.php');

function led_start_flash(){
	exec('echo 5 > /tmp/blink_led');
}

function led_stop_flash(){
	exec('rm /tmp/blink_led');
}

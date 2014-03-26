<?php
require_once('inc/constants.inc.php');

function led_on($led_id){
	echo 'echo 1 > /sys/class/gpio/gpio'.$led_id.'/value';
	exec('echo 1 > /sys/class/gpio/gpio'.$led_id.'/value');
}

function led_off($led_id){
	echo 'echo 0 > /sys/class/gpio/gpio'.$led_id.'/value';
	exec('echo 0 > /sys/class/gpio/gpio'.$led_id.'/value');
}

function led_flash($led_id, $flashes = 1, $duration=.2){
	//in case its on, lets turn it off for a quick flash
	$duration *= 1000000;
	echo $duration;
	led_off($led_id);
	for($i = 0; $i < $flashes; $i++){
		led_on($led_id);
		usleep($duration);
		led_off($led_id);
		usleep($duration);
	}
	led_on($led_id); // default state is on
}

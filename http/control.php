<?php
require_once('global.inc.php');

header('Expires: Sat 26 Jul 1997 05:00:00 GMT');
header('Last-Modified: '.gmdate('D, d M Y H:i:i').' GMT');
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Cache-control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');


switch($_GET['op']){
case 'mining_restart':
	require_once('miner.inc.php');
	$nice = isset($_GET['nice']);
	//$ret = miner_service('restart');
	$ret = miner_restart($nice);
	break;
case 'blink_led':
case 'end_blink_led':
	// flash LED's 
	require_once('leds.inc.php');
	//$seconds = $_GET['time'];
	//if(!is_numeric($seconds)) $seconds = 3;
	//led_flash(YELLOW_LED, $seconds*2, .25);
	led_flash($_GET['op'] == "blink_led" ? "start":"stop");
	break;
    case 'clear_log':
        if (file_exists("/tmp/mg_event_log")) {
            unlink("/tmp/mg_event_log");
        }
        break;
    case 'spond_start':
        exec("/usr/local/bin/spond-manager start >> /dev/null 2>&1");
        break;

    case 'spond_stop':
        exec("/usr/local/bin/spond-manager stop >> /dev/null 2>&1");
        break;

default:
	$ret = 'Error: Unknown operation';

    echo $ret;
}

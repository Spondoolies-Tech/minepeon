<?php

/**
 * global includes, or operations which must be performed at every page load
 * initially, this file is just to check that the user has accepted our terms and conditions
 */

/**
 * @var $setting sarray
 */
require_once('inc/settings.inc.php');


// make sure user has accepted terms and conditions before allowing them to do anything else
if((!array_key_exists('agree', $settings) || ! intval(time($settings['agree'])))  ){
	$open_pages = array(
		'contact.php',
		'agreement.php',
		'license.php'
	);

	if(!in_array(basename($_SERVER['REQUEST_URI']), $open_pages)  && !(isset($_POST['agree']) && basename($_SERVER['REQUEST_URI']) == 'settings.php' ) ){
		require('agreement.php'); exit;	
	}
} 

//Update watchdog monitored file (to prevent reboots)
file_put_contents('/var/run/dont_reboot', "1");
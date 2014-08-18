<?php

/**
 * global includes, or operations which must be performed at every page load
 * initially, this file is just to check that the user has accepted our terms and conditions
 */

$model_id=trim(file_get_contents('/model_id'));
$full_model_name=($model_id=="SP10")?"SP10 Dawson":"SP30 Yukon";

/**
 * @var $setting sarray
 */

require_once('inc/constants.inc.php');
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

if ($model_id == "SP10") {
$default_max_watts = 1260;
$default_dc2dc_current = 62;
} else {
$default_max_watts = 1360;
$default_dc2dc_current = 140;
}

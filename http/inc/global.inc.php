<?php

/**
 * global includes, or operations which must be performed at every page load
 * initially, this file is just to check that the user has accepted our terms and conditions
 */

//ini_set("display_errors", 1);

$model_id=trim(file_get_contents('/model_name'));
$model_class=trim(file_get_contents('/model_id'));
$limited_access = 0;
if(file_exists('/etc/mg_limited_access')){
    $limited_access=intval(trim(file_get_contents('/etc/mg_limited_access')));
}
#$model_class = preg_replace('/.$/', 'x', $model_id);

switch($model_class){
	case 'SP1x':
		$model_long_name = 'Dawson';
		break;
	case 'SP50':
        $model_long_name = 'Yukon';
		break;
	case 'SP3x':
        $model_long_name = 'Yukon';
		break;
	case 'SP2x':
        $model_long_name = 'Jackson';
		break;
}
$full_model_name= $model_id.' '.$model_long_name;

/**
 * @var $setting sarray
 */

require_once('constants.inc.php');
require_once('settings.inc.php');

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

switch($model_class){
	case 'SP1x':
		$default_max_watts = 1260;
		$default_dc2dc_current = 62;
		break;
	case 'SP50':
	case 'SP3x':
	default:
		$default_max_watts = 1360;
		$default_dc2dc_current = 140;
}

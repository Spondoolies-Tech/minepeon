<?php
/*
f_pools_save saves the pools data
returns success, bytes written and new pool data
this file should be called f_pools.php and should be built like f_settings.php
*/
header('Content-type: application/json');

// Check for POST or GET data
if (empty($_REQUEST['saving']) or !$_REQUEST['saving']) {
	echo json_encode(array('success' => false, 'debug' => "Not saving"));
	exit;
}

//initialize a limit to the number of pools that are added to the miner config file. is there an official limit?
$poolLimit = 20;

// Loop through all rows, stop after 3 empty rows or if poolLimit is exceeded, process the POST or GET data
$e = 0;
$hostname = trim(exec("hostname"));
$hostname = str_replace("miner-","",$hostname);
$ip =  $_SERVER['SERVER_ADDR'];


for($i=0;$i<$poolLimit || $e < 3;$i++) {
	if(!empty($_REQUEST['URL'.$i]) and !empty($_REQUEST['USER'.$i])){
		// Set pool data
		// Avoid empty pool passwords because it might be problematic if used in a command
//        $user = str_replace("%h",$hostname,trim($_REQUEST['USER'.$i]));
//        $user = str_replace("%i",$ip,$user);
//        $user = str_replace("%v",trim(file_get_contents(CURRENT_VERSION_FILE)),$user);
		$dataPools[] = array(
			"url" => trim($_REQUEST['URL'.$i]),
			"user" => $user,
			"pass" => trim(empty($_REQUEST['PASS'.$i])?"none":$_REQUEST['PASS'.$i])
			);

		// reset empty
		$e = 0;
	}
	else{
		// increment empty count
		$e++;
	}

	// debug output
	// echo $_REQUEST['URL'.$i.''] . $_REQUEST['USER'.$i.''] . $_REQUEST['PASS'.$i.''];
}

$written = 0;
// Recode into JSON and save
// Never save if no pools given
if (!empty($dataPools)) {
	// Read current config, prefer miner.user.conf
	//if(file_exists("/etc/cgminer.conf")){
    $data = json_decode(file_get_contents("/etc/cgminer.conf.template", true), true);
/*	}
	else{
		$data = json_decode(file_get_contents("/opt/minepeon/etc/miner.conf", true), true);
	}*/
    // Unset currect
    unset($data['pools']);

    //Verify that API parameters are set in cgminer config and add any missing ones
    If(!isset($data['api-listen']) || !isset($data['api-allow']))
    {
        unset($data['api-listen']);
        unset($data['api-allow']);

        $data['api-listen'] = true;
        $data['api-allow'] = "W:127.0.0.1";
    }

	// Set new pool data
	$data['pools']=$dataPools;

	// Write back to file
	$written = file_put_contents("/etc/cgminer.conf.template", json_encode($data));
}

echo json_encode(array('success' => true, 'written' => $written, 'pools' => $dataPools));
?>

<?php

require_once('constants.inc.php');
require_once('settings.inc.php'); // for syncing

function miner($command, $parameter) {

        $command = array (
                "command"  => $command,
                "parameter" => $parameter
        );

        $jsonCmd = json_encode($command);
        $host = "127.0.0.1";
        $port = 4028;
        $client = @stream_socket_client("tcp://$host:$port", $errno, $errorMessage, 1.5);

        if ($client === false) {
		    throw new Exception("The miner is not running or waiting for pool connection.");
        }
        fwrite($client, $jsonCmd);
        stream_set_timeout($client, 1.5);
        $response = stream_get_contents($client);
        fclose($client);
        $response = preg_replace("/[^[:alnum:][:punct:]]/","",$response);
        $response = json_decode($response, true);
        return $response;

}

function promotePool($addr, $user){
try{
  $pools = miner('pools','');
}catch(Exception $e){
echo $e;
}
  $pools = $pools['POOLS'];
  $pool = 0;
  // echo "changeing";
  foreach ($pools as $key => $value) {
    if(isset($value['User']) && $value['URL']==$addr && $value['User']==$user){
	  // echo "found";
	  miner('switchpool',$pool);
    }
	$pool = $pool + 1;
  }
  
}

function setMinerSpeed($speed){
	$speed = sprintf("CONF:%d %d %d %d %d %d", $speed['fan_speed'], (float)$speed['start_voltage_top']*1000, (float)$speed['start_voltage_bot']*1000, (float)$speed['max_voltage']*1000, $speed['max_watts'], $speed['dc2dc_current']);
	file_put_contents(MINER_WORKMODE_FILE, $speed);
	settings_sync();
}

function getMinerSpeed(){
	if(file_exists(MINER_WORKMODE_FILE)) $s = trim(file_get_contents(MINER_WORKMODE_FILE), "CONF: ");
	else $s = DEFAULT_MINER_WORKMODE.' '.$DEFAULT_MAX_WATTS.' '.$DEFAULT_DC2DC_CURRENT;
	return explode(' ', $s);
}

function miner_service($op = "restart"){
	exec(MINER_CONTROL_CMD.$op. " > /dev/null");
	if(!$ret) return 'Operation succesful';
	else return 'There was an error while calling the Spondoolies manager.';
}

function miner_restart($nice = false){
	if($nice){
		try{
		return miner('restart');
		}catch(Exception $e){
		
		}
	}
	return miner_service('restart');
}



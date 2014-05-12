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
	$old_speed = getMinerSpeed();
	$speed = sprintf("%s %s", $speed, $old_speed[3]); // psu limit in fourth place
	file_put_contents(MINER_WORKMODE_FILE, $speed);
	//miner_service("restart");
	settings_sync();
}

function getMinerSpeed(){
	if(file_exists(MINER_WORKMODE_FILE)) $s = file_get_contents(MINER_WORKMODE_FILE);
	else $s = DEFAULT_MINER_WORKMODE;
	return explode(' ', $s);
}

function get_psu_limit(){
	$speed = getMinerSpeed();
	return $speed[3];
	//return exec('cat '.MAX_ELECTRICAL_USAGE_FILE);
}
function set_psu_limit($limit){
	//exec('echo '.$limit.' > '.MAX_ELECTRICAL_USAGE_FILE, $output, $ret);
	$speed = getMinerSpeed();
	$speed[3] = $limit;
	$speed = implode(' ', $speed);
	return file_put_contents(MINER_WORKMODE_FILE, $speed);
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



<?php

require_once('global.inc.php');

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
	foreach($speed as $k=>$v){
		$v = floatval($v);
		if (floor($v) == 0) $v *= 1000;
		$speed[$k] = intval($v);
	}

	$format = explode(' ', WORKMODE_FORMAT);
	$workmode = array();
	foreach($format as $key){$workmode[] = $speed[$key];}
	$workmode = vsprintf(WORKMODE_FORMAT_LINE, $workmode);
	file_put_contents(MINER_WORKMODE_FILE, $workmode);
	settings_sync();
}

function getMinerSpeed($runtime = false){
	$file = MINER_WORKMODE_FILE;
	if($runtime && file_exists(MINER_RUNTIME_WORKMODE_FILE)) $file = MINER_RUNTIME_WORKMODE_FILE;
	if(file_exists($file)) $s = trim(file_get_contents($file));
	else throw new Exception("Workmode file (".MINER_WORKMODE_FILE.") missing. Please contact customer support.");
	return sscanf($s, WORKMODE_FORMAT_LINE);
}

/**
 * this should replace getMinerSpeed
 */
function getMinerWorkmode($runtime=false){
	$keys = explode(" ", WORKMODE_FORMAT);
	$text = explode("-", WORKMODE_TEXT);
	$data = getMinerSpeed($runtime);
	$workmode = array();
	foreach($data as $i=>$v){
		$workmode[$keys[$i]] = array("text"=>$text[$i], "value"=>$v);
	}
	return $workmode;
}

function miner_service($op = "restart"){
    if (file_exists("/tmp/mg_last_voltage")) {
        unlink("/tmp/mg_last_voltage");
    }
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



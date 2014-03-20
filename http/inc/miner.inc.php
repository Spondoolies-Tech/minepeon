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
		    throw new Exception("The miner is not ready");
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
	if(!is_numeric($speed) ) return;
	file_put_contents(MINER_WORKMODE_FILE, $speed);
	settings_sync();
}

function getMinerSpeed(){
	if(file_exists(MINER_WORKMODE_FILE)) return file_get_contents(MINER_WORKMODE_FILE);
	return DEFAULT_MINER_WORKMODE;
}

function miner_service($op = "restart"){
	exec('/usr/local/bin/spond-manager '.$op. " > /dev/null");
	if(!$ret) return 'Operation succesful';
	else return 'There was an error while calling the Spondoolies manager.';
}





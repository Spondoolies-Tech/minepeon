<?php


function miner($command, $parameter) {

        $command = array (
                "command"  => $command,
                "parameter" => $parameter
        );

        $jsonCmd = json_encode($command);

        $host = "127";
        $port = 4028;

        $client = @stream_socket_client("tcp://$host:$port", $errno, $errorMessage, 1.5);

        if ($client === false) {
		    throw new Exception("The miner is loading");
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

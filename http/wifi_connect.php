<?php
//Handle the connect command
//Preparation
$desc = array(
    0 => array("pipe", "r"),  // stdin is a pipe that the child will read from
    1 => array("pipe", "w"),  // stdout is a pipe that the child will write to
    2 => array("file", "/tmp/wifi-conf-create.txt", "a") // stderr is a file to write to
);

//Create and open new process
if(isset($_POST['password'])) //with password and key, if set
    $cmd = 'ESSID="'.$_POST['wifiName'].'" KEY_MGMT="'.$_POST['keyMgmt'].'" PROTO="'.$_POST['protocol'].'" PAIRWISE_CIPHERS="'.$_POST['pairWise'].'" GROUP_CIPHERS="'.$_POST['groupCiphers'].'" /usr/local/bin/wifi-conf-create.sh';
else //without password and key
    $cmd = 'ESSID="'.$_POST['wifiName'].'" KEY_MGMT="NONE" /usr/local/bin/wifi-conf-create.sh';

$process = proc_open($cmd, $desc, $pipes);

//Input password, if defined
if(isset($_POST['password'])) {
    fwrite($pipes[0], $_POST['password']);
}
//Close input to start command
fclose($pipes[0]);

stream_get_contents($pipes[1]);

//Clean-up inputs
fclose($pipes[1]);
fclose($pipes[2]);
proc_close($process);


//Restart the network
exec('/etc/init.d/S40network restart');

exit(0);
?>

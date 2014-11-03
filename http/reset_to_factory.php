<?php
$dir = '/mnt/config/etc/';
$reboot = true;
//Remove all the custom settings files

$except = explode(',', $_REQUEST['except']);
foreach($except as $k=>$v){
	copy($dir.$v, '/tmp/'.basename($v));
}


exec('rm -rf /mnt/config/rrd/*');
exec('rm -rf /mnt/config/log/*');
exec('rm -rf /mnt/config/etc/*');

foreach($except as $k=>$v){
	@mkdir($dir.'/'.str_replace(basename($v), '', $v), 0777, true); // crate subdirectories, if necessary
	copy('/tmp/'.basename($v), $dir.$v);
}

//Reboot the machine
//header('Location: /reboot.php');
//die;

?>	
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN"
    "http://www.w3.org/TR/html4/strict.dtd">
<html>
  <head>
    <title>Rebooting...</title>
<script type="text/javascript">
var start = new Date();
start = Date.parse(start)/1000;
var seconds = 120;
function CountDown(){
    var now = new Date();
    now = Date.parse(now)/1000;
    var counter = parseInt(seconds-(now-start),10);
    document.getElementById('countdown').innerHTML = counter;
    if(counter > 0){
        timerID = setTimeout("CountDown()", 100)
    }else{
	    location.href = "http://<?php echo $_SERVER['SERVER_ADDR']; ?>/"
    }
}
window.setTimeout('CountDown()',100);
</script>
  </head>
  <body>
  <center>
<?php if($refresh_ip != "none"){ ?>
  <p><h1>Rebooting Miner</h1></p>
  <p>You will be reconnected in</p>
  <p><h1 id="countdown">120</h1></p>
  <p>seconds.</p> 
<?php } else{ ?>
<div><center><h3>Your miner is rebooting.</h3><h5>If you changed your ip, your miner will have a new ip when it restarts. To access the MinerUI, enter the new IP into the address bar.</h5></center></div>
<?php } ?>
  </center>
  </body>
</html>
<?php

#exec('/usr/local/bin/spond-manager stop > /dev/null 2>&1 ');
#sleep(3);
// flush buffer, so user can see the countdown timer without waiting for the script to complete (500 error)
ob_flush();
flush();
exec('/bin/sync');
exec('kill `pidof watchdog`');
exec('watchdog -T 15 -t 30 /dev/watchdog0');
exec('/sbin/reboot > /dev/null 2>&1 &');

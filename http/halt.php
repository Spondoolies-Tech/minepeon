<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN"
    "http://www.w3.org/TR/html4/strict.dtd">
<html>
  <head>
    <title>Page title</title>
<script type="text/javascript">
var start = new Date();
start = Date.parse(start)/1000;
var seconds = 20;
function CountDown(){
    var now = new Date();
    now = Date.parse(now)/1000;
    var counter = parseInt(seconds-(now-start),10);
    document.getElementById('countdown').innerHTML = counter;
    if(counter > 0){
        timerID = setTimeout("CountDown()", 100)
    }else{
        location.href = "/"
    }
}
window.setTimeout('CountDown()',20);
</script>
  </head>
  <body>
  <center>
  <p><h1>Shutting Down Miner</h1></p>
  <p>It should be safe to unplug in</p> 
  <p><h1 id="countdown">20</h1></p>
  <p>seconds.</p> 
  </center>
  </body>
</html>

<?php
//exec('/usr/local/bin/spond-manager stop > /dev/null 2>&1');
sleep(5);
exec('/sbin/halt > /dev/null 2>&1 &');

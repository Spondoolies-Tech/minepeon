<?php

include('head.php');
include('menu.php');
?>
<style type="text/css">
#upgrade_wrapper{
height:300px;
background:#cbc;
color:#323;
overflow:auto;
padding:10px 0 0 10px;
}
</style> 
<script language="javascript" type="text/javascript">
function upgrade(){
if(!confirm("Upgrade Firmware?")) return;
//document.getElementById("upgrade_output").src="upgrade.php";
var t = document.getElementById("upgrade_output");
var s = document.getElementById("upgrade_scroller");
var req = new XMLHttpRequest();
try{
	req.responseType = "chunked-text";
}catch(e){}
req.onreadystatechange = function(){
if(req.readyState > 2){
if(req.responseText) t.innerHTML = req.responseText;
else if(req.response) t.innerHTML = req.response; 
s.scrollIntoView();
}
}
req.open("GET", "upgrade.php", true);
req.send();
}

</script>
<div>

<div class="container">
<button name="upgrade" onclick="javascript:upgrade()">Upgrade Now</button>
<pre>
<div id="upgrade_wrapper">
<div id="upgrade_output"></div>
<span id="upgrade_scroller"></span>
</div>
</pre>
</div>
<?php
include('foot.php');


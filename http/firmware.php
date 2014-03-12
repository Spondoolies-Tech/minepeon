<?php

include('head.php');
include('menu.php');
?>
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
<p class="alert"><b>WARNING:</b>Power interruption during the upgrade may brick your unit!</p>
<h1>Firmware upgrade</h1>
<button name="upgrade" class="btn btn-default" onclick="javascript:upgrade()">Upgrade Now</button>
<br><br>
<pre>
<div id="upgrade_output"></div>
<span id="upgrade_scroller"></span>
</div>
</pre>
</div>
<?php
include('foot.php');


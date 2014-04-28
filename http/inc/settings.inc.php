<?php

if (file_exists("/opt/minepeon/DEBUG")) {
    $debug = true;
} else {
    $debug = false;
} 

$settings = json_decode(file_get_contents("/etc/minepeon.conf", true), true);


$timezone = $settings['userTimezone'];
ini_set( 'date.timezone', $timezone );
putenv("TZ=" . $timezone);
date_default_timezone_set($timezone);

$uptime = explode(' ', exec("cat /proc/uptime"));

function writeSettings($settings, $file = 'minepeon.conf') {
	// Call this when you want settings to be saved with writeSettings($settings);
	// can be used to save to an alternat file name with writeSettings($settings, 'OtherFileName.conf);

	file_put_contents("/etc/" . $file, json_encode($settings/*, JSON_PRETTY_PRINT | JSON_NUMERIC_CHECK*/));
	settings_sync();
}

function settings_sync(){
	exec('/bin/sync');
}

function get_psu_limit(){
	return exec('cat '.MAX_ELECTRICAL_USAGE_FILE);
}
function set_psu_limit($limit){
	exec('echo '.$limit.' > '.MAX_ELECTRICAL_USAGE_FILE, $output, $ret);
	return $ret;
}

function json2html($json, $level=0){
	$json_test = json_decode($json, true);
	if(is_array($json_test)) $json = $json_test;
	if(is_bool($json)) $json = ($json) ? 'True' : 'False';
	if(!is_array($json)) $json = strval($json);
	if(is_string($json)) return '<span class="value">'.$json.'</span>';
	// if not string, is array
	if(array_keys($json) == range(0, count($json)-1)){ // numeric array
		$html = '<ul class="list">';
		foreach($json as $v) $html .= '<li>'.json2html($v, $level+1).'</li>';
		$html = $html.'</ul>';
	}else{
		$html = '<ul class="obj">';
		foreach($json as $k=>$v) $html .= '<li><span class="key">'.$k.'</span><p>'.json2html($v, $level+1).'</p></li>';
		$html = $html.'</ul>';
	}
	if($level == 0) $html = '<div class="json formatted">'.$html.'</div>';
	return $html;
}

$plea = '
<hr />
<h3>Plea</h3>
<p>Please reconsider your decision to give back absolutely nothing to the project that is currently running your miners. A lot of time and effort has gone into making MinePeon what it is today and a small token of 15 minutes of your hash power would be greatly appreciated and will continue to fund the ongoing development and support of MinePeon.  </p>
<p>It is such a small amount and well below the normal variance in bitcoin mining you will not even notice the difference. If you work it out for every 1 GH/s you have it is 0.00027 bitcoin a day, ask yourself, is that really too much to support MinePeon?</p>
<p>Some of the features that I would like to include are;-</p>
<ul>
<li>TFT Display</li>
<li>LCD Display</li>
<li>Android app</li> 
<li>iOS app</li>
<li>Live Update</li>
<li>SMS/Email Alerts</li>
<li>Backup/Restore</li>
<li>Cloud Control</li>
<li>VPN Tunneling (DDOS Protection & Anonymity)</li>
</ul>
<p>Most of those new features cost money to setup and run, I would prefer not to have to make features available as "paid for" addons but it all depends on you.</p>
<p>Neil Fincham</p>
<p>The MineForeman</p>';
?>

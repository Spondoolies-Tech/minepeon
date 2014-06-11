<?php

require('global.inc.php');
require('miner.inc.php');

$json = array();

$stats = miner('stats');
$json['stats'] = $stats['STATS'][0];
$ppols = miner('pools');
//$summary = miner("summary", "");
$json['pools'] = $pools['POOLS'];

$json['conf'] = json_decode(file_get_contents(CGMINER_CONF_FILE, true), true);

$json['miner'] = array();
	$json['miner']['model_id'] = trim(file_get_contents(MODEL_ID_FILE));
    $hostname = trim(exec("hostname"));
    $json['miner']['board_ver'] = str_replace("miner-","",$hostname);
	$json['miner']['fw_ver'] = trim(file_get_contents(CURRENT_VERSION_FILE));
	$json['miner']['mac'] = trim(exec("/usr/local/bin/getmac.sh"));
    $json['miner']['uptime'] = round($uptime[0]);
    $json['miner']['free_mem'] = exec('awk \'/MemFree/ {printf( "%.2d\n", $2 / 1024 )}\' /proc/meminfo');
$json['mg_events'] = '';
if(file_exists(MG_EVENTS_FILE)){
	$json['mg_events'] = trim(file_get_contents(MG_EVENTS_FILE));
}
file_put_contents(MG_EVENTS_FILE, '');

$json['mg_status'] = '';
if(file_exists(MG_STATUS)) $json['mg_status'] = trim(file_get_contents(MG_STATUS));

echo json_encode($json);

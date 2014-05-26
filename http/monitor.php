<?php

require('global.inc.php');
require('miner.inc.php');

$json = array();

$stats = miner('stats');
$json['stats'] = $stats['STATS'][0];
$ppols = miner('pools');
$json['pools'] = $pools['POOLS'];

$json['conf'] = json_decode(file_get_contents(CGMINER_CONF_FILE, true), true);

$json['miner'] = array();
	$json['miner']['board_id'] = file_get_contents(BOARD_ID_FILE);
	$json['miner']['board_ver'] = file_get_contents(BOARD_VERSION_FILE);
	$json['miner']['fw_ver'] = file_get_contents(CURRENT_VERSION_FILE);
	$json['miner']['mac'] = exec("grep -o '\(..:\)\{5\}..' | head -1");

$json['notices'] = '';
if(file_exists(MG_NOTICES_FILE)){
	$json['notices'] = file_get_contents(MG_NOTICES_FILE);
}
file_put_contents(MG_NOTICES_FILE, '');

$json['status'] = '';
if(file_exists(MG_STATUS)) $json['status'] = file_get_contents(MG_STATUS);

echo json_encode($json);

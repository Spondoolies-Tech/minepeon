<?php
/**
 * a script for checking the status of a process through ajax
 */

function get_status($proc){
switch($proc){
case 'cgminer':
	require_once('miner.inc.php');
	try{
		miner('summary', '');
		$status = true;
	}catch(Exception $e){
		$status = false;
	}
	$ret = array("status"=>$status);
	break;
case 'ps_cgminer':
	$pid = get_pid('cgminer');
	$ret = array('status'=>!empty($pid));
	break;
case 'ps_miner_gate':
	$pid = get_pid('miner_gate');
	$ret = array('status'=>!empty($pid));
	break;
default:
	$ret = array("error"=>"unknown operation");
}
return $ret;
}

function get_pid($proc){
	// note, that this matchs the expected output of ps on the miner. ps on other machines may not have the process name in field 5, or the id in field 1
	return exec(" ps -l | awk '\$10 ~ /$proc/{print \$3}'");
}

if(isset($_GET['proc'])){
	echo json_encode(get_status($_GET['proc']));
}


<?php
/**
 * a script for checking the status of a process through ajax
 */

switch($_GET['proc']){
case 'cgminer':
	require_once('miner.inc.php');
	try{
		miner('summary', '');
		$status = true;
	}catch(Exception $e){
		$status = false;
	}
	echo json_encode(array("status"=>$status));
	break;
default:
	echo json_encode(array("error"=>"unknown operation"));
}



<?php

require_once('global.inc.php');

const CRON_BEGIN = "### BEGIN %s ###";
const CRON_END =   "###  END %s  ###";
define("CRON_MINER_RESTART_CMD", MINER_CONTROL_CMD." restart");
const CRON_CMD = "crontab";
define("CRON_CMD_LIST", CRON_CMD." -l");
const CRON_GROUP_MINER_SPEED = 'miner speed';
define("CRON_MINER_SPEED_SCHEDULE", "echo %s > ".MINER_WORKMODE_FILE. " && ".CRON_MINER_RESTART_CMD);
const CRONLINE = '%s %s * * %s %s';

function get_schedule($group){
	$cron = $ret = array();
	$start = sprintf(CRON_BEGIN, $group);
	$end = sprintf(CRON_END, $group);
	$state = "read";
	exec(CRON_CMD_LIST, $cron);
	foreach($cron as $line){
		$line = trim($line);
		switch($state){
			case "read":
				if($line == $start){
					$state = "rec";
				}
				break;
			case "rec":
				if($line == $end){
					break(2);
				}
				$ret[] = $line;
		}
	}
	return cron2sched($ret);
}

function write_schedule($group, $lines){
	$start = sprintf(CRON_BEGIN, $group);
	$end = sprintf(CRON_END, $group);
	$state = "read";
	$cronlines = array();
	exec(CRON_CMD_LIST, $cron);
	foreach($cron as $line){
		$line = trim($line);
		switch($state){
			case "read":
				$cronlines[] = $line;
				if($line == $start){
					$state = "replace";
					$cronlines = array_merge($cronlines, $lines);
					$lines = array();
				}
				break;
			case "replace":
				if($line == $end){
					$state = "read";
					$cronlines[] = $line;
				}
		}
	}
	if(count($lines)){
		$cronlines[] = $start;
		$cronlines = array_merge($cronlines, $lines);
		$cronlines[] = $end;
	}
	$rc = popen(CRON_CMD." - ", 'w');
	fwrite($rc, implode("\n", $cronlines)."\n");
	pclose($rc);
}

function save_schedule($group, $data){
	$sched = input2sched($data);
	$cronlines = sched2cron($sched);
	write_schedule($group, $cronlines);
}

function cron2sched($schedule){
	if(empty($schedule)) return array();
	$item_template = array('minute'=>0, 'hour'=>0, 'cmd');
	$times = array("minute", "hour", "day", "month", "weekday");
	$ret = array_fill(0, 7, null);
	foreach($schedule as $item){
		preg_match("/(\S+\s+){5}/", $item, $m);
		$sched = $m[0];
		$cmd = str_replace($sched, '', $item);
		$cron_symbols = preg_split('/\s+/', trim($sched));
		$sched = array_combine($times, $cron_symbols);
		foreach(explode(',', $sched['weekday']) as $day){
			if(!array_key_exists($day, $ret)) $ret[$day] = array();
			foreach(explode(',', $sched['minute']) as $minute){
			foreach(explode(',', $sched['hour']) as $hour){
				$time = sprintf('%02s:%02s', $hour, $minute);
				$time = str_replace('0*', '--', $time);
				$ret[$day][$time] = $cmd;
			}
			}
		}
	}
	$ret = array_filter($ret);
	foreach($ret as &$day){
		ksort($day);
	}
	return $ret;
}

function sched2cron($sched){
	$crons = array();
	foreach($sched as $cmd => $day){
		foreach($day as $d => $hour){
			foreach($hour as $h => $minutes){
				if(strval($d) == 'all') $d = '*';
				$crons[] = sprintf(CRONLINE, $minutes, $h, $d, sprintf(CRON_MINER_SPEED_SCHEDULE, $cmd));
			}
		}
	}
	return $crons;
}

function input2sched($data){
	// data is parrallel arrays for day, hour, minute, task
	$items = count($data['day']);
	$jobs = array();
	for($i = 0; $i < $items; $i++){
		$day = $data['day'][$i];
		if($day === "") continue; // "add new" field
		$cmd = $data['cmd'][$i];
		$hour = $data['hour'][$i];
		$minute = $data['minute'][$i];
		if(!array_key_exists($cmd, $jobs)) $jobs[$cmd] = array();
		if(!array_key_exists($day, $jobs[$cmd])) $jobs[$cmd][$day] = array();
		if(!array_key_exists($hour, $jobs[$cmd][$day])) $jobs[$cmd][$day][$hour] = array();
		$jobs[$cmd][$day][$hour][] = $minute;
	}
	foreach($jobs as $j => $day){
		foreach($day as $d => $hour){
			foreach($hour as $h => $minutes){
			$jobs[$j][$d][$h] = implode(',', $minutes);
			}
		}
	}
	return $jobs;
}

function cron_commands($group, $key=null){
	$commands = array(
		CRON_GROUP_MINER_SPEED => array(
                          0 => "~1.35Th / ~1100W / ~quiet",
                          1 => "~1.43Th / ~1350W / normal",
                          2 => "~1.47Th / ~1370W / turbo",
                          3 => "~1.00Th / ~720W / ~quiet"
		)
	);
	if(is_null($key)) return $commands[$group];
	return $commands[$group][$key];
}

// HTML functions
function schedule_form_element($group, $day="", $time=":", $cmd=""){
	$time = explode(':', $time);
	switch($group){
		case CRON_GROUP_MINER_SPEED:
		sscanf($cmd, CRON_MINER_SPEED_SCHEDULE, $cmd);
		$html = 'At '.hour_select($time[0]).':'.minute_select($time[1]).' switch to: '.command_select($group, $cmd).'<input type="hidden" name="day[]" value="'.$day.'" class="day_field" />';
		break;
	default:
		throw new Exception('Unknown cron group referenced.');
	}
	return $html;
}

function command_select($group, $default=-1){
	$commands = cron_commands($group);
	$e = '<select name="cmd[]" class="cmd_field">';
	foreach($commands as $k=>$v){
		$selected = '';
		if($k == $default) $selected = 'selected="selected"';
		$e .= '<option value="'.($k).'" '.$selected.'>'.$v.'</option>';
	}
	$e .= '</select>';
	return $e;
}

function hour_select($default=""){
		if(is_numeric($default)) $default = intval($default);
		$e = '<select name="hour[]" class="hour_field">';//<option value="*">0-23</option>';
		for($i = 0; $i < 24; $i++){
			$selected = '';
			if($i == $default) $selected = 'selected="selected"';
			$e .= sprintf('<option value="%d" %s>%02d</option>', $i, $selected, $i);
		}
		$e .= '</select>';
		return $e;
}

function minute_select($default=""){
		$default = intval($default);
		$e = '<select name="minute[]" class="minute_field">'; //<option value="*">0-59</option>';
		for($i = 0; $i < 60; $i++){
			$selected = '';
			if($i == $default) $selected = 'selected="selected"';
			$e .= sprintf('<option value="%d" %s>%02d</option>', $i, $selected, $i);
		}
		$e .= '</select>';
		return $e;
}

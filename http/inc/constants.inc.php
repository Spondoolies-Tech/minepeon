<?php

const FIRMWARE_UPGRADE_SCRIPT = "/usr/local/bin/upgrade-software.sh";
const FIRMWARE_UPGRADE_URL = "http://firmware.spondoolies-tech.com/release/spon.tar";
const FIRMWARE_UPGRADE_URL_DEV = "http://firmware.spondoolies-tech.com/development/spon.tar";

const FIRMWARE_AVAILABLE_VERSIONS = "http://firmware.spondoolies-tech.com/release/versions?id=";
const FIRMWARE_DOWNLOAD_VERSION = "http://firmware.spondoolies-tech.com/release/download";

const MINER_CONTROL_CMD = "/usr/local/bin/spond-manager ";

const UI_USER_NAME = "admin";

const MINER_WORKMODE_FILE = '/etc/mg_custom_mode'; // work_mode

//const MAX_ELECTRICAL_USAGE_FILE = '/etc/mg_psu_limit';

const CURRENT_VERSION_FILE = '/fw_ver';
const LATEST_VERSION_FILE = '/tmp/fw_update';
const MODEL_ID_FILE = '/model_id';
const CGMINER_CONF_FILE = '/etc/cgminer.conf';
const MG_EVENTS_FILE = '/tmp/mg_event';
const MG_STATUS = '/tmp/mg_status';
const WIFI_SIGNAL_THRESHOLD = 0.4;

//leds
//const GREEN_LED = 51; // reserved for miner use
const YELLOW_LED = 22;
const BLINK_FILE = '/tmp/blink'; // NOTE: file also defined in /usr/local/bin/leds script.

// workmode
switch($model_id){
	case "SP10":
			define('WORKMODE_FORMAT', "fan_speed start_voltage_top start_voltage_bot max_voltage max_watts dc2dc_current");
			define('WORKMODE_FORMAT_LINE', "CONF:%d %d %d %d %d %d");
			define('WORKMODE_TURBO', '90 .680 .680 .790 '.$DEFAULT_MAX_WATTS.' '.$DEFAULT_DC2DC_CURRENT);
			define('WORKMODE_NORMAL', '70 .680 .680 .790 '.$DEFAULT_MAX_WATTS.' '.$DEFAULT_DC2DC_CURRENT);
			define('WORKMODE_QUIET', '50 .680 .680 .790 '.$DEFAULT_MAX_WATTS.' '.$DEFAULT_DC2DC_CURRENT);
		break;
	case "SP30":
			//const WORKMODE_FORMAT = "fan_speed start_voltage_top start_voltage_bot max_voltage max_watts_top max_watts_top dc2dc_current";
			define('WORKMODE_FORMAT', "FAN VST VSB VMAX AC_TOP AC_BOT DC_AMP");
			define('WORKMODE_FORMAT_LINE', "FAN:%d VST:%d VSB:%d VMAX:%d AC_TOP:%d AC_BOT:%d DC_AMP:%d");
			define('WORKMODE_TURBO', '90 .680 .680 .730 1340 1340 150');
			define('WORKMODE_NORMAL', '80 .680 .680 .730 1340 1340 150');
			define('WORKMODE_QUIET', '60 .660 .660 .730 1100 1100 150');
		break;
	default:
		throw new Exception("Miner type not set or not known: '".$model_id."'.");
}


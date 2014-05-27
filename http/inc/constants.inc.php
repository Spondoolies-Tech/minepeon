<?php

const FIRMWARE_UPGRADE_SCRIPT = "/usr/local/bin/upgrade-software.sh";
const FIRMWARE_UPGRADE_URL = "http://firmware.spondoolies-tech.com/release/spon.tar";
const FIRMWARE_UPGRADE_URL_DEV = "http://firmware.spondoolies-tech.com/development/spon.tar";

const FIRMWARE_AVAILABLE_VERSIONS = "http://firmware.spondoolies-tech.com/release/versions?id=SP10";
const FIRMWARE_DOWNLOAD_VERSION = "http://firmware.spondoolies-tech.com/release/download";

const MINER_CONTROL_CMD = "/usr/local/bin/spond-manager ";

const UI_USER_NAME = "admin";

const MINER_WORKMODE_FILE = '/etc/mg_custom_mode'; // work_mode

//const MAX_ELECTRICAL_USAGE_FILE = '/etc/mg_psu_limit';

const CURRENT_VERSION_FILE = '/fw_ver';
const LATEST_VERSION_FILE = '/tmp/fw_update';
const MODEL_ID_FILE = '/model_id';
const CGMINER_CONF_FILE = '/etc/cgminer.conf';
const MG_EVENTS_FILE = '/tmp/mg_events';
const MG_STATUS = '/tmp/mg_status';

const WIFI_SIGNAL_THRESHOLD = 0.4;

//leds
//const GREEN_LED = 51; // reserved for miner use
const YELLOW_LED = 22;
const BLINK_FILE = '/tmp/blink'; // NOTE: file also defined in /usr/local/bin/leds script.

// numbers
const DEFAULT_MINER_WORKMODE = "80 660 750"; //turbo
const DEFAULT_MAX_WATTS = 1260;
const DEFAULT_DC2DC_CURRENT = 62;

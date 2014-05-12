<?php

const FIRMWARE_UPGRADE_SCRIPT = "/usr/local/bin/upgrade-software.sh";
const FIRMWARE_UPGRADE_URL = "http://firmware.spondoolies-tech.com/release/spon.tar";
const FIRMWARE_UPGRADE_URL_DEV = "http://firmware.spondoolies-tech.com/development/spon.tar";

const FIRMWARE_AVAILABLE_VERSIONS = "http://firmware.spondoolies-tech.com/release/versions?id=SP10";
const FIRMWARE_DOWNLOAD_VERSION = "http://firmware.spondoolies-tech.com/release/download?id=SP10&fwver=";

const MINER_CONTROL_CMD = "/usr/local/bin/spond-manager ";

const UI_USER_NAME = "admin";

const MINER_WORKMODE_FILE = '/etc/mg_custom_mode'; // work_mode
const DEFAULT_MINER_WORKMODE = "80 664 683 1250"; //turbo
//const DEFAULT_MINER_WORKMODE = 2; //turbo

//const MAX_ELECTRICAL_USAGE_FILE = '/etc/mg_psu_limit';

const CURRENT_VERSION_FILE = '/fw_ver';
const LATEST_VERSION_FILE = '/tmp/fw_update';

const WIFI_SIGNAL_THRESHOLD = 0.4;

//leds
//const GREEN_LED = 51; // reserved for miner use
const YELLOW_LED = 22;
const BLINK_FILE = '/tmp/blink'; // NOTE: file also defined in /usr/local/bin/leds script.

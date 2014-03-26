<?php

const FIRMWARE_UPGRADE_SCRIPT = "/usr/local/bin/upgrade-software.sh";
const FIRMWARE_UPGRADE_URL = "http://firmware.spondoolies-tech.com/release/spon.tar";
const FIRMWARE_UPGRADE_URL_DEV = "http://firmware.spondoolies-tech.com/development/spon.tar";

const UI_USER_NAME = "admin";

const MINER_WORKMODE_FILE = '/etc/mg_work_mode';
const DEFAULT_MINER_WORKMODE = 2; //turbo

const MAX_ELECTRICAL_USAGE_FILE = '/etc/mg_psu_limit';

const CURRENT_VERSION_FILE = '/fw_ver';
const LATEST_VERSION_FILE = '/tmp/fw_update';

//leds
//const GREEN_LED = 51; // reserved for miner use
const YELLOW_LED = 22;

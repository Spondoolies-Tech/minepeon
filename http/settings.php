<?php

require_once('inc/global.inc.php');
require_once('inc/miner.inc.php');
require_once('inc/network.inc.php');
require_once('inc/cron.inc.php');
//Update watchdog monitored file (to prevent reboots)
//file_put_contents('/var/run/dont_reboot', "php_active_settings");

require_once($model_class.'/settings.php');

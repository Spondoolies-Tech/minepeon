<?php

require_once('global.inc.php');
require_once('miner.inc.php');
require_once('network.inc.php');
require_once('cron.inc.php');
//Update watchdog monitored file (to prevent reboots)
//file_put_contents('/var/run/dont_reboot', "php_active_settings");

require_once($model_class.'/settings.php');

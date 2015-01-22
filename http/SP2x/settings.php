<?php
require_once('inc/global.inc.php');
$utc = new DateTimeZone('UTC');
$dt = new DateTime('now', $utc);

if ($limited_access & 2) {
    echo "Limited access, no settings ".$limited_access;
    exit();
}

// Check for settings to write and do it after all checks
$writeSettings=false;

// Restore 

if (isset($_FILES["file"]["tmp_name"])) {
	exec("gunzip -cf ".$_FILES["file"]["tmp_name"] . " | tar -x -C / ");
	header('Location: /reboot.php');
	exit;
}


if(isset($_POST['asic'])){
    $asics = array();
    for($i = 0; $i < 8; $i++){
        if(isset($_POST['asic'][$i]))  $asics[$i] = $_POST['asic'][$i];
        else $asics[$i] = 1; // 1 = disabled, if checkbox was not checked
    }
    foreach($asics as $k=>$v) $asics[$k] = $k.':'.$v;
    $asics = array_chunk($asics, 2);
    file_put_contents(MG_DISABLED_ASICS, implode("\n", array_map(function($row){return implode(' ', $row);}, $asics)));
    //echo '<pre>';var_dump($_POST['asic']); var_dump($asics); die;
}

$asics = array_map(function($row){return explode(" ", trim($row));}, file(MG_DISABLED_ASICS));

// User settings
if (isset($_POST['userTimezone'])) {

  $settings['userTimezone'] = $_POST['userTimezone'];
  ksort($settings);
  writeSettings($settings);
	$current_tz = new DateTimeZone($_POST['userTimezone']);
	$offset =  $current_tz->getOffset($dt);
	$transition =  $current_tz->getTransitions($dt->getTimestamp(), $dt->getTimestamp());
	$abbr = $transition[0]['abbr'];
	$tz = $abbr.strval(-1*$offset/3600);
	file_put_contents('/etc/profile.d/timezone.sh', 'export TZ='.$tz); exec('export TZ='.$tz.'; date; /etc/init.d/S47cron restart'); // changing TZ will not affect running processes, like cron
  header('Location: /settings.php');
  exit;

}

// Network settings
if (isset($_POST['dhcpEnable'])) {
  if($_POST['dhcpEnable'] == "true"){
	  set_dhcp_network();
	  $refresh_ip="none";
  }
  else{
	  set_fixed_network(array($_POST['ipaddress'], $_POST['subnet'], $_POST['gateway'], $_POST['dns1'], $_POST['wifi_ipaddress'], $_POST['wifi_subnet']));
	  $refresh_ip = $_POST['ipaddress'];
  }
  header('Location: /reboot.php?ip='.$refresh_ip); // 
  exit;
}

if (isset($_POST['userPassword1'])) {

	if ($_POST['userPassword1'] <> '') {
        $hash = crypt($_POST['userPassword1'], base64_encode($_POST['userPassword1']));
        $contents = UI_USER_NAME . ':' . $hash;
        file_put_contents('/etc/ui.pwd', $contents);
	settings_sync();

		header('Location: /settings.php');
		exit;

	}
}

// SSH password change
if (isset($_POST['rootPassword1'])) {

    if ($_POST['rootPassword1'] <> '') {

        $npass = $_POST['rootPassword1'];
        exec("echo -e \"$npass\n$npass\n\" | passwd root");
	settings_sync();
        header('Location: /settings.php');
        exit;

    }
}

// Miner startup file
/*
 * if you enable this, make sure to fix the file paths!
if (isset($_POST['minerSettings'])) {

	if ($_POST['minerSettings'] <> '') {
	
		
		file_put_contents('/opt/minepeon/etc/init.d/miner-start.sh', preg_replace('/\x0d/', '', $_POST['minerSettings']));
		exec('/usr/bin/chmod +x /opt/minepeon/etc/init.d/miner-start.sh');
		settings_sync();
	}
}
$minerStartup = file_get_contents('/opt/minepeon/etc/init.d/miner-start.sh');
 */

if (isset($_POST['agree'])) {
	$settings['agree'] = time();
	$writeSettings = true;
}

if(isset($_POST['setRegisterDevice'])){ // toggle PandP device regisatration.
	$settings['setRegisterDevice'] = (array_key_exists('setRegisterDevice', $_POST) && $_POST['setRegisterDevice'] == "true") ? "true":"false";
	$writeSettings = true;

    //Rename the actual cron'ed registering file
    if($settings['setRegisterDevice'] == "true")
            rename("/etc/cron.d/pandp_register.sh.disabled", "/etc/cron.d/pandp_register.sh");
    else
        rename("/etc/cron.d/pandp_register.sh", "/etc/cron.d/pandp_register.sh.disabled");
}

if(isset($_POST['setSSLEnforce'])){ // toggle SSL Enforcement
	$settings['setSSLEnforce'] = (array_key_exists('setSSLEnforce', $_POST) && $_POST['setSSLEnforce'] == "true") ? "true":"false";
	$writeSettings = true;

    //Rename the actual cron'ed registering file
    if($settings['setSSLEnforce'] == "true")
        rename("/etc/lighttpd/redirect.conf.disabled", "/etc/lighttpd/redirect.conf");
    else
        rename("/etc/lighttpd/redirect.conf", "/etc/lighttpd/redirect.conf.disabled");

    exec("kilall lighttpd && lighttpd -f /etc/lighttpd/lighttpd.conf");
}

// Mining settings

if (isset($_POST['miningExpDev'])) {

  $settings['miningExpDev'] = $_POST['miningExpDev'];
  $writeSettings=true;

}
if (isset($_POST['miningExpHash'])) {

  $settings['miningExpHash'] = $_POST['miningExpHash'];
  $writeSettings=true;

}
if (isset($_POST['FAN'])) {
 // setMinerSpeed(intval($_POST["minerSpeed"]));
  setMinerSpeed($_POST);
  $mining_restart = true;
}
if(isset($_POST['minimal_rate']) && is_numeric($_POST['minimal_rate'])){
	file_put_contents('/etc/mg_minimal_rate', $_POST['minimal_rate']);
}
if(isset($_POST['flag_0']) && is_numeric($_POST['flag_0'])){
	file_put_contents('/etc/mg_flag_0', $_POST['flag_0']);
}
if(isset($_POST['flag_1']) && is_numeric($_POST['flag_1'])){
	file_put_contents('/etc/mg_flag_1', $_POST['flag_1']);
}
if(isset($_POST['hostname'])){
    file_put_contents('/etc/mg_hostname', $_POST['hostname']);
}

// Donation settings
if (isset($_POST['donateEnable']) and isset($_POST['donateAmount'])) {

  $settings['donateEnable'] = $_POST['donateEnable']=="true";
  $settings['donateAmount'] = $_POST['donateAmount'];

  // If one of both 0, make them both
  if ($_POST['donateEnable']=="false" || $_POST['donateAmount']<1) {
    $settings['donateEnable'] = false;
    $settings['donateAmount'] = 0;
  }
  $writeSettings=true;
  
}

// Alert settings
if (isset($_POST['alertEnable'])) {

  $settings['alertEnable'] = $_POST['alertEnable']=="true";
  $writeSettings=true;
  
}
if (isset($_POST['alertDevice'])) {

  $settings['alertDevice'] = $_POST['alertDevice'];
  $writeSettings=true;

}
if (isset($_POST['alertEmail'])) {

	$settings['alertEmail'] = $_POST['alertEmail'];
	$writeSettings=true;

}
if (isset($_POST['alertSmtp'])) {

  $settings['alertSmtp'] = $_POST['alertSmtp'];
  $writeSettings=true;

}

if (isset($_POST['alertSMTPAuth'])) {

  $settings['alertSMTPAuth'] = $_POST['alertSMTPAuth']=="true";
  $writeSettings=true;

}

if (isset($_POST['alertSmtpAuthUser'])) {

  $settings['alertSmtpAuthUser'] = $_POST['alertSmtpAuthUser'];
  $writeSettings=true;

}

if (isset($_POST['alertSmtpAuthPass'])) {

  $settings['alertSmtpAuthPass'] = $_POST['alertSmtpAuthPass'];
  $writeSettings=true;

}

if (isset($_POST['alertSmtpAuthPort'])) {

  $settings['alertSmtpAuthPort'] = $_POST['alertSmtpAuthPort'];
  $writeSettings=true;

}


/*if(isset($_POST['speedSched'])){
	save_schedule(CRON_GROUP_MINER_SPEED, $_POST);
}*/

if(isset($_POST['voltSched'])){
	save_schedule(CRON_GROUP_START_VOLTAGE, $_POST);
}

// Write settings
if ($writeSettings) {
  ksort($settings);
  writeSettings($settings);
}

function formatOffset($offset) {
	$hours = $offset / 3600;
	$remainder = $offset % 3600;
	$sign = $hours > 0 ? '+' : '-';
	$hour = (int) abs($hours);
	$minutes = (int) abs($remainder / 60);

	if ($hour == 0 AND $minutes == 0) {
		$sign = ' ';
	}
	return $sign . str_pad($hour, 2, '0', STR_PAD_LEFT) .':'. str_pad($minutes,2, '0');

}


$tzselect = '<select id="userTimezone" name="userTimezone" class="form-control">';

foreach(DateTimeZone::listIdentifiers() as $tz) {
	$current_tz = new DateTimeZone($tz);
	$offset =  $current_tz->getOffset($dt);
	$transition =  $current_tz->getTransitions($dt->getTimestamp(), $dt->getTimestamp());
	$abbr = $transition[0]['abbr'];

	$tzselect = $tzselect . '<option ' .($settings['userTimezone']==$tz?"selected":""). ' value="' .$tz. '">' .$tz. ' [' .$abbr. ' '. formatOffset($offset). ']</option>';
}
$tzselect = $tzselect . '</select>';

$workmode = getMinerWorkmode();

$voltage = exec('cat /tmp/voltage');
$overvolt110 = file_exists("/etc/mg_ignore_110_fcc");

//$schedule = get_schedule(CRON_GROUP_MINER_SPEED);
$schedule = get_schedule(CRON_GROUP_START_VOLTAGE);

include('head.php');
include('menu.php');




?>
<div class="container">
<?php if(isset($mining_restart)){ ?>
<div class="help-block alert lead">You must restart MinerGate for your settings to take effect.<br/>
    <?php include('widgets/mining_restart.php'); ?>
</div>
<?php } ?>
  <h2>Settings</h2>

<!-- ######################## Miner speed -->
<form name="speed" action="/settings.php" method="post" class="form-horizontal" id="speed_settings" onsubmit="return saveCustomSpeed()">
      <fieldset>
          <legend>Over-clocking <?php echo "(Socket voltage: $voltage volt)"; if (($voltage < 130) && $overvolt110) echo " ! 110V limitation override ! "; ?></legend>


          <div class="basic form-group">
              <div class="basic col-lg-9 col-offset-3 view-alternative">
                  <div class="radio">
                      <label>
                      </label>
                      <label>
                          <input name="speed_basic_radio" type="radio" speed="quiet" id="minerSpeed" value="0" <?php echo $workmode == 0?"checked":"";?> onclick="setCustomSpeed(this)">slow fans, medium rate<br>
                      </label>
                      <label>
                          <input name="speed_basic_radio" type="radio" speed="normal" id="minerSpeed" value="1" <?php echo $workmode == 1?"checked":"";?> onclick="setCustomSpeed(this)">medium fans, high rate<br>
                      </label>
                      <label>
                          <input name="speed_basic_radio" type="radio" speed="turbo" id="minerSpeed" value="2" <?php echo $workmode == 2?"checked":"";?> onclick="setCustomSpeed(this)">turbo fans, highest rate
                      </label>
                  </div>
              </div>

              <div class="custom col-lg-9 col-offset-3 view-alternative" style="display:none">
                  <div>
                      <small>Set your fans</small>
                      <div class="row">
                          <div class="col-5">
                              <label for="">Fan Speed</label>
                          </div>
                          <div>
                              <select name="FAN" id="fan_speed_select">

                                  <?php
                                  printf('<option value="0" %s>Auto</option>', ($i == $workmode['FAN']['value'])?' selected="selected"':'');
                                  for($i = 10; $i < 101; $i += 10){
                                      printf('<option value="%d" %s>%d</option>', $i, ($i == $workmode['FAN']['value'])?' selected="selected"':'', $i);
                                  } ?>

                              </select>
                          </div>
                      </div>
                      <small>Set your starting voltage no more then 10 mv under your stable working voltage (from ASIC stats page)</small>
                      <div class="row NOThidden">
                          <div class="col-5">
                              <label for="">Start Volts Unit 1(0.58-0.80)</label>
                          </div>
				          <div><input size="5" type="number" onblur="validateSpeed(this)" id="minimum_voltage_0" name="VS0" value="<?php echo $workmode['VS0']['value']/1000?>" min=".580" max=".81" step="0.001" /></div>
                      </div>
                      <div class="row NOThidden">
                          <div class="col-5">
                              <label for="">Start Volts Unit 2(0.58-0.80)</label>
                          </div>
				          <div><input size="5"  type="number" onblur="validateSpeed(this)" id="minimum_voltage_1" name="VS1" value="<?php echo $workmode['VS1']['value']/1000?>" min=".580" max=".81" step="0.001" /></div>
                      </div>
                      <div class="row NOThidden">
                          <div class="col-5">
                              <label for="">Start Volts Unit 3(0.58-0.80)</label>
                          </div>
				          <div><input size="5"  type="number" onblur="validateSpeed(this)" id="minimum_voltage_2" name="VS2" value="<?php echo $workmode['VS2']['value']/1000?>" min=".580" max=".81" step="0.001" /></div>
                      </div>
                      <div class="row NOThidden">
                          <div class="col-5">
                              <label for="">Start Volts Unit 4(0.58-0.80)</label>
                          </div>
				          <div><input size="5"  type="number" onblur="validateSpeed(this)" id="minimum_voltage_3" name="VS3" value="<?php echo $workmode['VS3']['value']/1000?>" min=".580" max=".81" step="0.001" /></div>
                      </div>

                      <small>Set your Maximum voltage limit to 0.790 for unlimited  or less for under-voltage</small>
                      <div class="row">
                          <div class="col-5">
                              <label for="">Voltage Limit (0.580-0.790)</label>
                          </div>
				<div><input size="5" type="number" onblur="validateSpeed(this)" id="maximum_voltage" name="VMAX" value="<?php echo $workmode['VMAX']['value']/1000?>" min=".580" max=".79" step="0.001" /></div>
                      </div>
              <small>Set PSU power.</small>
		      <div class="row">
			      <div class="col-5">
				      <label for="max_watts_top" class="control-label">Max PSU Power Unit 1 (70W - 288W) </label>
			      </div>
				<div><input type="text" size="4" onblur="validateSpeed(this)" name="AC0" id="max_watts_0" type="number" step="1" min="70" max="288" value="<?php echo $workmode['AC0']['value']?>"></div>
		      </div>
		      <div class="row">
			      <div class="col-5">
				      <label for="max_watts_top" class="control-label">Max PSU Power Unit 2 (70W - 288W) </label>
			      </div>
				<div><input type="text" size="4" onblur="validateSpeed(this)" name="AC1" id="max_watts_1" type="number" step="1" min="70" max="288" value="<?php echo $workmode['AC1']['value']?>"></div>
		      </div>
		      <div class="row">
			      <div class="col-5">
				      <label for="max_watts_top" class="control-label">Max PSU Power Unit 3 (70W - 288W) </label>
			      </div>
				<div><input type="text" size="4" onblur="validateSpeed(this)" name="AC2" id="max_watts_2" type="number" step="1" min="70" max="288" value="<?php echo $workmode['AC2']['value']?>"></div>
		      </div>
		      <div class="row">
			      <div class="col-5">
				      <label for="max_watts_top" class="control-label">Max PSU Power Unit 4 (70W - 288W) </label>
			      </div>
				<div><input type="text" size="4" onblur="validateSpeed(this)" name="AC3" id="max_watts_3" type="number" step="1" min="70" max="288" value="<?php echo $workmode['AC3']['value']?>"></div>
		      </div>
              <div class="row">
                  <div class="col-5">
                      <label for="minimal_rate" class="control-label">Restart miner if rate below </label>
                  </div>
                  <div><input type="text" size="4" name="minimal_rate" id="minimal_rate" type="number" step="1" min="0" max="9999" value="<?php echo file_get_contents("/etc/mg_minimal_rate")?>"></div>
              </div>
              <div class="row">
                  <div class="col-5">
                      <label for="flag_0" class="control-label">FPGA serial speed (0=10MB, 1=5MB, 2=2.5MB) </label>
                  </div>
                  <div><input type="text" size="4" name="flag_0" id="flag_0" type="number" step="1"  value="<?php echo file_get_contents("/etc/mg_flag_0")?>"></div>
              </div>
              <div class="row">
                  <div class="col-5">
                      <label for="flag_1" class="control-label">ExtraFlag: add numbers: 1:no-scaling 2:extranonce.subscribe 4:no-debug 8:alt-bistword</label>
                  </div>
                  <div><input type="text" size="4" name="flag_1" id="flag_1" type="number" step="1" value="<?php echo file_get_contents("/etc/mg_flag_1")?>"></div>
              </div>
              <div class="row">
                  <div class="col-5">
                      <label for="flag_1" class="control-label">Hostname, empty for default (needs reboot)</label>
                  </div>
                  <div><input type="text" size="4" name="hostname" id="hostname" value="<?php echo file_get_contents("/etc/mg_hostname")?>"></div>
              </div>


		      <div class="row hidden">
			      <div class="col-5">
				      <label for="dc2dc_current" class="control-label">DC2DC Limit (50A-180A)</label>
			      </div> 8:
				<div><input type="text" size="4" onblur="validateSpeed(this)" name="DC_AMP" id="dc2dc_current" type="number" step="1" min="50" max="180" value="<?php echo $workmode['DC_AMP']['value']?>"></div>
		      </div>

                  </div>
              </div>
	      <div class="col-offset-3 col-6 buttons">
                  <button type="submit" class="btn btn-default">Save</button>
		</div>
		<div>
                  <button class="btn btn-default" onclick="return toggleCustomSpeedSettings()"><span id="settings_view_name">Advanced</span> Settings</button>
		</div>
          </div>
      </fieldset>
	<script type="text/javascript">
		speedSettings = {
<?php 
$format = explode(' ', WORKMODE_FORMAT);
$modes = array('turbo'=>explode(' ', WORKMODE_TURBO), 'normal'=>explode(' ', WORKMODE_NORMAL), 'quiet'=>explode(' ', WORKMODE_QUIET));
foreach($modes as $mode=>$mode_settings){
	echo $mode.':{';
		foreach($mode_settings as $i => $s) echo $format[$i].':'.$s.', ';
	echo "},\n";
} ?>
		}
		advanced_warning = false;
		function setupSpeedSettings(){
			// check if we have a predefined settings, if not show custom settings
			var predefined = false;
			for(s in speedSettings){
				if(
					<?php $conds = array(); foreach($format as $i=>$f){ $conds[] = 'speedSettings[s].'.$f.'* (($("[name='.$f.']").attr("min") < 1) ? 1000 : 1) == '.$workmode[$f]['value']; } echo implode(' && ', $conds); ?>
				)
				{
					$('input[speed='+s+']').prop('checked', true);
					predefined = true;
					break;
				}
			}
			if(!predefined) toggleCustomSpeedSettings(true);
		}
		function toggleCustomSpeedSettings(force){
			if(!force && !advanced_warning && $('#settings_view_name').text() == "Advanced"){
				advanced_warning = true;
				bootbox.confirm("Choosing a manual voltage setting can lead to overheating the unit, which can damage your miner or cause it to restart unexpectedly.<br/><br/>Do you want to continue?", 
					function(confirm){
						if(confirm) toggleCustomSpeedSettings();
						else advanced_warning = false;
					});
					return false;
			}
			$('form#speed_settings .view-alternative').toggle();
			$('#settings_view_name').text($('#settings_view_name').text()=="Advanced"?"Basic":"Advanced");
			return false;
		}
		function setCustomSpeed(e){
			var speed = $(e).attr('speed'); 	
			$('#speed_settings input, #speed_settings select').each(function(){
				if(speedSettings[speed].hasOwnProperty($(this).attr('name'))) $(this).val(speedSettings[speed][$(this).attr('name')]);
			});
		}
		function validateSpeed(e){
			if(parseFloat($(e).val()) < parseFloat($(e).attr('min'))) $(e).val($(e).attr('min'));
			if(parseFloat($(e).val()) > parseFloat($(e).attr('max'))) $(e).val($(e).attr('max'));
		}
		function saveCustomSpeed(){
			if($('#speed_settings .view-alternative.basic').is(':visible')) setCustomSpeed($('#speed_settings .view-alternative.basic input:checked')[0]);
			if($('#maximum_voltage').val() < $('#minimum_voltage_top').val()){
				bootbox.alert("Maximum voltage must be greater than start value.");
				return false;
			}
            if($('#maximum_voltage').val() < $('#minimum_voltage_bot').val()){
                bootbox.alert("Maximum voltage must be greater than start value.");
                return false;
            }
			return true;	
		}
	</script>
  </form>
  <form name="speedSched" action="" method="post" class="form-horizontal">
	<input type="hidden" name="voltSched" />
      <fieldset>
	<legend>Miner Speed Scheduling (experimental)</legend>
          <div class="form-group">
              <div class="col-lg-9 col-offset-3">
                  <div class="">
	
<?php
	echo '<div class="jobs-container-all" '.(array_key_exists('*', $schedule)?'':'style="display:none;"').'>';
	echo '<h4>Every Day</h4><div class="day-all">';
	if(array_key_exists('*', $schedule)) foreach($schedule['*']as $time=>$cmd){
	echo '<div class="job">'.schedule_form_element(CRON_GROUP_START_VOLTAGE, 'all', $time, $cmd).'<span class="delete" onclick="removeCron(this)">X</span></div>';
	}
	echo '</div><hr/>';
	echo '</div>';
	unset($schedule['*']);
	$days = array('Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday');
	foreach($days as $day_index=>$day){
		echo '<div class="jobs-container-'.$day_index.'" '.(array_key_exists($day_index, $schedule)?'':'style="display:none;"').'>';
		echo '<h4>'.$day.'</h4><div class="day-'.$day_index.'">';
	if(array_key_exists($day_index, $schedule) ) foreach($schedule[$day_index] as $time=>$cmd){
		echo '<div class="job">'.schedule_form_element(CRON_GROUP_START_VOLTAGE, $day_index, $time, $cmd).'<span class="delete" onclick="removeCron(this)">X</span></div>';
	}
		echo '</div><hr/></div>';
}?> 
		<div class="jobs-container-new">
		<h4>New</h4>
		<div class="job">
		<span class="days">On <select multiple="multiple" class="new_day multiple"><option value="all" selected="selected">All Days</option><?php foreach($days as $k=>$v) echo '<option value="'.$k.'">'.$v.'</option>'; ?></select></span>
		<?php echo schedule_form_element(CRON_GROUP_START_VOLTAGE); ?>
			<span class="add" onclick="addCron(this)">+</span></div>
		  </div>
                  <p><br/><button type="submit" class="btn btn-default" onclick="return saveCrons(this)">Save</button></p>
	      </div>
	      </div>
	</div>

      </fieldset>
  </form>
<!-- ######################## -->

<!-- ######################## Network -->
  <form name="network" action="/settings.php" method="post" class="form-horizontal">
    <fieldset>
      <legend>Network settings</legend>
      <div class="form-group">
        <div class="col-lg-6 col-offset-3">
          <div class="checkbox">
            <input type='hidden' value='false' name='dhcpEnable'>
            <label>
              <input type="checkbox" <?php echo $eth_settings['dhcp']?"checked":""; ?> value="true" id="dhcpEnable" name="dhcpEnable"> Use DHCP
            </label>
          </div>
        </div>
	<div class="col-lg-3">
    <a class="btn btn-default" href="/wifi.php">WiFi networks</a>
	<?php include('widgets/led_blinker.php'); ?>
	</div>
      </div>
      <div class="form-group dhcp-enabled <?php echo !$eth_settings['dhcp']?"":"collapse"; ?>">
        <label for="ipaddress" class="control-label col-lg-3">LAN IP address</label>
        <div class="col-lg-9">
          <input type="text" value="<?php echo $eth_settings['ipaddress'] ?>" id="ipaddress" name="ipaddress" class="form-control" placeholder="192.x.x.x" onblur="checkIP(this)">
        </div>
      </div>
      <div class="form-group dhcp-enabled <?php echo !$eth_settings['dhcp']?"":"collapse"; ?>">
        <label for="subnet" class="control-label col-lg-3">LAN Subnet</label>
        <div class="col-lg-9">
          <input type="text" value="<?php echo $eth_settings['subnet'] ?>" id="subnet" name="subnet" class="form-control" placeholder="255.255.255.0" onblur="checkIP(this)">
        </div>
      </div>

<?php
    if(trim(exec("iwgetid wlan0 --raw")) != "")
        $disabled = "";
    else
        $disabled = "disabled";
?>

      <div class="form-group dhcp-enabled <?php echo !$wlan_settings['dhcp']?"":"collapse"; ?>">
        <label for="wifi_ipaddress" class="control-label col-lg-3 <?php echo $disabled ?>">WiFi IP address</label>
        <div class="col-lg-9">
          <input type="text" <?php echo $disabled ?> value="<?php echo $wlan_settings['ipaddress'] ?>" id="wifi_ipaddress" name="wifi_ipaddress" class="form-control" placeholder="192.x.x.x" onblur="checkIP(this)">
        </div>
      </div>
      <div class="form-group dhcp-enabled <?php echo !$wlan_settings['dhcp']?"":"collapse"; ?>">
        <label for="wifi_subnet" class="control-label col-lg-3 <?php echo $disabled ?>">WiFi Subnet</label>
        <div class="col-lg-9">
          <input type="text" <?php echo $disabled ?> value="<?php echo $wlan_settings['subnet'] ?>" id="wifi_subnet" name="wifi_subnet" class="form-control" placeholder="255.255.255.0" onblur="checkIP(this)">
        </div>
      </div>

      <div class="form-group dhcp-enabled <?php echo !$eth_settings['dhcp']?"":"collapse";?>">
          <label for="gateway" class="control-label col-lg-3">Gateway</label>
          <div class="col-lg-9">
              <input type="text" value="<?php echo $eth_settings['gateway'] ?>" id="gateway" name="gateway" class="form-control" placeholder="192.x.x.1" onblur="checkIP(this)">
          </div>
      </div>

      <div class="form-group dhcp-enabled <?php echo !$eth_settings['dhcp']?"":"collapse";?>">
        <label for="dns1" class="control-label col-lg-3">DNS</label>
        <div class="col-lg-9">
          <input type="text" value="<?php echo $eth_settings['dns1'] ?>" id="dns1" name="dns1" class="form-control" placeholder="8.8.8.8" onblur="checkIP(this)">
        </div>
      </div>

        <div class="form-group">
            <div class="col-lg-9 col-offset-3">
                <div class="form-group dhcp-enabled <?php echo !$eth_settings['dhcp']?"":"collapse";?>">
                    <p class="help-block alert">Note that incorrect settings may make your miner unavailable. <br/>Change this setting only if you are sure this is what you want.<!--<br/><br/>WiFi settings will be enabled only if you plugged a WiFi USB dongle and connected to a WiFi network via the "WiFi networks" button.--></p>
                </div>

                <button type="submit" class="btn btn-default" onclick="return a=[],$('input.form-control:visible', $(this).parents('form')).each(function(){a.push(checkIP(this));}), a.reduce(function(a,b){return a&&b;});">Save</button>
            </div>
        </div>

    </fieldset>
  </form>
<!-- ######################## -->

<form name="password" action="/settings.php" method="post" class="form-horizontal">
    <fieldset>
        <legend>HW control</legend>
        <div class="form-group">
            <label for="asicControl" class="control-label col-lg-3">Disable ASICs</label>
            <div class="col-lg-3">
                <a class="btn btn-default asics_control opener">ASICS Control Panel</a>
            </div>
        </div>
    </fieldset>
</form>
  <!-- ######################## Passwords -->
  <form name="password" action="/settings.php" method="post" class="form-horizontal">
      <fieldset>
          <legend>UI password</legend>
          <div class="form-group">
              <label for="userPassword" class="control-label col-lg-3">New password</label>
              <div class="col-lg-9">
                  <input type="password" placeholder="New password" id="userPassword1" name="userPassword1" class="form-control" onblur="checkPass('userPassword', 'submitPassword');">
                  <br />
                  <input type="password" placeholder="Repeat Password" id="userPassword2" name="userPassword2" class="form-control" onblur="checkPass('userPassword', 'submitPassword');">
                  <br />
                  <p class="help-block alert alert-info">It is <b>highly</b> recommended that you create a secure password, but if you forget your password you will need to perform a recovery with micro-SDcard.</p>
                  <button type="submit" id="submitPassword" class="btn btn-default">Save</button>
              </div>

          </div>

      </fieldset>
  </form>

  <form name="miner_password" action="/settings.php" method="post" class="form-horizontal">
      <fieldset>
          <legend>SSH password</legend>
          <div class="form-group">
              <label for="rootPassword" class="control-label col-lg-3">New password</label>
              <div class="col-lg-9">
                  <input type="password" placeholder="New password" id="rootPassword1" name="rootPassword1" class="form-control" onblur="checkPass('rootPassword', 'submitRootPassword');">
                  <br />
                  <input type="password" placeholder="Repeat Password" id="rootPassword2" name="rootPassword2" class="form-control" onblur="checkPass('rootPassword', 'submitRootPassword');">
                  <br />
                  <p class="help-block alert alert-info">It is <b>highly</b> recommended that you create a secure password, but if you forget your password and you want to access your miner with ssh you will need to perform a recovery with micro-SDcard.</p>
                  <button type="submit" id="submitRootPassword" class="btn btn-default">Save</button>
              </div>

          </div>

      </fieldset>
  </form>

  <!-- ######################## Timezone -->

  <form name="timezone" action="/settings.php" method="post" class="form-horizontal">
      <fieldset>
          <legend>TimeZone</legend>
          <div class="form-group">
              <label for="userTimezone" class="control-label col-lg-3">Timezone</label>
              <div class="col-lg-9">
                  <?php echo $tzselect ?>
                  <p class="help-block">Miner thinks it is now <?php echo date('D, d M Y H:i:s T') ?></p>
                  <button type="submit" class="btn btn-default">Save</button>
              </div>
          </div>
      </fieldset>
  </form>

  <!-- ######################## Timezone >

  <form name="max_watts" action="/settings.php" method="post" class="form-horizontal">
      <fieldset>
          <legend>PSU</legend>
          <div class="form-group">
              <label for="userTimezone" class="control-label col-lg-3">Maximum Power Consumption</label>
              <div class="col-lg-9">
                <div><input type="radio" name="max_watts" id="max_watts_1150" value="1150" < ?php if($max_watts == 1150) echo 'checked="checked"'; ?>> <label for="max_watts_1150">1150 Watts</label></div>
                <div><input type="radio" name="max_watts" id="max_watts_1200" value="1200" < ?php if($max_watts == 1200) echo 'checked="checked"'; ?>> <label for="max_watts_1200">1200 Watts</label></div>
                <div><input type="radio" name="max_watts" id="max_watts_1250" value="1250" < ?php if($max_watts == 1250) echo 'checked="checked"'; ?>> <label for="max_watts_1250">1250 Watts</label></div>
< ?php   if($max_watts != 1250 && $max_watts != 1200 && $max_watts != 1150){ ?>

		<div><input type="radio" name="max_watts" id="max_watts_< ?php echo $max_watts; ?>" value="< ?php echo $max_watts; ?>" checked="checked"> <label for="max_watts_< ?php echo $max_watts; ?>">< ?php echo $max_watts; ?> Watts (Custom setting found)</label></div>
< ?php } ?>
                  <button type="submit" class="btn btn-default">Save</button>
              </div>
          </div>
      </fieldset>
  </form>
  <!-- ######################## -->

<!--  <form name="mining" action="/settings.php" method="post" class="form-horizontal">
    <fieldset>
      <legend>Mining</legend>
      <div class="form-group">
        <label for="miningExpDev" class="control-label col-lg-3">Expected Devices</label>
        <div class="col-lg-9">
          <input type="number" value="< ?php /*echo $settings['miningExpDev'] */?>" id="miningExpDev" name="miningExpDev" class="form-control">
          <p class="help-block">
            If the count of active devices falls below this value, an alert will be sent.
          </p>
        </div>
      </div>
      <div class="form-group">
        <label for="miningExpHash" class="control-label col-lg-3">Expected Hashrate</label>
        <div class="col-lg-9">
          <div class="input-group">
            <input type="number" value="< ?php /*echo $settings['miningExpHash'] */?>" id="miningExpHash" name="miningExpHash" class="form-control">
            <span class="input-group-addon">MH/s</span>
          </div>
          <p class="help-block">
            If the hashrate falls below this value an alert will be sent.
          </p>
        </div>
      </div>
      <div class="form-group">
        <div class="col-lg-9 col-offset-3">
          <button type="submit" class="btn btn-default">Save</button>
        </div>
      </div>
    </fieldset>
-->
<!-- ######################## Alerts -->

<!--  <form name="alerts" action="/settings.php" method="post" class="form-horizontal">
    <fieldset>
      <legend>Alerts</legend>
      <div class="form-group">
        <div class="col-lg-9 col-offset-3">
          <div class="checkbox">
            <input type='hidden' value='false' name='alertEnable'>
            <label>
              <input type="checkbox" < ?php /*echo $settings['alertEnable']?"checked":""; */?> value="true" id="alertEnable" name="alertEnable"> Enable e-mail alerts
            </label>
          </div>
        </div>
      </div>
      <div class="form-group alert-enabled <?php /*echo $settings['alertEnable']?"":"collapse"; */?>">
        <label for="alertDevice" class="control-label col-lg-3">Device Name</label>
        <div class="col-lg-9">
          <input type="text" value="<?php /*echo $settings['alertDevice'] */?>" id="alertDevice" name="alertDevice" class="form-control" placeholder="MinePeon">
        </div>
      </div>
      <div class="form-group alert-enabled <?php /*echo $settings['alertEnable']?"":"collapse"; */?>">
        <label for="alertEmail" class="control-label col-lg-3">E-mail</label>
        <div class="col-lg-9">
          <input type="email" value="<?php /*echo $settings['alertEmail'] */?>" id="alertEmail" name="alertEmail" class="form-control" placeholder="example@example.com">
        </div>
      </div>
      <div class="form-group alert-enabled <?php /*echo $settings['alertEnable']?"":"collapse"; */?>">
        <label for="alertSmtp" class="control-label col-lg-3">SMTP Server</label>
        <div class="col-lg-9">
          <input type="text" value="<?php /*echo $settings['alertSmtp'] */?>" id="alertSmtp" name="alertSmtp" class="form-control" placeholder="smtp.myisp.com">
          <p class="help-block">Please choose your own SMTP server.</p>
        </div>
      </div>
	  
	  <div class="form-group">
        <div class="col-lg-9 col-offset-3">
          <div class="checkbox" >
            <input type='hidden' value='false' name='alertSMTPAuth'>
            <label class="form-group alert-enabled ">
              <input type="checkbox"  class="form-group alert-enabled " <?php /*echo $settings['alertSMTPAuth']?"checked":""; */?> value="true" id="alertSMTPAuth" name="alertSMTPAuth"> Use SMTP Auth
            </label>
          </div>
        </div>
      </div>
	  
	  <div class="form-group smtpauth-enabled alert-enabled <?php /*echo $settings['alertSMTPAuth']?"":"collapse"; */?>">
        <label for="alertSmtp" class="control-label col-lg-3">SMTP Auth Username</label>
        <div class="col-lg-9">
          <input type="text" value="<?php /*echo $settings['alertSmtpAuthUser'] */?>" id="alertSmtpAuthUser" name="alertSmtpAuthUser" class="form-control">
        </div>
      </div>
	  
	  <div class="form-group smtpauth-enabled alert-enabled <?php /*echo $settings['alertSMTPAuth']?"":"collapse"; */?>">
        <label for="alertSmtp" class="control-label col-lg-3">SMTP Auth Password</label>
        <div class="col-lg-9">
          <input type="text" value="<?php /*echo $settings['alertSmtpAuthPass'] */?>" id="alertSmtpAuthPass" name="alertSmtpAuthPass" class="form-control">
        </div>
      </div>

	  <div class="form-group smtpauth-enabled alert-enabled <?php /*echo $settings['alertSMTPAuth']?"":"collapse"; */?>">
        <label for="alertSmtp" class="control-label col-lg-3">SMTP Auth Port</label>
        <div class="col-lg-9">
          <input type="text" value="<?php /*echo $settings['alertSmtpAuthPort'] */?>" id="alertSmtpAuthPort" name="alertSmtpAuthPort" class="form-control">
        </div>
      </div>
	  
      <div class="form-group">
        <div class="col-lg-9 col-offset-3">
          <button type="submit" class="btn btn-default">Save</button>
        </div>
      </div>
    </fieldset>
  </form>-->

<!-- ######################## -->

<!--  <form name="minerStartup" action="/settings.php" method="post" class="form-horizontal">
    <fieldset>
      <legend>Miner Startup Settings</legend>
      <div class="form-group">
        <label for="minerSettings" class="control-label col-lg-3">Settings</label>
        <div class="col-lg-9">
          <div>
			<textarea rows="4" cols="120" id="minerSettings" name="minerSettings"><?php /*echo $minerStartup */?></textarea>
          </div>
        </div>
      </div>
      <div class="form-group">
        <div class="col-lg-9 col-offset-3">
          <button type="submit" class="btn btn-default">Save</button>
		  <button type="button" type="bfgminer" onclick="minerSwitch('bfgminer')" class="btn btn-default">Default bfgminer</button>
		  <button type="button" type="cgminer" onclick="minerSwitch('cgminer')" class="btn btn-default">Default cgminer</button>
		  <button type="button" type="cgminer" onclick="minerSwitch('cgminer-HEXu')" class="btn btn-default">cgminer-HEXu</button>
		  <script language="javascript" type="text/javascript">
			function minerSwitch(miner) {
			  if (miner == "cgminer") {
				document.getElementById('minerSettings').value = "#!/bin/bash\nsleep 10\n/usr/bin/screen -dmS miner /opt/minepeon/bin/cgminer -c /opt/minepeon/etc/miner.conf\n";
			  } 
			  if (miner == "bfgminer") {
				document.getElementById('minerSettings').value = "#!/bin/bash\nsleep 10\n/usr/bin/screen -dmS miner /opt/minepeon/bin/bfgminer -S all -c /opt/minepeon/etc/miner.conf\n";
			  }
			  if (miner == "cgminer-HEXu") {
				document.getElementById('minerSettings').value = "#!/bin/bash\nsleep 10\n/usr/bin/screen -dmS miner /opt/minepeon/bin/cgminer-HEXu -c /opt/minepeon/etc/miner.conf\n";
			  } 
			}
		  </script>
		  <p class="help-block">
            Enter you own miner parameters or select a default bfgminer or cgminer configuration.  
			You will need to press Save and then reboot the miner when you finish.<br />
			If you intend to enable the cgminer-HEXu option <a href="http://minepeon.com/index.php/Cgminer-HEXu">please read this page for instructions.</a>
          </p>
        </div>
      </div>
    </fieldset>
  </form>
-->
<!-- ######################## -->

<!--  <form name="donation" action="/settings.php" method="post" class="form-horizontal">
    <fieldset>
      <legend>Donation</legend>
      <div class="form-group">
        <label for="donateAmount" class="control-label col-lg-3">Donation</label>
        <div class="col-lg-9">
          <div class="checkbox">
            <input type='hidden' value='false' name='donateEnable'>
            <label>
              <input type="checkbox" <?php /*echo $settings['donateEnable']?"checked":""; */?> value="true" id="donateEnable" name="donateEnable"> Enable donation
            </label>
          </div>
          <div class="donate-enabled <?php /*echo $settings['donateEnable']?"":"collapse"; */?>">
            <div class="input-group">
              <input type="number" value="<?php /*echo $settings['donateAmount'] */?>" placeholder="Donation minutes" id="donateAmount" name="donateAmount" class="form-control">
              <span class="input-group-addon">minutes per day</span>
            </div>
          </div>
        </div>
      </div>
      <div class="form-group">
        <div class="col-lg-9 col-offset-3">
          <button type="submit" class="btn btn-default">Save</button>
        </div>
      </div>
    </fieldset>
  </form>
-->

<!-- ######################## Backup -->
  <form name="backup" action="/settings.php" method="post" enctype="multipart/form-data" class="form-horizontal">
    <fieldset>
      <legend>Report</legend>
     <div class="form-group">
        <div class="col-lg-9 col-offset-3">
		  <a class="btn btn-default" href="/report.php">Report</a>
		  <p class="help-block">The report will contain all of your settings and statistics.</p>
        </div>
      </div>

      <!-- div class="form-group">
		<div class="col-lg-9 col-offset-3">
		  <input type="file" name="file" id="file" class="btn btn-default" onchange="enableRestore()" data-input="false">
		</div>
	  </div>
	  <div class="form-group">
		<div class="col-lg-9 col-offset-3">
		  <button type="submit" id="restore_button" name="submit" class="btn btn-default disabled" onclick="return  document.getElementById('file').value != ''">Restore</button>
		  <p class="help-block">Restoring a configuration will cause your miner to reboot.</p>
		</div>
      </div -->
    </fieldset>
  </form>
<!-- ######################## -->

  <!-- ######################## Reset stats -->
  <form name="reset" action="/settings.php" method="post" enctype="multipart/form-data" class="form-horizontal">
      <fieldset>
          <legend>Factory reset</legend>
          <div class="form-group">
              <div class="col-lg-9 col-offset-3">
                  <a name="resetfactory" id="resetfactory" class="btn btn-default miner-action" onclick="return confirmClick(this);" href="/reset_to_factory.php?except=">Reset to factory settings</a>
                  <p class="help-block">
                      <input type="checkbox" value="cgminer.conf.template" onchange="changeUrl(this, '#resetfactory')" />Keep cgminer.conf
                      <input type="checkbox" value="mg_disabled_asics_sp20" onchange="changeUrl(this, '#resetfactory')" />Keep mg_disabled_asics
                  </p>
                  <p class="help-block">This will restore your miner settings to the factory default ones!</p>
              </div>
<?php if(file_exists("/mnt/config/etc/bin/miner_gate_".strtolower(str_replace('x', '0', $model_class)))){ ?>
              <div class="col-lg-9 col-offset-3">
		  <a name="reset_minerarm" class="btn btn-default miner-action" onclick="return confirmClick(this);" href="/files.php?op=del&dir=config&files=bin/miner_gate_<?php echo strtolower(str_replace('x', '0', $model_class)) ?>">Restore miner_gate_arm</a>
              </div>
<?php } ?>
          </div>
      </fieldset>
  </form>
  <!-- ######################## -->

  <!-- ######################## Device Registration -->
  <form name="reset" action="settings.php" method="post" enctype="multipart/form-data" class="form-horizontal">
      <fieldset>
          <legend>Device Registration</legend>
          <div class="form-group">
              <div class="col-lg-9 col-offset-3">
<div>
            <label class="form-group alert-enabled " for="setRegisterDevice">
		<input type="hidden" name="setRegisterDevice" value="" />
	    <input type="checkbox"  <?php echo (!array_key_exists('setRegisterDevice', $settings) || $settings['setRegisterDevice'] == "true")?"checked":""; ?> id="setRegisterDevice" name="setRegisterDevice" value="true"/>
	    Send device data to Spondoolies-tech.com (enabled by default).
            </label>
<br/>
		<input class="btn btn-default" value="Save" type="submit" />
             </div>
              </div>
          </div>
      </fieldset>
  </form>
  <!-- ######################## -->

  <!-- ######################## SSL control >
  <form name="reset" action="settings.php" method="post" enctype="multipart/form-data" class="form-horizontal">
      <fieldset>
          <legend>SSL Enforcement</legend>
          <div class="form-group">
              <div class="col-lg-9 col-offset-3">
<div>
        <label class="form-group alert-enabled " for="setSSLEnforce">
		<input type="hidden" name="setSSLEnforce" value="" />
	    <input type="checkbox"  < ?php echo (!array_key_exists('setSSLEnforce', $settings) || $settings['setSSLEnforce'] == "true")?"checked":""; ?> id="setSSLEnforce" name="setSSLEnforce" value="true"/>
	    Enforce SSL login (disabled by default).
            </label>
<br/>
		<input class="btn btn-default" value="Save" type="submit" />
             </div>
              </div>
          </div>
      </fieldset>
  </form>
  <!-- ######################## -->

<script type="text/javascript" id="js">

    function confirmClick(target) {
        bootbox.confirm("Are you sure?", function(result) {
            if (!result) return;
            window.location.replace($(target).attr('href'));
        });
	return false;
    }

   function changeUrl(source, target){
	var url = $(target).attr('href');
	if($(source).is(':checked')){
		url += ","+$(source).val();
	}else{
		url = url.replace(new RegExp($(source).val()), '');
	}
	$(target).attr('href', url);
    }

  function checkPass(id, submitButton)
{
    //Store the password field objects into variables ...
    var pass1 = document.getElementById(id+'1');
    var pass2 = document.getElementById(id+'2');
    //Store the Confimation Message Object ...
    var message = document.getElementById('confirmMessage');
	var submit = document.getElementById('submitPassword');
    //Set the colors we will be using ...
    var goodColor = "#66cc66";
    var badColor = "#ff6666";
    //Compare the values in the password field 
    //and the confirmation field
    if(pass1.value == pass2.value){
        //The passwords match. 
        //Set the color to the good color and inform
        //the user that they have entered the correct password 
		document.getElementById(submitButton).disabled = false;
        pass2.style.backgroundColor = goodColor;
        message.style.color = goodColor;
        message.innerHTML = "Passwords Match!"
    }else{
        //The passwords do not match.
        //Set the color to the bad color and
        //notify the user.
		document.getElementById(submitButton).disabled = true;
        pass2.style.backgroundColor = badColor;
        message.style.color = badColor;
        message.innerHTML = "Passwords Do Not Match!"
    }
}
function checkIP(e){
    if ($(e).is(':disabled')) return true;

	$(e).val($(e).val().trim());
	if(!$(e).val().match(/^\d{0,3}\.\d{0,3}(\.\d{0,3}\.\d{0,3})?$/)){
		$(e).addClass('invalid alert').attr({title:'Invalid IP Address'});
	}else{
		$(e).removeClass('invalid alert').attr({title:""});
	}

	return !$(e).hasClass('invalid');
}

  function enableRestore(){
  	$('#restore_button').removeClass('disabled');
  }
	function removeCron(e){
		$(e).parents('.job').remove();
	}
	function addCron(e){
		if($(e).hasClass('delete')) return removeCron(e);
		var p = $(e).parents('.job');
		var cmd = p.find('.cmd');
		if(cmd.val() != "0" && (parseFloat(cmd.attr("min")) > cmd.val() || parseFloat(cmd.attr("max")) < cmd.val())){
			return false;
		}
		var days = $('select.multiple', p).val();
		if(days[0] == "all") days = ["all"]; // remove individual selected days if "all days" was selected
		for(d in days){
			var job = p.clone();
			$('select', p).each(function(){$('.'+$(this).attr('class'), job).val($(this).val()); });
			$('.day_field', job).val(days[d]);
			$('.add', job).removeClass('add').addClass('delete').text('X');
			$('.days', job).remove();
			$('.day-'+days[d]).append(job);
			$('.jobs-container-'+days[d]).show();
			delete job;
		}
		cron_ready_to_add = false;
	}
	function saveCrons(e){
		if(cron_ready_to_add) bootbox.confirm('You have selected a new Cron, but did not add it to the list. If you want to save this cron, click "Cancel", and then click on the "+" to add your new cron to the list.', function(s){
			if(s) $(e).parents('form').submit();
		});
		else $(e).parents('form').submit();
		return false;
	}
</script>

<div class="hidden">
    <div class="asics_control container">
        <?php include('widgets/asics_control.html'); ?>
    </div>
</div>

<?php
include('foot.php');
?>


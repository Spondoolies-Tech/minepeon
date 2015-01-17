<?php
$cur_ver = exec('cat '.CURRENT_VERSION_FILE);
$latest_ver = exec('cat '.LATEST_VERSION_FILE);

//Update watchdog monitored file (to prevent reboots)
//file_put_contents('/var/run/dont_reboot', "php_active_menu");

?>
<div class="navbar">
  <div class="container">
    <a class="navbar-brand" href="/"><?php echo $full_model_name ?></a>
    <ul class="nav navbar-nav">
<?php if (($limited_access & 1) == 0) { ?>
      <li><a href="/pools.php">Pools</a></li>
<?php
    }
    if (($limited_access & 2) == 0) {
?>
    <li><a href="/settings.php">Settings</a></li>

    <?php  if(version_compare($cur_ver, $latest_ver) < 0){ ?>
        <li><a href="/firmware.php">Firmware Upgrade <p class="badge badge-info">New!</p></a></li>
    <?php }else{ ?>
        <li><a href="/firmware.php">Firmware Upgrade</a></li>
    <?php }  ?>
<?php } ?>

      <li><a href="/asics.php">ASIC Stats</a></li>
      <!--li><a href="/carbon.php">Calc</a></li-->
<?php if ($model_class=="SP1x") { ?>
	<li><a href="/hw.php">DCR</a></li>
<?php } else { ?>
        <li><a href="/log.php">Events</a></li>
<?php }  ?>
<!--      <li><a href="about.php">About</a></li>
      <li><a href="contact.php">Contact</a></li>
-->      <li><a href="/license.php">License</a></li>
        <li><a href="/help.php">Help</a></li>
    </ul>
	<a href="http://www.spondoolies-tech.com" target="_blank"><img class="logo responsive pull-right" src="img/SpondooliesLogo-transparent-small.png" /></a>
  </div>
</div>


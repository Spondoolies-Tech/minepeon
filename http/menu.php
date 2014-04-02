<?php
$cur_ver = exec('cat '.CURRENT_VERSION_FILE);
$latest_ver = exec('cat '.LATEST_VERSION_FILE);
?>
<div class="navbar">
  <div class="container">
    <a class="navbar-brand" href="/">SP10 Dawson</a>
    <ul class="nav navbar-nav">
      <!--<li><a href="/">Status</a></li>-->
      <li><a href="/pools.php">Pools</a></li>
      <li><a href="/settings.php">Settings</a></li>
      <li><a href="/firmware.php">Firmware Upgrade</a></li>
<?php /* if($cur_ver == $latest_ver){ ?>
	<li><span class="disabled" title="You already have the latest version of the firmware installed.">Firmware Upgrade</span></li>
<?php }else{ ?>
      <li><a class="alert" href="/firmware.php" title="Upgrade to version <?php echo $latest_ver;?> (Current version is <?php echo $cur_ver?>.)" >Firmware Upgrade</a><span class="badge alert" style="position:absolute;top:4px;right:0;"><?php echo $latest_ver;?></span></li>
<?php } */ ?>
      <li><a href="/asics.php">ASIC Stats</a></li>
<!--      <li><a href="about.php">About</a></li>
      <li><a href="contact.php">Contact</a></li>
-->      <li><a href="/license.php">License</a></li>
    </ul>
	<a href="http://www.spondoolies-tech.com" target="_blank"><img class="logo responsive pull-right" src="img/SpondooliesLogo-transparent-small.png" /></a>
  </div>
</div>


<?php
$cur_ver = exec('cat '.CURRENT_VERSION_FILE);
$latest_ver = exec('cat '.LATEST_VERSION_FILE);
?>
<div class="navbar">
  <div class="container">
    <a class="navbar-brand" href="/">SP10 Dawson</a>
    <ul class="nav navbar-nav">
      <li><a href="/pools.php">Pools</a></li>
      <li><a href="/settings.php">Settings</a></li>
<?php  if(version_compare($cur_ver, $latest_ver) < 0){ ?>
    <li><a href="/firmware.php">Firmware Upgrade <p class="badge badge-info">New!</p></a></li>
<?php }else{ ?>
    <li><a href="/firmware.php">Firmware Upgrade</a></li>
<?php }  ?>
      <li><a href="/asics.php">ASIC Stats</a></li>
      <li><a href="/hw.php">DCR</a></li>
<!--      <li><a href="about.php">About</a></li>
      <li><a href="contact.php">Contact</a></li>
-->      <li><a href="/license.php">License</a></li>
    </ul>
	<a href="http://www.spondoolies-tech.com" target="_blank"><img class="logo responsive pull-right" src="img/SpondooliesLogo-transparent-small.png" /></a>
  </div>
</div>


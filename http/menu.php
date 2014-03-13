<?php require_once('constants.php');?>
<div class="navbar">
  <div class="container">
    <a class="navbar-brand" href="">SpondMiner</a>
    <ul class="nav navbar-nav">
      <li><a href="/">Status</a></li>
      <li><a href="pools.php">Pool</a></li>
      <li><a href="settings.php">Settings</a></li>
<?php if(file_exists(FIRMWARE_UPGRADE_SCRIPT)){ ?>
      <li><a href="firmware.php">Firmware Upgrade</a></li>
<?php } ?>
<!--      <li><a href="about.php">About</a></li>
      <li><a href="contact.php">Contact</a></li>
-->      <li><a href="license.php">License</a></li>
    </ul>
  </div>
</div>


<?php

require('miner.inc.php');
include('global.inc.php');
include('functions.inc.php');

/*
//Moved to Cron-based PHP CLI generation
create_graph("mhsav-hour.png", "-1h", "Last Hour");
create_graph("mhsav-day.png", "-1d", "Last Day");
create_graph("mhsav-week.png", "-1w", "Last Week");
create_graph("mhsav-month.png", "-1m", "Last Month");
create_graph("mhsav-year.png", "-1y", "Last Year");

function create_graph($output, $start, $title) {
  $RRDPATH = '/mnt/mmc-config/rrd/';
  $options = array(
    "--slope-mode",
    "--start", $start,
    "--title=$title",
    "--vertical-label=Hash per second",
    "--lower=0",
    "DEF:hashrate=" . $RRDPATH . "hashrate.rrd:hashrate:AVERAGE",
    "CDEF:realspeed=hashrate,1000,*",
    "LINE2:realspeed#FF0000"
    );
  $ret = rrd_graph("/tmp/rrd/" . $output, $options);
  if (! $ret) {
    echo "<b>Graph error: </b>".rrd_error()."\n";
  }
}*/

// A few globals for the title of the page
$G_MHSav = 0;

//MinePeon temperature
$mpTemp = explode(" ", file_get_contents("/var/run/mg_rate_temp"));

//MinePeon Version
$version = "SP10 Dawson";

//MinePeon CPU load
$mpCPULoad = sys_getloadavg();

if (isset($_GET['url']) and isset($_GET['user'])) {

	$poolMessage = "Pool  Change Requested " . $_GET['url'] . $_GET['user'];

	//echo $poolMessage;

	promotePool($_GET['url'], $_GET['user']);

}

try{
	$stats = miner("devs", "");
	$status = $stats['STATUS'];
	$devs = $stats['DEVS'];
	$summary = miner("summary", "");
	$pools = miner("pools", "");
	$running = true;
}catch(Exception $e){
	$status = "NA";
	$devs = array();
	$summary = array(
		"SUMMARY"=>array(array(
			"BestShare"=>"NA",
			"Elapsed" => null
			)),
		"STATUS" => array(array(
			"Description" => "NA"
			)));
	$pools = array();
	$running = false;
	$error = $e->getMessage();
}

include('head.php');
include('menu.php');
?>
<div class="container">
  <h3 id="miner-header-txt">SP10 Miner</h3><br>
  <?php
  if (file_exists('/tmp/rrd/mhsav-hour.png')) {
  ?>
  <p class="text-center">
    <img src="rrd/mhsav-hour.png" alt="mhsav.png" />
    <img src="rrd/mhsav-day.png" alt="mhsav.png" /><br/>
    <!--a href="#" id="chartToggle">Display extended charts</a-->
  </p>
  <!--p class="text-center collapse chartMore">
    <img src="rrd/mhsav-week.png" alt="mhsav.png" />
    <img src="rrd/mhsav-month.png" alt="mhsav.png" />
  </p>
  <p class="text-center collapse chartMore">
    <img src="rrd/mhsav-year.png" alt="mhsav.png" />
  </p--!>
  <?php
  } else {
  ?>
  <center><h1>Processing history</h1></center>
  <center><h2>Amazing graphs will be available shortly</h2></center>
  <?php
  }
	if(!$running){
echo "<center class='alert alert-info'><h1>".$error."</h1></center>";
	}
  ?>
  <div class="row">
    <div class="col-lg-4">
      <dl class="dl-horizontal">
        <dt>Temp Front / Back</dt>
        <dd><?php echo $mpTemp[2]; ?> <small>&deg;C</small> / <?php echo $mpTemp[1]; ?> <small>&deg;C</small>
        <dt>System CPU Load</dt>
        <dd><?php echo $mpCPULoad[0]; ?> <small>[1 min]</small></dd>
        <dd><?php echo $mpCPULoad[1]; ?> <small>[5 min]</small></dd>
        <dd><?php echo $mpCPULoad[2]; ?> <small>[15 min]</small></dd>
      </dl>
    </div>
    <div class="col-lg-4">
      <dl class="dl-horizontal">
        <dt>Best Share</dt>
        <dd><?php echo $summary['SUMMARY'][0]['BestShare']; ?></dd>
        <dt>System Uptime</dt>
        <dd><?php echo secondsToWords(round($uptime[0])); ?></dd>
        <dt>CGMiner Uptime</dt>
        <dd><?php echo secondsToWords($summary['SUMMARY'][0]['Elapsed']); ?></dd>
      </dl>
    </div>
    <div class="col-lg-4">
      <dl class="dl-horizontal">
        <dt>Hardware Version</dt>
        <dd><?php echo $version; ?></dd>
        <dt>FW Version</dt>
        <dd><?php echo(file_get_contents("/fw_ver")) ?></dd>
        <dt>CGMiner Version</dt>
        <dd><?php echo $summary['STATUS'][0]['Description']; ?></dd>
<!--        <dt>Donation Minutes</dt>
        <dd><//?php echo $settings['donateAmount']; ?>
-->      </dl>
    </div>
  </div>
  <center>
    <a class="btn btn-default" href='/restart.php'>Restart CgMiner with RPC</a>
    <a class="btn btn-default" href='/restart.php'>Force Restart CgMiner</a>
    <a class="btn btn-default" href='/reboot.php'>Reboot</a>
    <a class="btn btn-default" href='/halt.php'>ShutDown</a>
  </center>
  <h3>Pools</h3>
  <table id="pools" class="table table-striped table-hover">
    <thead> 
      <tr>
        <th></th>
        <th>URL</th>
        <th>User</th>
        <th>Status</th>
        <th title="Priority">Pr</th>
        <th title="GetWorks">GW</th>
        <th title="Accept">Acc</th>
        <th title="Reject">Rej</th>
        <th title="Discard">Disc</th>
        <th title="Last Share Time">Last</th>       
        <th title="Difficulty 1 Shares">Diff1</th>        
        <th title="Difficulty Accepted">DAcc</th>
        <th title="Difficulty Rejected">DRej</th>
        <th title="Last Share Difficulty">DLast</th>
        <th title="Best Share">Best</th>      
      </tr>
    </thead>
    <tbody>
      <?php if($running) echo poolsTable($pools['POOLS']); 
	    else echo "<div class='alert alert-info'>".$error."</div>";
	?>
    </tbody>
  </table>

  <h3>Statistics</h3>
  <?php echo statsTable($devs); ?>
  <?php
  if ($debug == true) {
	
	echo "<pre>";
	print_r($pools['POOLS']);
	print_r($devs);
	echo "<pre>";
	
  }
  ?>

</div>
<script language="javascript" type="text/javascript">
 
document.title = '<?php echo $G_MHSav; ?>|<?php echo $version; ?>';
 
<?php 
 
// Change screen colour test for alerts
 
/*if ($settings['donateAmount'] < 1) {
	echo 'document.body.style.background = "#FFFFCF"';
}*/

?>

</script>
<?php
include('foot.php');

function statsTable($devs) {
  if(count($devs)==0){
    return "</tbody></table><div class='alert alert-info'>Miner is not ready</div>";
  }

  $devices = 0;
  $MHSav = 0;
  $Accepted = 0;
  $Rejected = 0;
  $HardwareErrors = 0;
  $Utility = 0;

  $tableRow = '<table id="stats" class="table table-striped table-hover stats">
    <thead>
      <tr>
        <th>Name</th>
<!--
        <th>ID</th>
        <th>Temp</th>
-->
        <th>GH/s</th>
        <th>Accepted shares</th>
        <th>Rejected shares</th>
        <th>Errors</th>
        <th>Utility</th>
        <th>Last Share</th>
      </tr>
    </thead>
    <tbody>';

 	$hwErrorPercent = 0;
	$DeviceRejected = 0;

  foreach ($devs as $dev) {
  
	// Sort out valid deceives
	
	$validDevice = true;
 

    // Veird mismatch between us and the pool.
    if (file_exists("/var/run/mg_rate_temp")) {
        $s = file_get_contents("/var/run/mg_rate_temp");
        $s = explode(" ", $s);
    } else {
        $s = array(0,0,0);
    }

    $dev['MHSav'] = intval($s[0]);


	if ((time() - $dev['LastShareTime']) > 500) {
		// Only show devices that have returned a share in the past 5 minutes
        //TODO: Enable on production
		$validDevice = false;
	}

	$temperature = intval($s[1]);

	if ($validDevice) {

		if ($dev['DeviceHardware%'] >= 10 || $dev['DeviceRejected%'] > 5) {
			$tableRow = $tableRow . "<tr class=\"error\">";
		} else {
			$tableRow = $tableRow . "<tr class=\"success\">";
		}
    ?>
    <script type="text/javascript">
        document.getElementById("miner-header-txt").innerText = "<?php echo "Mining Rate: ".round($dev['MHSav']/1000000,2)?>Ths";
        document.getElementById("miner-header-txt").innerHTML = "<?php echo "Mining Rate: ".round($dev['MHSav']/1000000,2)?>Ths";
    </script>
    <?php

	$tableRow = $tableRow . "<td>" . "SP10" . "</td>
      <!-- <td>" . "1" . "</td>
      <td>" . $temperature . "</td> -->
      <td>" . $dev['MHSav'] / 1000 . "</td>
      <td>" . $dev['Accepted'] . "</td>
      <td>" . $dev['Rejected'] . "</td>
      <td>" . $dev['HardwareErrors'] . "</td>
      <td>" . $dev['Utility'] . "</td>
      <td>" . date('H:i:s', $dev['LastShareTime']) . "</td>
      </tr>";

		$devices++;
		$MHSav = $MHSav + $dev['MHSav'];
		$Accepted = $Accepted + $dev['Accepted'];
		$Rejected = $Rejected + $dev['Rejected'];
		$HardwareErrors = $HardwareErrors + $dev['HardwareErrors'];
		$DeviceRejected = $DeviceRejected + $dev['DeviceRejected%'];
		$hwErrorPercent = $hwErrorPercent + $dev['DeviceHardware%'];
		$Utility = $Utility + $dev['Utility'];

		$GLOBALS['G_MHSav'] = $MHSav / 1000 . " GH/s|" . $devices . " DEV";

	}
  }


  $totalShares = $Accepted + $Rejected + $HardwareErrors;
  $tableRow = $tableRow . "
  </tbody>
  <!-- <tfoot>
  <tr>
  <th>Totals</th>
  <th>" . $devices . "</th>
  <th></th>
  <th>" . $MHSav / 1000 . "</th>
  <th>" . $Accepted . "</th>
  <th>" . $Rejected . " [" . "</th>
  <th>" . $HardwareErrors . " [" . "</th>
  <th>" . $Utility . "</th>
  <th></th>
  </tr>
  </tfoot> -->
  </tbody>
  </table>
  ";

  return $tableRow;
}

function secondsToWords($seconds)
{
  $ret = "";

  /*** get the days ***/
  $days = intval(intval($seconds) / (3600*24));
  if($days> 0)
  {
    $ret .= "$days<small> day </small>";
  }

  /*** get the hours ***/
  $hours = (intval($seconds) / 3600) % 24;
  if($hours > 0)
  {
    $ret .= "$hours<small> hr </small>";
  }

  /*** get the minutes ***/
  $minutes = (intval($seconds) / 60) % 60;
  if($minutes > 0)
  {
    $ret .= "$minutes<small> min </small>";
  }

  /*** get the seconds ***/
  $seconds = intval($seconds) % 60;
  if ($seconds > 0) {
    $ret .= "$seconds<small> sec</small>";
  }

  return $ret;
}

function poolsTable($pools) {

// class="success" error warning info

  $poolID = 0;

  $table = "";
  
  array_sort_by_column($pools, 'Priority');
  
  foreach ($pools as $pool) {

    if ($pool['Status'] <> "Alive") {

      $rowclass = 'error';

    } else {

      $rowclass = 'success';

    }
	
	$poolURL = explode(":", str_replace("/", "", $pool['URL']));

    $table = $table . "
    <tr class='" . $rowclass . "'>
	<td>";
	/*if($poolID != 0) {
		$table = $table . "<a href='/?url=" . urlencode($pool['URL']) . "&user=" . urlencode($pool['User']) . "'><img src='/img/up.png'></td>";
	}*/
	$table = $table . "
    <td class='text-left'>" . $poolURL[1] . "</td>
    <td class='text-left ellipsis'>" . $pool['User'] . "</td>
    <td class='text-left'>" . $pool['Status'] . "</td>
    <td>" . $pool['Priority'] . "</td>
    <td>" . $pool['Getworks'] . "</td>
    <td>" . $pool['Accepted'] . "</td>
    <td>" . $pool['Rejected'] . "</td>
    <td>" . $pool['Discarded'] . "</td>
    <td>" . date('H:i:s', $pool['LastShareTime']) . "</td>        
    <td>" . $pool['Diff1Shares'] . "</td>       
    <td>" . round($pool['DifficultyAccepted']) . "</td>
    <td>" . round($pool['DifficultyRejected']) . "</td>
    <td>" . round($pool['LastShareDifficulty'], 0) . "</td>
    <td>" . $pool['BestShare'] . "</td>     
    </tr>";
    
    $poolID++;

  }

  return $table;

}


  <div class="row">
    <div class="col-lg-4">
      <dl class="dl-horizontal">
        <dt>Temp Front / Back T,B </dt>
        <dd><?php echo $mpTemp[1]; ?> <small>&deg;C</small> / <?php echo $mpTemp[2]; ?>,<?php echo $mpTemp[3]; ?> <small>&deg;C</small></dd>
	
	<dt>Fan Speed</dt>
	<dd><?php echo $workmode['FAN']['value']?></dd>
	<dt>Start Voltage</dt>
	<dd title="top/bottom"><?php echo $workmode['VS0']['value']/1000?> / <?php echo $workmode['VS1']['value']/1000?> / <?php echo $workmode['VS2']['value']/1000?> / <?php echo $workmode['VS3']['value']/1000?></dd>
	<dt>Max Voltage</dt>
	<dd><?php echo $workmode['VMAX']['value']/1000?></dd>
	<dt>Max Watts</dt>
	<dd title="top/bottom"><?php echo $workmode['AC0']['value']?> / <?php echo $workmode['AC1']['value']?> / <?php echo $workmode['AC2']['value']?> / <?php echo $workmode['AC3']['value']?></dd>
      </dl>
    </div>
    <div class="col-lg-4">
      <dl class="dl-horizontal">
        <dt>Best Share</dt>
        <dd><?php echo $summary['SUMMARY'][0]['BestShare']; ?></dd>
		<dt>Miner time</dt>
        <dd><?php echo date('D, d M Y H:i') ?></dd>
        <dt>System Uptime</dt>
        <dd><?php echo secondsToWords(round($uptime[0])); ?></dd>
        <dt>CGMiner Uptime</dt>
        <dd><?php echo secondsToWords($summary['SUMMARY'][0]['Elapsed']); ?></dd>
      </dl>
    </div>
    <div class="col-lg-4">
      <dl class="dl-horizontal">

        <dt>Hostname</dt>
        <dd><?php echo exec("hostname", $name); ?></dd>
        <dt>MAC address</dt>
        <dd><?php echo exec("/usr/local/bin/getmac.sh", $name);  ?></dd>
        <dt>Hardware Version</dt>
        <dd><?php echo $full_model_name; ?></dd>
        <dt>FW Version</dt>
        <dd><?php echo(file_get_contents("/fw_ver")) ?>
        <?php
          if (! is_link('/usr/local/bin/miner_gate_arm')) {
                 echo "<b></br>CUSTOM miner_gate_arm! (/usr/local/bin)</b>";
             }
             if (file_exists('/mnt/config/etc/bin/miner_gate_sp20')) {
                 echo "<b></br>CUSTOM miner_gate_arm! (/etc/bin)</b>";
             }
	?>
        </dd>
        <dt>CGMiner Version</dt>
        <dd><?php echo $summary['STATUS'][0]['Description']; ?></dd>
        <dt>CGMiner Status</dt>
	<dd class="status-<?php echo strtolower(str_replace(' ', '_', $proc_status['cgminer'])); ?>"><?php echo $proc_status['cgminer']; ?></dd>
        <dt>MinerGate Status</dt>
	<dd class="status-<?php echo strtolower(str_replace(' ', '_', $proc_status['minergate'])); ?>"><?php echo $proc_status['minergate']; ?></dd>
<!--        <dt>Donation Minutes</dt>
        <dd><//?php echo $settings['donateAmount']; ?>
-->      </dl>
    </div>
  </div>


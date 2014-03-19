<footer class="container">
  <hr />
  Miner Time: <?php echo date('D, d M Y H:i:s T') ?>

  <!-- TODO: Re-enable for production -->
  <?php //if(empty($settings['donateAmount'])) { echo $plea; } ?>
</footer>

<script type="text/javascript" src="js/jquery.min.js"></script> 
<script type="text/javascript" src="js/jquery.tablesorter.js"></script>
<script type="text/javascript" src="js/bootstrap.min.js"></script>
<script type="text/javascript" src="js/bootbox.min.js"></script>
<script type="text/javascript" id="js">
  $(document).ready(function() {
    $(".tablesorter").tablesorter();
    
    $('#chartToggle').click(function() {
      $('.chartMore').slideToggle('slow', function() {
          if ($(this).is(":visible")) {
              $('#chartToggle').text('Hide extended charts');
          } else {
              $('#chartToggle').text('Display extended charts');
          }
      });
    });
    $('#dhcpEnable').click(function() {
      $(".dhcp-enabled").toggle(!this.checked);
    });
    $('#alertEnable').click(function() {
      $(".alert-enabled").toggle(this.checked);
    });
    $('#donateEnable').click(function() {
      $(".donate-enabled").toggle(this.checked);
    });
    $('#alertSMTPAuth').click(function() {
      $(".smtpauth-enabled").toggle(this.checked);
    });
    // highlight active tab
    $('.navbar li').each(function(){
    	if($('a', this).attr('href') == document.location.pathname){
		$(this).addClass('active');
		return false;
	}
    });
  });
</script>

</body>
</html>

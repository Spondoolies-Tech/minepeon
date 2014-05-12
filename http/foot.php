<footer class="container">
  <hr />
  <!-- TODO: Re-enable for production -->
  <?php //if(empty($settings['donateAmount'])) { echo $plea; } ?>
</footer>

<script type="text/javascript" src="js/jquery.min.js"></script> 
<script type="text/javascript" src="js/jquery.tablesorter.js"></script>
<script type="text/javascript" src="js/bootstrap.min.js"></script>
<script type="text/javascript" src="js/bootbox.min.js"></script>
<script type="text/javascript" src="js/spond.js"></script>
<script type="text/javascript" src="js/bootstrap-multiselect.js"></script>
<link rel="stylesheet" href="css/bootstrap-multiselect.css" type="text/css"/>
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
    /**
     * settings page
     **/
    $('select.new_day').multiselect({buttonClass:"btn-none", onChange: function(e, c){
		cron_ready_to_add = true;
		if(c){ // something was checked, not unchecked
			if($(e).val() != "all") $('select.new_day').multiselect('deselect', 'all');
			else $('select.new_day').multiselect('deselect', [0,1,2,3,4,5,6]);
		}
    	}});
    cron_ready_to_add  = false;
    $('select', '.jobs-container-new').change(function(){
    	cron_ready_to_add = true;
    });
    if(typeof(speedSettings) != "undefined") setupSpeedSettings();
  });
</script>

</body>
</html>

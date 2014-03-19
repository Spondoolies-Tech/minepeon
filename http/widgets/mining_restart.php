<div class="mining_restart">
	<a href="" onclick="restart_mining(); return false;">Click here to restart the mining process</a>
</div>
<script type="text/javascript">
	function restart_mining(){
		$.get('/control.php?op=mining_restart', function(data){
			bootbox.alert(data)
		})
	}
</script>

//var ajax_op = {

var ajax_op = function(ops){
	op = this;
	this.method = "get",
	this.url = "",
	this.wait_url = "", // should return "{status:true}", unless a custom status function is provided.
	this.timeout = 5, // will check for completed status this many times.
	this.wait = 1, //seconds to wait between checking status
	this.initialResponse = "", // response from initial ajax request, before verifying operation was successful
	this.errorMessage = "There was a timeout performing the requested operation. It probably means CgMiner waits for the pool, check the state of CgMiner in the main page in a minute.",
	this.successMessage = "The requested operation was successful.",
	this.waiting;

	this.status = function(data){ // data from wait_url
		return data.status;
	},

	this.ajax = {
		async: true,
		dataType:"text",
		error: function(data, e){
			}
		},

	this.send = function(){
		op.waiting = bootbox.dialog({message:"Processing request. Please wait (up to 30 seconds)."});
		var ajax = op.ajax;
		ajax.type = op.method;
		ajax.success  =op.verifyComplete;
		$.ajax(op.url, ajax);
	},

	this.verifyComplete = function(data){
		if(typeof(data) != "undefined"){
			op.initialResponse = data;
		}
		if(!op.wait_url){
			op.waiting.modal('hide');
			return data;
		}
		if(!op.timeout) return op.error();
		op.timeout--;
		$.get(op.wait_url, function(data){
			if(op.status(data) == true) return op.success();
			op.verifier = setTimeout(op.verifyComplete, op.wait*1000);
		}, "json")
	},

	this.error = function(){
		op.waiting.modal('hide');
		bootbox.alert(op.errorMessage);
		return false;
	},

	this.success = function(){
		op.waiting.modal('hide');
		bootbox.alert(op.successMessage);
		return true;
	}

	// constructor
	if(typeof(ops) == typeof({})){ 
		for(k in ops){
			this[k] = ops[k];
		}
	}
};

//
//general purpose functions
function blink(times){
	if(typeof(times) == undefined || parseInt(times) < 1) times = 1;
	$.get('control.php?op=indicate&times='+times);
	// do we use less resources turning on/off from js, or using sleep in php?
}

var AjaxOps = function(ops){
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
function blink(op){
	if(typeof(op) == undefined) op = "end_flash_led";
	$.get('control.php?op='+op);
	// do we use less resources turning on/off from js, or using sleep in php?
}


function send_command(cmd, type){
    if(typeof(type) == "undefined")
    type ="";

    var timeout = 10; // for nice, 10 tries is sufficient
    if(type != "nice"){
    timeout = 30; // hard restart can take longer to get back up
    }

    var a = new AjaxOps({
        url: "control.php?op=" + cmd + "&"+type,
        wait_url: "status.php?proc=cgminer",
        wait: 2,
        timeout: timeout,
        success: function(){setTimeout(function() { document.location.reload()}, 3500) }
    });

    a.send();
return false;
}


$(function(){
$('a.ajax').click(function(e){
    $.get($(this).attr('href'), function(){
        document.location.reload();
    });
    e.preventDefault();
    return false;
});
});
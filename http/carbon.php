
<?php
require_once('inc/global.inc.php');
require('inc/ansi.inc.php');
include('head.php');
include('menu.php');
?>
    <h3 class="asics">Under-volting Calculator</h3>
<body>
<pre style="padding:10px;font-size:85%">
<div style="padding:10px;color:black;background:#f4f4f4">
The numbers presented here are rough estimation of your bottom line gains under different ASIC voltage settings.
Find the optimal voltage based on your electricity price and set your "start voltage" and "max voltage" to those settings.
It is not recommended to change the max wattage setting for PSU for under-volting.
Note1: The numbers here are for average miner and it is a linear approximation of non-linear function. Make proper calculation to get actual values for your system
Note2: Not all voltages may work on your system because of PSU and ASIC limitation. SP31/SP35/SP20 ASICs should work between 620mv and 740mv, SP30 ASICs work in 640mv to 740mv range. The PSU also limits your system.
Note3: SP50 under development ...
<br/>



<form name ="myform" action="#">
Miner type:
<select id="ddlViewBy">
<option value="sp20">sp20</option>
<option value="sp30">sp30</option>
<option value="sp31">sp31</option>
<option value="sp35">sp35</option>
<option value="sp50">sp50</option>
</select>

Price per kw  <input type ="text" id = "cent_per_kw" value="10"/>cent<br/>
BTC price  <input type="text" id ="btc_price" value="200"/>$<br/>
ASIC voltage  <input type="text" id ="asic_voltage" value="690"/>mv<br/>
Network HashRate  <input type="text" id ="net_hash" value="300"/>PH<br/>

Electricity source:
<select id="electricity_source">
<option value="800">Coal</option>
<option value="430">Gas</option>
<option value="6">Nuclear</option>
<option value="4">Hydro</option>
<option value="60">Solar</option>
<option value="3">Wind</option>
<option value="1500">Wood</option>
</select>


<input type ="button" value ="Calculate" onclick="summation();"/><br/><br/>
----------------------------------------------
Power Estimation   <input type="text" id ="your_power" readonly /> kw/h<br/>
Hashrate Estimation   <input type="text" id ="your_hashrate" readonly /> TH/s<br/>
Monthly carbon footprint <input type ="text" id = "carbon" readonly /> kg <br/>
Monthly Power bill:   <input type ="text" id = "txt_bill" readonly /> $<br/>
BTCs per month:   <input type ="text" id = "txt_btc_expected" readonly /> BTC, worth <input type ="text" id = "txt_btc_val" readonly /> $<br/>
----------------------------------------------
Bottom line gain:  <input type ="text" id = "txt_gain"/> $<br/>
</form>





<script language="JavaScript">
    function summation() {
        var num1, num2, result;
        //document.getElementById("txtSum").value = 0;
        cent_per_kw = parseInt( document.getElementById("cent_per_kw").value);
        btc_price = parseInt( document.getElementById("btc_price").value);
        asic_voltage = parseInt( document.getElementById("asic_voltage").value);
        net_hash = parseInt( document.getElementById("net_hash").value);


        //expected_power=voltage;
        //hash_power =  expected_hash_gh*1000* / difficulty
        var e = document.getElementById("ddlViewBy");
        var model = e.options[e.selectedIndex].value;
        switch(model) {
            case "sp30":
                power_watts = (16.05*asic_voltage) - 8575;
                hash_gh  = (21.3*asic_voltage) -10732;
                break;
            case "sp50":
            case "sp31":
            case "sp35":
                power_watts = (25.36*asic_voltage) - 14116;
                hash_gh  = (27.12*asic_voltage) -13374;
                break;
            case "sp20":
                power_watts = (25.36*asic_voltage/3.75) - 14116/3.75;
                hash_gh  = (27.12*asic_voltage/3.75) -13374/3.75;
                break;
        }





        btcs_in_month = 6*24*25*(30.5); // 108000
        your_btcs_per_month = (hash_gh*btcs_in_month)/(net_hash*1000000); //
        your_btc_value = your_btcs_per_month*btc_price;
        power_bill = cent_per_kw*power_watts*24*(30.5)/1000/100;
        //alert(your_btcs_per_month_promil);
        document.getElementById("txt_btc_expected").value = (your_btcs_per_month).toFixed(2);
        document.getElementById("your_hashrate").value = (hash_gh/1000).toFixed(2);
        document.getElementById("your_power").value = (power_watts/1000).toFixed(2);
        document.getElementById("txt_btc_val").value = (your_btc_value).toFixed(2);
        document.getElementById("txt_bill").value = (power_bill).toFixed(2);

        var e = document.getElementById("electricity_source");
        var gramm = e.options[e.selectedIndex].value;
        document.getElementById("carbon").value = (power_watts*gramm*24*(30.5)/1000/1000).toFixed(2);
        document.getElementById("txt_gain").value = (your_btc_value - power_bill).toFixed(2);

        //result = num1+num2;
        /*
        if (isNaN(result))
        {
            document.getElementById("txtSum").value = "Wrong data";
        }
        else
        {
            document.getElementById("txtSum").value = result;
        }
        */
        return true;
    }


</script>
</body>




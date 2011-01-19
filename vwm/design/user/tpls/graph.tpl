<!--[if IE]><script language="javascript" type="text/javascript" src="modules/js/flot/excanvas.min.js"></script><![endif]-->
<span style="float:right;padding-right:40px;"><input type="text" name="begin" value="{$begin}" /> - <input type="text" name="end" value="{$end}" />
<input type="submit" value="Set Date" /></span> 
</form>

<h2 style="align:center;padding-left:40px;">Daily Emissions</h2><br/>
<div style="padding-left:20px;width:1450px;height:370px;">
   <div id="placeholderDE" style="float:left;width:1200px;height:300px"></div>
   <div id="legendDE" style="float:left;width:200px;height:300px"></div>
    <p id="hoverdataDE" style="float:left;">Mouse hovers at
    (<span id="xDE">0</span>, <span id="yDE">0</span>). <span id="clickdata"></span></p>
</div>
<h2 style="align:center;padding-left:40px;">Product Usage</h2><br/>
<div style="padding-left:20px;width:1450px;height:{if $legendPUheight > 370}{$legendPUheight}{else}370{/if}px;">
   <div id="placeholderPU" style="float:left;width:1200px;height:300px"></div>
   <div id="legendPU" style="float:left;width:200px;height:300px"></div>
    <p id="hoverdataPU" style="float:left;">Mouse hovers at
    (<span id="xPU">0</span>, <span id="yPU">0</span>). <span id="clickdata"></span></p>
</div>
{if $dataDU}
<h2 style="align:center;padding-left:40px;">Department Usage</h2><br/>
<div style="padding-left:20px;width:1450px;height:370px;">
   <div id="placeholderDU" style="float:left;width:1200px;height:300px"></div>
   <div id="legendDU" style="float:left;width:200px;height:300px"></div>
    <p id="hoverdataDU" style="float:left;">Mouse hovers at
    (<span id="xDU">0</span>, <span id="yDU">0</span>). <span id="clickdata"></span></p>
</div>
{/if}
<script language="javascript" type="text/javascript">
{literal}
$(function () {
	{/literal}
	{if $dataDE}
		var all_data = {$dataDE};
		var tick = {$tick};
		var ylabel = 'voc, lbs';
		var xlabel = 'date';
		var placeholder = $("#placeholderDE");
		var legend = $("#legendDE");
		var x = $("#xDE");
		var y = $("#yDE");
		
		flotGraph(placeholder, legend, all_data, tick, ylabel, xlabel, y, x);
	{/if}
	
	{if $dataPU}
		all_data = {$dataPU};
		placeholder = $("#placeholderPU");
		legend = $("#legendPU");
		x = $("#xPU");
		y = $("#yPU");
		var ylabel = 'qty, lbs';
		
		flotGraph(placeholder, legend, all_data, tick, ylabel, xlabel, y, x);
	{/if}

	{if $dataDU}
		all_data = {$dataDU};
		placeholder = $("#placeholderDU");
		x = $("#xDU");
		y = $("#yDU");
		legend = $("#legendDU");
		
		flotGraph(placeholder, legend, all_data, tick, ylabel, xlabel, y, x);
	{/if}
	{literal}
});
{/literal}
</script>

    
    
  
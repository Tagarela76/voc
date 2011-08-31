<!--[if IE]><script language="javascript" type="text/javascript" src="modules/js/flot/excanvas.min.js"></script><![endif]-->
<span style="float:right;padding-right:40px;">
	<input type="text" name="begin" id="calendar1" value="{$begin->formatOutput()}" /> - <input type="text" name="end" id="calendar2" value="{$end->formatOutput()}" />
	<input type="submit" value="Set Date" class="button" /></span>
</form>
{assign var=noDataTable value="<table style='padding-left:20px;width:100%;height:100%;verticalalign:middle;'><tr><td style='width:100%;height:100%;text-align:center; vertical-align:middle; border:1px solid Black'><h2>No Data</h2></td></tr></table>"}
<h2 style="align:center;padding-left:40px;">Daily Emissions</h2><br/>

<div style="padding-left:20px;width:1450px;height:370px;">
	<div id="placeholderDE" style="float:left;width:1200px;height:300px"></div>
	<div id="legendDE" style="float:left;width:200px;height:300px;overflow:auto;"></div>
    <p id="hoverdataDE" style="float:left;">Mouse hovers at
		(<span id="xDE">0</span>, <span id="yDE">0</span>). <span id="clickdata"></span></p>
</div>



<h2 style="align:center;padding-left:40px;">Product Usage</h2><br/>
<div style="padding-left:20px;width:1450px;height:{*if $legendPUheight > 370}{$legendPUheight}{else*}370{*/if*}px;">
	<div id="placeholderPU" style="float:left;width:1200px;height:300px"></div>
	<div id="legendPU" style="float:left;width:200px;height:300px;overflow:auto;"></div>
    <p id="hoverdataPU" style="float:left;">Mouse hovers at
		(<span id="xPU">0</span>, <span id="yPU">0</span>). <span id="clickdata"></span></p>
</div>
{if $dataDU}
	<h2 style="align:center;padding-left:40px;">Department Usage</h2><br/>
	<div style="padding-left:20px;width:1450px;height:370px;">
		<div id="placeholderDU" style="float:left;width:1200px;height:300px"></div>
		<div id="legendDU" style="float:left;width:200px;height:300px;overflow:auto;"></div>
		<p id="hoverdataDU" style="float:left;">Mouse hovers at
			(<span id="xDU">0</span>, <span id="yDU">0</span>). <span id="clickdata"></span></p>
	</div>
{/if}
<h2 style="align:center;padding-left:40px;">Daily Emissions by Facilities</h2><br/>
<div style="padding-left:20px;width:1450px;height:370px;">
	<div id="placeholderDEFacility" style="float:left;width:1200px;height:300px"></div>
	<div id="legendDEFacility" style="float:left;width:200px;height:300px;overflow:auto;"></div>
    <p id="hoverdataDEFacility" style="float:left;">Mouse hovers at
		(<span id="xDEFacility">0</span>, <span id="yDEFacility">0</span>). <span id="clickdata"></span></p>
</div>

<form method="POST" name="facilityName" action="?action=browseCategory&category={$request.category}&id={$request.id}&bookmark={$request.bookmark}">
	<table width="600px">
		<tr>
			<td width="60%"><h2 style="align:center;padding-left:40px;">Daily Emissions by Departments</h2></br></td>
			<td width="40%"><div><big>Facility: 
						<select type="text" name="facilityList" onchange="onSelectFacility(value);">
							{if (count($facilityList) gt 1)}<option value="all" {if ($selectedFacility == 'all')} selected {/if}>All Facilities</option>{/if}
							{foreach from=$facilityList item=facility}
								<option value="{$facility.id}" {if $selectedFacility == $facility.id} selected {/if}>{$facility.name}</option>
							{/foreach}
						</select></div></br>
			</td>
		</tr>
	</table>
</form>

<div style="padding-left:20px;width:1450px;height:370px;">
	<div id="placeholderDEDepartment" style="float:left;width:1200px;height:300px"></div>
	<div id="legendDEDepartment" style="float:left;width:200px;height:300px;overflow:auto;"></div>
    <p id="hoverdataDEDepartment" style="float:left;">Mouse hovers at
		(<span id="xDEDepartment">0</span>, <span id="yDEDepartment">0</span>). <span id="clickdata"></span></p>
</div>
{literal}					
<script type="text/javascript">
	function onSelectFacility(val){
		document.forms.facilityName.submit();
		//alert(val);
	}
</script>
{/literal}
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

		{if $dataDE != "[]"}
		flotGraph(placeholder, legend, all_data, tick, ylabel, xlabel, y, x);
		{else}
		//document.getElementById('placeholderDE').innerHTML = "{$noDataTable}";
		$('#placeholderDE').html("{$noDataTable}");
		{/if}
	{/if}

	{if $dataPU}
		all_data = {$dataPU};
		placeholder = $("#placeholderPU");
		legend = $("#legendPU");
		x = $("#xPU");
		y = $("#yPU");
		var ylabel = 'qty, lbs';

		{if $dataPU != "[]"}
		flotGraph(placeholder, legend, all_data, tick, ylabel, xlabel, y, x);
		{else}
		document.getElementById('placeholderPU').innerHTML = "{$noDataTable}";
		{/if}
	{/if}

	{if $dataDU}
		all_data = {$dataDU};
		placeholder = $("#placeholderDU");
		var ylabel = 'voc, lbs';
		x = $("#xDU");
		y = $("#yDU");
		legend = $("#legendDU");

		{if $dataDU != "[]"}
		flotGraph(placeholder, legend, all_data, tick, ylabel, xlabel, y, x);
		{else}
		document.getElementById('placeholderDU').innerHTML = "{$noDataTable}";
		{/if}
	{/if}
		
	{if $dataDEF}
		var all_data = {$dataDEF};
		var tick = {$tick};
		var ylabel = 'voc, lbs';
		var xlabel = 'date';
		var placeholder = $("#placeholderDEFacility");
		var legend = $("#legendDEFacility");
		var x = $("#xDEFacility");
		var y = $("#yDEFacility");

		{if $dataDEF != "[]"}
		flotGraph(placeholder, legend, all_data, tick, ylabel, xlabel, y, x);
		{else}
		//document.getElementById('placeholderDE').innerHTML = "{$noDataTable}";
		$('#placeholderDEFacility').html("{$noDataTable}");
		{/if}
	{/if}
		
	{if $dataDED}
		var all_data = {$dataDED};
		console.log(all_data);
		var tick = {$tick};
		var ylabel = 'voc, lbs';
		var xlabel = 'date';
		var placeholder = $("#placeholderDEDepartment");
		var legend = $("#legendDEDepartment");
		var x = $("#xDEDepartment");
		var y = $("#yDEDepartment");
		
		{if $dataDED != "[]"}
		flotGraph(placeholder, legend, all_data, tick, ylabel, xlabel, y, x);
		{else}
		//document.getElementById('placeholderDE').innerHTML = "{$noDataTable}";
		$('#placeholderDEDepartment').html("{$noDataTable}");
		{/if}
	{/if}	
		
	{literal}
});
	{/literal}
</script>
{literal}
	<script>
		function clearInputBox(item){
			item.value = "";
		}

		$(document).ready(function(){
			 $('#calendar1, #calendar2').datepicker({ dateFormat: '{/literal}{$begin->getFromTypeController('getFormatForCalendar')}{literal}' });
		});

	</script>
{/literal}




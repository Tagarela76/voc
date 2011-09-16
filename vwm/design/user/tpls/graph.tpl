<!--[if IE]><script language="javascript" type="text/javascript" src="modules/js/flot/excanvas.min.js"></script><![endif]-->
<div><table width="100%"><tr>
			<td align="right" width="40%" valign="center"><b>Select graph:</b></td>
			<td align="left" width="60%" valign="center"><select id="selGr" name="selectGraph" onChange="onSelectGraph(value, false);">
					<option value="1" {if ($selectedGraph == '1')} selected {/if}>Company Daily Emissions</option>
					<option value="2" {if ($selectedGraph == '2')} selected {/if}>Company Product Usage</option>
					<option value="3" {if ($selectedGraph == '3')} selected {/if}>Daily Emissions by Facility</option>
					<option value="4" {if ($selectedGraph == '4')} selected {/if}>Daily Emissions by Department</option>
					<option value="5" {if ($selectedGraph == '5')} selected {/if}>Product Usage by Facility</option>
					<option value="6" {if ($selectedGraph == '6')} selected {/if}>Product Usage by Deprtment</option>
				</select></td>
		</tr></table></div>
<span style="float:right;padding-right:40px;">
	<input type="text" name="begin" id="calendar1" value="{$begin->formatOutput()}" /> - <input type="text" name="end" id="calendar2" value="{$end->formatOutput()}" />
	<input type="submit" value="Set Date" class="button" /></span><br><br>
</form>
{assign var=noDataTable value="<table style='padding-left:20px;width:100%;height:100%;verticalalign:middle;'><tr><td style='width:100%;height:100%;text-align:center; vertical-align:middle; border:1px solid Black'><h2>No Data</h2></td></tr></table>"}

<div id="graph1" style="display: block;">
	<table><tr>
			<td style="padding-left:20px;padding-right:5px; padding-top: 5px;"><a><img src="../vwm/images/goBack.png" onClick="onBack();"></a></td>
			<td><h2 style="align:center; margin: 0px; padding: 0px">{if ($request.category eq 'company')} Company Daily Emissions {else}{if ($request.category eq 'facility')} Facility Daily Emissions{else} Daily Emissions {/if}{/if}</h2></td>
			<td style="padding-right:20px;padding-left:5px; padding-top: 5px;"><a><img src="../vwm/images/goNext.png" onClick="onNext();"/></a></td>
		</tr></table><br/><br/>
	<div style="padding-left:20px;width:1450px;height:330px;">
		<div id="placeholderDE" style="float:left;width:1200px;height:300px"></div>
		<div id="legendDE" style="float:left;width:200px;height:300px;overflow:auto;"></div>
		<p id="hoverdataDE" style="float:left;">Mouse hovers at
			(<span id="xDE">0</span>, <span id="yDE">0</span>). <span id="clickdata"></span></p>
	</div>
</div>

<div id="graph2" style="display: block;">
	<table><tr>
			<td style="padding-left:20px;padding-right:5px; padding-top: 5px;"><a><img src="../vwm/images/goBack.png" onClick="onBack();"></a></td>
			<td><h2 style="align:center; margin: 0px;">{if ($request.category eq 'company')} Company Product Usage {else}{if ($request.category eq 'facility')} Facility Product Usage{else} Product Usage {/if}{/if}</h2></td>
			<td style="padding-right:20px;padding-left:5px; padding-top: 5px;"><a><img src="../vwm/images/goNext.png" onClick="onNext();"/></a></td>
		</tr></table><br/><br/>

	<div style="padding-left:20px;width:1450px;height:{*if $legendPUheight > 370}{$legendPUheight}{else*}330{*/if*}px;">
		<div id="placeholderPU" style="float:left;width:1200px;height:300px"></div>
		<div id="legendPU" style="float:left;width:200px;height:300px;overflow:auto;"></div>
		<p id="hoverdataPU" style="float:left;">Mouse hovers at
			(<span id="xPU">0</span>, <span id="yPU">0</span>). <span id="clickdata"></span></p>
	</div>
</div>


{if $dataDU}
	<h2 style="align:center;padding-left:40px;">Daily Emissions by Departments</h2><br/>
	<div style="padding-left:20px;width:1450px;height:330px;">
		<div id="placeholderDU" style="float:left;width:1200px;height:300px"></div>
		<div id="legendDU" style="float:left;width:200px;height:300px;overflow:auto;"></div>
		<p id="hoverdataDU" style="float:left;">Mouse hovers at
			(<span id="xDU">0</span>, <span id="yDU">0</span>). <span id="clickdata"></span></p>
	</div>
{/if}


<div id="graph3" style="display: block;">
	{if $dataDEF}
		<table><tr>
				<td style="padding-left:20px;padding-right:5px; padding-top: 5px;"><a><img src="../vwm/images/goBack.png" onClick="onBack();"></a></td>
				<td><h2 style="align:center; margin: 0px;">Daily Emissions by Facility</h2></td>
				<td style="padding-right:20px;padding-left:5px; padding-top: 5px;"><a><img src="../vwm/images/goNext.png" onClick="onNext();"/></a></td>
			</tr></table><br/><br/>
		<div style="padding-left:20px;width:1450px;height:330px;">
			<div id="placeholderDEFacility" style="float:left;width:1200px;height:300px"></div>
			<div id="legendDEFacility" style="float:left;width:200px;height:300px;overflow:auto;"></div>
			<p id="hoverdataDEFacility" style="float:left;">Mouse hovers at
				(<span id="xDEFacility">0</span>, <span id="yDEFacility">0</span>). <span id="clickdata"></span></p>
		</div>
	{/if}
</div>


<div id="graph4" style="display: block;">
	{if $dataDED}
		<form method="POST" name="facilityName" action="?action=browseCategory&category={$request.category}&id={$request.id}&bookmark={$request.bookmark}">
			<table>
				<tr>
					<td style="padding-left:20px;padding-right:5px; padding-top: 5px;"><a><img src="../vwm/images/goBack.png" onClick="onBack();"></a></td>
					<td><h2 style="align:center; margin: 0px;">Daily Emissions by Department</h2></td>
					<td style="padding-right:20px;padding-left:5px; padding-top: 5px;"><a><img src="../vwm/images/goNext.png" onClick="onNext();"/></a></td>
					<td valign="middle" style="padding-left: 10px;"><b>Facility:</b>
						<select type="text" name="facilityList" onchange="onSelectFacility(value);">
					{if (count($facilityList) gt 1)}<option value="all" {if ($selectedFacility == 'all')} selected {/if}>All Facilities</option>{/if}
					{foreach from=$facilityList item=facility}
						<option value="{$facility.id}" {if $selectedFacility == $facility.id} selected {/if}>{$facility.name}</option>
					{/foreach}
				</select>
			</td>
		</tr>
	</table></br></br>
</form>

<div style="padding-left:20px;width:1450px;height:330px;">
	<div id="placeholderDEDepartment" style="float:left;width:1200px;height:300px"></div>
	<div id="legendDEDepartment" style="float:left;width:200px;height:300px;overflow:auto;"></div>
    <p id="hoverdataDEDepartment" style="float:left;">Mouse hovers at
		(<span id="xDEDepartment">0</span>, <span id="yDEDepartment">0</span>). <span id="clickdata"></span></p>
</div>
{/if}
</div>


<div id="graph5" style="display: block;">
	{if $dataPUF}
		<form method="POST" name="facilityNamePU" action="?action=browseCategory&category={$request.category}&id={$request.id}&bookmark={$request.bookmark}">
			<table>
				<tr>
					<td style="padding-left:20px;padding-right:5px; padding-top: 5px;"><a><img src="../vwm/images/goBack.png" onClick="onBack();"></a></td>
					<td><h2 style="align:center; margin: 0px;">Product Usage by Facility</h2></td>
					<td style="padding-right:20px;padding-left:5px; padding-top: 5px;"><a><img src="../vwm/images/goNext.png" onClick="onNext();"/></a></td>
					<td><b>Facility:
							<select type="text" name="facilityListPU" onchange="onSelectFacilityPU(value);">
						{if (count($facilityListPU) gt 1)}<option value="all" {if ($selectedFacilityPU == 'all')} selected {/if}>All Facilities</option>{/if}
						{foreach from=$facilityListPU item=facility}
							<option value="{$facility.id}" {if $selectedFacilityPU == $facility.id} selected {/if}>{$facility.name}</option>
						{/foreach}
					</select>
			</td>
		</tr>
	</table><br/><br/>
</form>

<div style="padding-left:20px;width:1450px;height:330px;">
	<div id="placeholderPUFacility" style="float:left;width:1200px;height:300px"></div>
	<div id="legendPUFacility" style="float:left;width:200px;height:300px;overflow:auto;"></div>
    <p id="hoverdataPUFacility" style="float:left;">Mouse hovers at
		(<span id="xPUFacility">0</span>, <span id="yPUFacility">0</span>). <span id="clickdata"></span></p>
</div>
{/if}
</div>


<div id="graph6" style="display: block;">
	{if $dataPUD}
		<form method="POST" name="departmentNamePU" action="?action=browseCategory&category={$request.category}&id={$request.id}&bookmark={$request.bookmark}">
			<table>
				<tr>
					<td style="padding-left:20px;padding-right:5px; padding-top: 5px;"><a><img src="../vwm/images/goBack.png" onClick="onBack();"></a></td>
					<td><h2 style="align:center;margin: 0px;">Product Usage by Department</h2></td>
					<td style="padding-right:20px;padding-left:5px; padding-top: 5px;"><a><img src="../vwm/images/goNext.png" onClick="onNext();"/></a></td>
					<td><b>Facility/Department:</b>
						<select type="text" name="departmentListPU" onchange="onSelectDepartmentPU(value);">
					{if (count($departmentListPU) gt 1)}<option value="all" {if ($selectedDepartmentPU == 'all')} selected {/if}>All Departments</option>{/if}
					{foreach from=$departmentListPU key=k item=department}
						{foreach from=$department item=dep}
							<option value="{$dep.id}" {if $selectedDepartmentPU == $dep.id} selected {/if}>{$k}/{$dep.name}</option>
						{/foreach}
					{/foreach}
				</select>
			</td>
		</tr>
	</table><br/><br/>
</form>

<div style="padding-left:20px;width:1450px;height:330px;">
	<div id="placeholderPUDepartment" style="float:left;width:1200px;height:300px"></div>
	<div id="legendPUDepartment" style="float:left;width:200px;height:300px;overflow:auto;"></div>
	<p id="hoverdataPUDepartment" style="float:left;">Mouse hovers at
		(<span id="xPUDepartment">0</span>, <span id="yPUDepartment">0</span>). <span id="clickdata"></span></p>
</div>
{/if}
</div>
<div>
	<table width="1200px">
		<tr>
			<td align="right"><a title="Previous graph"><img src="../vwm/images/goBackLarge.png" onClick="onBack();"/></a></td>
			<td align="left"><a title="Next graph"><img src="../vwm/images/goNextLarge.png" onClick="onNext();"/></a></td>
		</tr>
	</table>
</div>

{literal}
	<script type="text/javascript">
		function onSelectFacility(val){
			document.forms['facilityName'].submit();
		}

		function onSelectDepartmentPU(val){
			document.forms['departmentNamePU'].submit();
		}

		function onSelectFacilityPU(val){
			document.forms['facilityNamePU'].submit();
		}

		function onNext(){
				selRow++;
				if (selRow > 6){
					selRow = 6;
				}
				onSelectGraph(selRow + '', true);
				document.getElementById('selGr').options[selRow-1].selected = true;
		}

		function onBack(){
				selRow--;
				if (selRow < 1){
					selRow = 1;
				}
				onSelectGraph(selRow + '', true);
				document.getElementById('selGr').options[selRow-1].selected = true;
		}

		function onSelectGraph(val, flag){

			switch (val){
				case '1':
					$('#graph2').hide();
					$('#graph3').hide();
					$('#graph4').hide();
					$('#graph5').hide();
					$('#graph6').hide();
					$('#graph1').show();
					break;
				case '2':
					$('#graph1').hide();
					$('#graph3').hide();
					$('#graph4').hide();
					$('#graph5').hide();
					$('#graph6').hide();
					$('#graph2').show();
					break;
				case '3':
					$('#graph1').hide();
					$('#graph2').hide();
					$('#graph4').hide();
					$('#graph5').hide();
					$('#graph6').hide();
					$('#graph3').show();
					break;
				case '4':
					$('#graph1').hide();
					$('#graph2').hide();
					$('#graph3').hide();
					$('#graph5').hide();
					$('#graph6').hide();
					$('#graph4').show();
					break;
				case '5':
					$('#graph1').hide();
					$('#graph2').hide();
					$('#graph3').hide();
					$('#graph4').hide();
					$('#graph6').hide();
					$('#graph5').show();
					break;
				case '6':
					$('#graph1').hide();
					$('#graph2').hide();
					$('#graph3').hide();
					$('#graph4').hide();
					$('#graph5').hide();
					$('#graph6').show();
					break;
			}
			if (!flag){
				selRow = document.getElementById('selGr').selectedIndex + 1;
			}
		}
	</script>
{/literal}
<script language="javascript" type="text/javascript">
	{literal}
function redraw(hides, data, datatype){
	glob_data_plot = [];
	for (var j = 0; j < data.length; ++j)
    if(!hides[j]) // что скрываем, а что нет
      glob_data_plot.push(data[j]);

	switch (datatype){
		case 'data_DE':
				var ylabel = 'voc, lbs';
				var xlabel = 'date';
				var placeholder = $("#placeholderDE");
				var legend = $("#legendDE");
				var x = $("#xDE");
				var y = $("#yDE");
				break;
		case 'data_PU':
				var ylabel = 'qty, lbs';
				var xlabel = 'date';
				var placeholder = $("#placeholderPU");
				var legend = $("#legendPU");
				var x = $("#xPU");
				var y = $("#yPU");
				break;
		case 'data_DU':
				var ylabel = 'voc, lbs';
				var xlabel = 'date';
				var placeholder = $("#placeholderDU");
				var legend = $("#legendDU");
				var x = $("#xDU");
				var y = $("#yDU");
				break;
		case 'data_DEF':
				var ylabel = 'voc, lbs';
				var xlabel = 'date';
				var placeholder = $("#placeholderDEFacility");
				var legend = $("#legendDEFacility");
				var x = $("#xDEFacility");
				var y = $("#yDEFacility");
				break;
		case 'data_DED':
				var ylabel = 'voc, lbs';
				var xlabel = 'date';
				var placeholder = $("#placeholderDEDepartment");
				var legend = $("#legendDEDepartment");
				var x = $("#xDEDepartment");
				var y = $("#yDEDepartment");
				break;
		case 'data_PUF':
				var ylabel = 'qty, lbs';
				var xlabel = 'date';
				var placeholder = $("#placeholderPUFacility");
				var legend = $("#legendPUFacility");
				var x = $("#xPUFacility");
				var y = $("#yPUFacility");
				break;
		case 'data_PUD':
				var ylabel = 'qty, lbs';
				var xlabel = 'date';
				var placeholder = $("#placeholderPUDepartment");
				var legend = $("#legendPUDepartment");
				var x = $("#xPUDepartment");
				var y = $("#yPUDepartment");
				break;
	}
	{/literal}

		var tick = {$tick};
		if (glob_data_plot != [])
			flotGraph(placeholder, legend, glob_data_plot, tick, ylabel, xlabel, y, x, false);

	{literal}
}
	var glob_data_DE;
	var glob_data_PU;
	var glob_data_DU;
	var glob_data_DEFacility;
	var glob_data_DEDepartment;
	var glob_data_PUFacility;
	var glob_data_PUDepartment;

	var glob_data_plot = [];

	var hideDE = [];
	var hidePU = [];
	var hideDU = [];
	var hideDEF = [];
	var hideDED = [];
	var hidePUF = [];
	var hidePUD = [];

	var selRow;

$(function () {

	{/literal}

	selRow = document.getElementById('selGr').selectedIndex + 1;

	{if $dataDE}
		var all_data = {$dataDE};
		glob_data_DE = all_data;
		var tick = {$tick};
		var ylabel = 'voc, lbs';
		var xlabel = 'date';
		var placeholder = $("#placeholderDE");
		var legend = $("#legendDE");
		var x = $("#xDE");
		var y = $("#yDE");

		{if $dataDE != "[]"}
		flotGraph(placeholder, legend, all_data, tick, ylabel, xlabel, y, x, true);
		{else}
		//document.getElementById('placeholderDE').innerHTML = "{$noDataTable}";
		$('#placeholderDE').html("{$noDataTable}");
		{/if}
		{literal}
		// create checkboxes in legend
		var legend = document.getElementById('legendDE');
		var legend_tbl = legend.getElementsByTagName('table')[0];
		var legend_html = '<table style="font-size: smaller; color: rgb(84, 84, 84);"><tbody>';
		for (var k=0; k<glob_data_DE.length; k++)
			hideDE[k] = false;
		for (var i = 0; i < legend_tbl.rows.length; i++) {
			legend_html += '<tr>' +
			'<td><input type="checkbox" onclick="hideDE['+ i +']=!hideDE['+ i +'];redraw(hideDE, glob_data_DE,\'data_DE\');" checked="1"></td>'
			+ legend_tbl.rows[i].innerHTML
			+ '</tr>';
		}
		legend_html += "</tbody></table>";
		legend.innerHTML = legend_html;
		{/literal}
	{/if}

	{if $dataPU}
		all_data = {$dataPU};
		glob_data_PU = all_data;
		placeholder = $("#placeholderPU");
		legend = $("#legendPU");
		x = $("#xPU");
		y = $("#yPU");
		var ylabel = 'qty, lbs';

		{if $dataPU != "[]"}
		flotGraph(placeholder, legend, all_data, tick, ylabel, xlabel, y, x, true);
		{else}
		document.getElementById('placeholderPU').innerHTML = "{$noDataTable}";
		{/if}
		{literal}
		// create checkboxes in legend
		var legend = document.getElementById('legendPU');
		var legend_tbl = legend.getElementsByTagName('table')[0];
		var legend_html = '<table style="font-size: smaller; color: rgb(84, 84, 84);"><tbody>';
		for (var k=0; k<glob_data_PU.length; k++)
			hidePU[k] = false;
		for (var i = 0; i < legend_tbl.rows.length; i++) {
			legend_html += '<tr>' +
			'<td><input type="checkbox" onclick="hidePU['+ i +']=!hidePU['+ i +'];redraw(hidePU, glob_data_PU,\'data_PU\');" checked="1"></td>'
			+ legend_tbl.rows[i].innerHTML
			+ '</tr>';
		}
		legend_html += "</tbody></table>";
		legend.innerHTML = legend_html;
		{/literal}
	{/if}

	{if $dataDU}
		all_data = {$dataDU};
		glob_data_DU = all_data;
		placeholder = $("#placeholderDU");
		var ylabel = 'voc, lbs';
		x = $("#xDU");
		y = $("#yDU");
		legend = $("#legendDU");

		{if $dataDU != "[]"}
		flotGraph(placeholder, legend, all_data, tick, ylabel, xlabel, y, x, true);
		{else}
		document.getElementById('placeholderDU').innerHTML = "{$noDataTable}";
		{/if}
		{literal}
		// create checkboxes in legend
		var legend = document.getElementById('legendDU');
		var legend_tbl = legend.getElementsByTagName('table')[0];
		var legend_html = '<table style="font-size: smaller; color: rgb(84, 84, 84);"><tbody>';
		for (var k=0; k<glob_data_DU.length; k++)
			hideDU[k] = false;
		for (var i = 0; i < legend_tbl.rows.length; i++) {
			legend_html += '<tr>' +
			'<td><input type="checkbox" onclick="hideDU['+ i +']=!hideDU['+ i +'];redraw(hideDU, glob_data_DU,\'data_DU\');" checked="1"></td>'
			+ legend_tbl.rows[i].innerHTML
			+ '</tr>';
		}
		legend_html += "</tbody></table>";
		legend.innerHTML = legend_html;
		{/literal}
	{/if}

	{if $dataDEF}
		var all_data = {$dataDEF};
		glob_data_DEFacility = all_data;
		var tick = {$tick};
		var ylabel = 'voc, lbs';
		var xlabel = 'date';
		var placeholder = $("#placeholderDEFacility");
		var legend = $("#legendDEFacility");
		var x = $("#xDEFacility");
		var y = $("#yDEFacility");

		{if $dataDEF != "[]"}
		flotGraph(placeholder, legend, all_data, tick, ylabel, xlabel, y, x, true);
		{else}
		//document.getElementById('placeholderDE').innerHTML = "{$noDataTable}";
		$('#placeholderDEFacility').html("{$noDataTable}");
		{/if}
		{literal}
		// create checkboxes in legend
		var legend = document.getElementById('legendDEFacility');
		var legend_tbl = legend.getElementsByTagName('table')[0];
		var legend_html = '<table style="font-size: smaller; color: rgb(84, 84, 84);"><tbody>';
		for (var k=0; k<glob_data_DEFacility.length; k++)
			hideDEF[k] = false;
		for (var i = 0; i < legend_tbl.rows.length; i++) {
			legend_html += '<tr>' +
			'<td><input type="checkbox" onclick="hideDEF['+ i +']=!hideDEF['+ i +'];redraw(hideDEF, glob_data_DEFacility,\'data_DEF\');" checked="1"></td>'
			+ legend_tbl.rows[i].innerHTML
			+ '</tr>';
		}
		legend_html += "</tbody></table>";
		legend.innerHTML = legend_html;
		{/literal}
	{/if}

	{if $dataDED}
		var all_data = {$dataDED};
		glob_data_DEDepartment = all_data;
		var tick = {$tick};
		var ylabel = 'voc, lbs';
		var xlabel = 'date';
		var placeholder = $("#placeholderDEDepartment");
		var legend = $("#legendDEDepartment");
		var x = $("#xDEDepartment");
		var y = $("#yDEDepartment");

		{if $dataDED != "[]"}
		flotGraph(placeholder, legend, all_data, tick, ylabel, xlabel, y, x, true);
		{else}
		//document.getElementById('placeholderDE').innerHTML = "{$noDataTable}";
		$('#placeholderDEDepartment').html("{$noDataTable}");
		{/if}

		{literal}
		// create checkboxes in legend
		var legend = document.getElementById('legendDEDepartment');
		var legend_tbl = legend.getElementsByTagName('table')[0];
		var legend_html = '<table style="font-size: smaller; color: rgb(84, 84, 84);"><tbody>';
		for (var k=0; k<glob_data_DEDepartment.length; k++)
			hideDED[k] = false;
		for (var i = 0; i < legend_tbl.rows.length; i++) {
			legend_html += '<tr>' +
			'<td><input type="checkbox" onclick="hideDED['+ i +']=!hideDED['+ i +'];redraw(hideDED, glob_data_DEDepartment,\'data_DED\');" checked="1"></td>'
			+ legend_tbl.rows[i].innerHTML
			+ '</tr>';
		}
		legend_html += "</tbody></table>";
		legend.innerHTML = legend_html;
		{/literal}
	{/if}

	{if $dataPUF}
		all_data = {$dataPUF};
		glob_data_PUFacility = all_data;
		placeholder = $("#placeholderPUFacility");
		legend = $("#legendPUFacility");
		x = $("#xPUFacility");
		y = $("#yPUFacility");
		var ylabel = 'qty, lbs';

		{if $dataPUF != "[]"}
		flotGraph(placeholder, legend, all_data, tick, ylabel, xlabel, y, x, true);
		{else}
		document.getElementById('placeholderPUFacility').innerHTML = "{$noDataTable}";
		{/if}
		{literal}
		// create checkboxes in legend
		var legend = document.getElementById('legendPUFacility');
		var legend_tbl = legend.getElementsByTagName('table')[0];
		var legend_html = '<table style="font-size: smaller; color: rgb(84, 84, 84);"><tbody>';
		for (var k=0; k<glob_data_PUFacility.length; k++)
			hidePUF[k] = false;
		for (var i = 0; i < legend_tbl.rows.length; i++) {
			legend_html += '<tr>' +
			'<td><input type="checkbox" onclick="hidePUF['+ i +']=!hidePUF['+ i +'];redraw(hidePUF, glob_data_PUFacility,\'data_PUF\');" checked="1"></td>'
			+ legend_tbl.rows[i].innerHTML
			+ '</tr>';
		}
		legend_html += "</tbody></table>";
		legend.innerHTML = legend_html;
		{/literal}
	{/if}

	{if $dataPUD}
		all_data = {$dataPUD};
		glob_data_PUDepartment = all_data;
		placeholder = $("#placeholderPUDepartment");
		legend = $("#legendPUDepartment");
		x = $("#xPUDepartment");
		y = $("#yPUDepartment");
		var ylabel = 'qty, lbs';

		{if $dataPUD != "[]"}
		flotGraph(placeholder, legend, all_data, tick, ylabel, xlabel, y, x, true);
		{else}
		document.getElementById('placeholderPUDepartment').innerHTML = "{$noDataTable}";
		{/if}
		{literal}
		// create checkboxes in legend
		var legend = document.getElementById('legendPUDepartment');
		var legend_tbl = legend.getElementsByTagName('table')[0];
		var legend_html = '<table style="font-size: smaller; color: rgb(84, 84, 84);"><tbody>';
		for (var k=0; k<glob_data_PUDepartment.length; k++)
			hidePUD[k] = false;
		for (var i = 0; i < legend_tbl.rows.length; i++) {
			legend_html += '<tr>' +
			'<td><input type="checkbox" onclick="hidePUD['+ i +']=!hidePUD['+ i +'];redraw(hidePUD, glob_data_PUDepartment,\'data_PUD\');" checked="1"></td>'
			+ legend_tbl.rows[i].innerHTML
			+ '</tr>';
		}
		legend_html += "</tbody></table>";
		legend.innerHTML = legend_html;
		{/literal}
	{/if}
		var xx = document.getElementById('selGr').value;
	{literal}
		switch (xx){
			case '1':
				$('#graph2').hide();
				$('#graph3').hide();
				$('#graph4').hide();
				$('#graph5').hide();
				$('#graph6').hide();
				break;
			case '2':
				$('#graph1').hide();
				$('#graph3').hide();
				$('#graph4').hide();
				$('#graph5').hide();
				$('#graph6').hide();
				break;
			case '3':
				$('#graph2').hide();
				$('#graph1').hide();
				$('#graph4').hide();
				$('#graph5').hide();
				$('#graph6').hide();
				break;
			case '4':
				$('#graph2').hide();
				$('#graph3').hide();
				$('#graph1').hide();
				$('#graph5').hide();
				$('#graph6').hide();
				break;
			case '5':
				$('#graph2').hide();
				$('#graph3').hide();
				$('#graph4').hide();
				$('#graph1').hide();
				$('#graph6').hide();
				break;
			case '6':
				$('#graph2').hide();
				$('#graph3').hide();
				$('#graph4').hide();
				$('#graph5').hide();
				$('#graph1').hide();
				break;
		}

	{/literal}

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
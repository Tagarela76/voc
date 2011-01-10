<link href="modules/js/jquery-ui-1.8.2.custom/css/smoothness/jquery-ui-1.8.2.custom.css" rel="stylesheet" type="text/css"/>

<script type="text/javascript" src="modules/js/jquery-ui-1.8.2.custom/development-bundle/external/jquery.bgiframe-2.1.1.js"></script>
<script type="text/javascript" src="modules/js/jquery-ui-1.8.2.custom/development-bundle/ui/jquery.ui.core.js"></script>
<script type="text/javascript" src="modules/js/jquery-ui-1.8.2.custom/development-bundle/ui/jquery.ui.widget.js"></script>
<script type="text/javascript" src="modules/js/jquery-ui-1.8.2.custom/development-bundle/ui/jquery.ui.mouse.js"></script>
<script type="text/javascript" src="modules/js/jquery-ui-1.8.2.custom/development-bundle/ui/jquery.ui.draggable.js"></script>
<script type="text/javascript" src="modules/js/jquery-ui-1.8.2.custom/development-bundle/ui/jquery.ui.position.js"></script>
<script type="text/javascript" src="modules/js/jquery-ui-1.8.2.custom/development-bundle/ui/jquery.ui.resizable.js"></script>
<script type="text/javascript" src="modules/js/jquery-ui-1.8.2.custom/development-bundle/ui/jquery.ui.dialog.js"></script>
<script type="text/javascript" src="modules/js/emissionLog.js"></script>
<input type='hidden' id = 'popup_category'  value = "{$popup_category}" />
<input type='hidden' id = 'popup_category_id'  value = "{$popup_category_id}" />

<div id="emissionLog" title="EmissionLog" style='display:none;'>
		<select id="selectYear" align ="center">
		{section name=i loop=10}
			{math assign=yearEquation equation="y-x" x=$smarty.section.i.index y=$curYear}
				<option value='{$yearEquation}' {if $yearEquation ==$period.year}selected='selected'{/if}>{$yearEquation}</option>
		{/section}
		</select>
		<br>
		
		<table border="0" id='emmisionLogTable' class="popup_table" width=100% cellpadding=0 cellspacing=0>
			<tr class="table_popup_rule">
				<td>Month</td>
				<td>VOC Emissions </td>
				<td>Facility limit exceeded</td>
				<td>Department limit exceeded</td>
			</tr>
				
		</table>
		
</div>

<script type="text/javascript" src="modules/js/noxLog.js"></script>
<input type='hidden' id = 'nox_popup_category'  value = "{$popup_category}" />
<input type='hidden' id = 'nox_popup_category_id'  value = "{$popup_category_id}" />

<div id="noxLog" title="noxLog" style='display:none;'>
		<select id="noxSelectYear" align ="center">
		{section name=i loop=10}
			{math assign=yearEquation equation="y-x" x=$smarty.section.i.index y=$curYear}
				<option value='{$yearEquation}' {if $yearEquation ==$period.year}selected='selected'{/if}>{$yearEquation}</option>
		{/section}
		</select>
		<br>
		
		<table border="0" id='noxLogTable' class="popup_table" width=100% cellpadding=0 cellspacing=0>
			<tr class="table_popup_rule">
				<td>Month</td>
				<td>NOX Emissions </td>
				<td>Facility limit exceeded</td>
			</tr>
				
		</table>
		
</div>
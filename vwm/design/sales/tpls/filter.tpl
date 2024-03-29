<script type='text/javascript'>
	var filterFieldText='{$filterData.filterField}';	
	var filterConditionText='{$filterData.filterCondition}';	
	var filterValueText='{$filterData.filterValue}';			
	var filterStr='{$filterArray}';

	{if $dateFormat}
		var filterDateFormat = '{$dateFormat}';
	{else}
		var filterDateFormat = 'yy-mm-dd';
	{/if}
</script>

<script type="text/javascript" src="modules/js/jquery-ui-1.8.2.custom/development-bundle/ui/jquery.ui.core.js"></script>
<script type="text/javascript" src="modules/js/jquery-ui-1.8.2.custom/development-bundle/ui/jquery.ui.widget.js"></script>
<script type="text/javascript" src="modules/js/jquery-ui-1.8.2.custom/development-bundle/ui/jquery.ui.datepicker.js"></script>
<script type="text/javascript" src="modules/js/filter.js"></script>
<div>
	<form method='GET' action='' id='formID'>
		<table align='center'>
			<tr>
				<td>
					<span style='color:gray'>Field name</span>
				</td> 
				
				<td>
					<span style='color:gray'>Condition</span>
				</td> 
				
				<td>
					<span style='color:gray' id='valueLabel'>Value</span>
				</td> 
				
				<td>
					&nbsp;
				</td> 
			</tr>	
			<tr>
				<td>
					<select id='filterField' filterClass='All'>						
					</select>
				</td> 
				
				<td>
					<select id='filterCondition' name='filterCondition'>					
					</select>
				</td> 
				
				<td>				
					<input id='filterValue' name='filterValue' type='text'>
					<input id='filterValueAll' type='text' disabled='true' style='display:none'>
					<input id='filterValueDate' name='filterValue' type='text' disabled='true' style='display:none'>
				</td> 
				
				<td>
					<input type='submit' id='filterButton' class='button' value='Filter'>
				</td> 
			</tr>	
		</table>
		<input type="hidden" name='filterField' >
		<input type="hidden" name="action" value="browseCategory">
		<input type="hidden" name="category" value="{$request.category}">
		<input type="hidden" name="bookmark" value="{$request.bookmark}">
                {if ($request.category eq "salescontacts")&($request.subBookmark != "")}
                <input type="hidden" name="subBookmark" value="{$request.subBookmark}">   
                {/if}
		<input type="hidden" name="searchAction" value="filter">
		<input type="hidden" name="sort" value='{$sort}'>
	</form>
</div>
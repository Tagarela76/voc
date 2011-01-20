<form method='GET' action='' id='sortForm'>
	<input type='hidden' name='sort' id='sort'>
	<input type="hidden" name="action" value="browseCategory">
	<input type="hidden" name="category" value="{$request.category}">
	<input type="hidden" name="id" value="{$request.id}">
	<input type="hidden" name="bookmark" value="{$childCategory}">	
	{if $tab}
		<input type="hidden" name="tab" value="{$tab}">	
	{/if}
	{if $searchAction=='filter'}
		<input type="hidden" name='filterField' value='{$filterData.filterField}'>
		<input type="hidden" name='filterCondition' value='{$filterData.filterCondition}'>
		<input type="hidden" name='filterValue' value='{$filterData.filterValue}'>						
		<input type="hidden" name="searchAction" value="filter">
	{/if}
	{if $searchAction=='search'}
		<input type="hidden" name="q" value="{$searchQuery}">
		<input type="hidden" name="searchAction" value="search">
	{/if}		
</form>

{if $request.bookmark == 'solventplan'}
<form id="solventPlanForm" name="solventPlanForm" action="?action=browseCategory&category=facility&id={$request.id}&bookmark=solventplan&tab={$request.tab}" method="post" >			
<div align="center">
				{if $periodType == 'month'}
					<select name="selectMonth" {*onChange="document.forms['solventPlanForm'].submit();"*}>
						<option value="1" {if $period.month =='01'}selected='selected'{/if}>January</option>
						<option value="2" {if $period.month =='02'}selected='selected'{/if}>February</option>
						<option value="3" {if $period.month =='03'}selected='selected'{/if}>March</option>
						<option value="4" {if $period.month =='04'}selected='selected'{/if}>April</option>
						<option value="5" {if $period.month =='05'}selected='selected'{/if}>May</option>
						<option value="6" {if $period.month =='06'}selected='selected'{/if}>June</option>
						<option value="7" {if $period.month == '07'}selected='selected'{/if}>July</option>
						<option value="8" {if $period.month =='08'}selected='selected'{/if}>August</option>
						<option value="9" {if $period.month =='09'}selected='selected'{/if}>September</option>
						<option value="10" {if $period.month =='10'}selected='selected'{/if}>October</option>
						<option value="11" {if $period.month =='11'}selected='selected'{/if}>November</option>
						<option value="12" {if $period.month =='12'}selected='selected'{/if}>December</option>	
					</select>
				{/if}
				
				{if $periodType == 'quarter'}
					<select name="selectQuarter" {*onChange="document.forms['solventPlanForm'].submit();"*}>
						<option value="1" {if $period.quarter =='01'}selected='selected'{/if}>Quarter 1</option>
						<option value="2" {if $period.quarter =='02'}selected='selected'{/if}>Quarter 2</option>
						<option value="3" {if $period.quarter =='03'}selected='selected'{/if}>Quarter 3</option>
						<option value="4" {if $period.quarter =='04'}selected='selected'{/if}>Quarter 4</option>
					</select>
				{/if}

				{if $periodType == 'semi-year'}
					<select name="selectSemiyear" {*onChange="document.forms['solventPlanForm'].submit();"*}>
						<option value="1" {if $period.period =='01'}selected='selected'{/if}>first half-year</option>
						<option value="2" {if $period.period =='02'}selected='selected'{/if}>second half-year</option>					
					</select>
				{/if}			
				
				<select name="selectYear" {*onChange="document.forms['solventPlanForm'].submit();"*}>
					{section name=i loop=10}
						{math assign=yearEquation equation="y-x" x=$smarty.section.i.index y=$curYear}
						<option value='{$yearEquation}' {if $yearEquation ==$period.year}selected='selected'{/if}>{$yearEquation}</option>
					{/section}
				</select>
				
				<input type='submit' name='setPeriod' class="button" value='View' />
</div>
</form>
{/if}
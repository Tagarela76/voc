<form method='GET' action='' id='sortForm'>
	<input type='hidden' name='sort' id='sort'>
	<input type="hidden" name="action" value="browseCategory">
	<input type="hidden" name="categoryID" value="{$categoryType}">
	<input type="hidden" name="itemID" value="{$bookmarkType}">	
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
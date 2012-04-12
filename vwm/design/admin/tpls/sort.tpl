<form method='GET' action='' id='sortForm'>
	<input type='hidden' name='sort' id='sort'>
	{if $request.category eq 'logging'}
	<input type="hidden" name="action" value="{$request.action}">
	<input type="hidden" name="id" value="{$request.id}">
	{else}
	<input type="hidden" name="action" value="browseCategory">
	{/if}
	<input type="hidden" name="category" value="{$request.category}">
	{if $request.bookmark}<input type="hidden" name="bookmark" value="{$request.bookmark}">{/if}
	{if $request.subBookmark}<input type="hidden" name="subBookmark" value="{$request.subBookmark}">{/if}
	
	{if $searchAction=='filter'}
		<input type="hidden" name='filterField' value='{$filterData.filterField}'>
		<input type="hidden" name='filterCondition' value='{$filterData.filterCondition}'>
		<input type="hidden" name='filterValue' value='{$filterData.filterValue}'>						
		<input type="hidden" name="searchAction" value="filter">
	{/if}
	{if $searchAction=='search' || $request.q}
		<input type="hidden" name="q" value="{$searchQuery}">
		<input type="hidden" name="searchAction" value="search">
	{/if}		
</form>
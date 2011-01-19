<div style="margin:3 0 3 0">
	{if $request.action eq 'settings'}
        <input type="button" class="button" value="<< Back" onclick="{if $backUrl}location.href='{$backUrl}'{else}history.go(-1);return true;{/if}">	
	{/if}
	{if $request.category neq 'docs' &&
		$request.category neq 'wastestorage' &&
		$request.category neq 'logbook' &&
		$request.category neq 'carbonfootprint'}
	{if $permissions.viewCategory}
	{*<input type="button" class="button" value="View" onclick="location.href='?action=viewDetails&itemID={if $smarty.session.overCategoryType eq "facility"}company{elseif $smarty.session.overCategoryType eq "department"}facility{else}department{/if}&id={$smarty.session.CategoryID}'">*}
	
	{if $viewURL != null}
	<input type="button" class="button" value="View" onclick="location.href='{$viewURL}'">	
	{else} 
	<input type="button" class="button" value="View" onclick="location.href='?action=viewDetails&category={$request.category}&id={$request.id}'">	
	{/if}
	{/if}
	{if $permissions.deleteCategory}
	{*<input type="button" class="button" value="Delete" onclick="location.href='?action=deleteItem&itemID={if $smarty.session.overCategoryType eq "facility"}company{elseif $smarty.session.overCategoryType eq "department"}facility{else}department{/if}&itemsCount=1&item_0={$smarty.session.CategoryID}'">*}
	<input type="button" class="button" value="Delete" onclick="location.href='?action=deleteItem&category={$request.category}&id={$request.id}'">
	{/if}
	{if $request.category eq 'facility' || $request.category eq 'department'}
	<input type="button" class="button" value="Emission Graphs" onclick="location.href='?action=browseCategory&category={$request.category}&id={$request.id}&bookmark=emissionGraphs'">	
	{/if}
	{/if}
		
	{*<input type="hidden" name="itemID" value="{$smarty.session.overCategoryType}">
	<input type="hidden" name="id" value="{$smarty.session.CategoryID}">
	<input type="hidden" name="itemsCount" value="1">
	<input type="hidden" name="item_0" value="{$id}">*}

</div>

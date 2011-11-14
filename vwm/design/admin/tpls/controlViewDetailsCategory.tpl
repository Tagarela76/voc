<div style="margin:7 0 0 10">
{if $request.action != ""}
	{if $page}
	<input type="button" class="button" value="<< Back" 
	onclick="location.href='?action=browseCategory&companyID={$request.companyID}&supplierID={$request.supplierID}&subaction=Filter&category=tables&bookmark=product&page={$page}'">
	{else}	
	<input type="button" class="button" value="<< Back" 
	onclick="location.href='admin.php?action=browseCategory&category={if $request.category != 'users'}{$parent}&bookmark={$request.category}{else}{$request.category}&bookmark={$request.bookmark}{/if}'">
	{/if}
	{if $page}
	<input type="button" class="button" value="Edit" onclick="location.href='admin.php?action=edit&category={$request.category}{if $request.bookmark}&bookmark={$request.bookmark}{/if}&id={$request.id}&page={$page}'">	
	{else}
	{if $request.category != 'issue'}
	<input type="button" class="button" value="Edit" onclick="location.href='admin.php?action=edit&category={$request.category}{if $request.bookmark}&bookmark={$request.bookmark}{/if}&id={$request.id}'">
	{/if}
	{/if}
	{*do not allow delete emissionfactors*}
	{if $request.category != 'emissionFactor' && $request.category != 'issue'}
	<input type="button" class="button" value="Delete" onclick="location.href='admin.php?action=deleteItem&category={$request.category}{if $request.bookmark}&bookmark={$request.bookmark}{/if}&itemsCount=1&item_0={$request.id}&page={$page}'">
	{/if}
{/if}
</div>

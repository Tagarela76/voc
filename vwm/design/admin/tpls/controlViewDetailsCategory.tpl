<div style="margin:7 0 0 10">
{if $request.action != ""}
	<input type="button" class="button" value="<< Back" 
	onclick="location.href='admin.php?action=browseCategory&category={if $request.category != 'users'}{$parent}&bookmark={$request.category}{else}{$request.category}&bookmark={$request.bookmark}{/if}'">
	{if $request.category != 'issue'}
	<input type="button" class="button" value="Edit" onclick="location.href='admin.php?action=edit&category={$request.category}&id={$request.id}'">
	{/if}
	{*do not allow delete emissionfactors*}
	{if $request.category != 'emissionFactor' && $request.category != 'issue'}
	<input type="button" class="button" value="Delete" onclick="location.href='admin.php?action=deleteItem&category={$request.category}&itemsCount=1&item_0={$request.id}'">
	{/if}
{/if}
</div>

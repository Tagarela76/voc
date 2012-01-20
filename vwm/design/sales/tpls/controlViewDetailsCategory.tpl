<div style="margin:7 0 0 10">
{if $request.action != ""}
	<input type="button" class="button" value="<< Back" 
	onclick="location.href='sales.php?action=browseCategory&category={if $request.category != 'users'}{$parent}&bookmark={$request.category}{else}{$request.category}&bookmark={$request.bookmark}{/if}{if $request.subBookmark}&subBookmark={$request.subBookmark}{/if}{if $request.page }&page={$request.page}{/if}'">
	{if $request.category != 'issue'}
	<input type="button" class="button" value="Edit" onclick="location.href='sales.php?action=edit&category={$request.category}&id={$request.id}{if $request.subBookmark}&subBookmark={$request.subBookmark}{/if}{if $request.page }&page={$request.page}{/if}'">
	{/if}
	{*do not allow delete emissionfactors*}
	{if $request.category != 'emissionFactor' && $request.category != 'issue'}
	<input type="button" class="button" value="Delete" onclick="location.href='sales.php?action=deleteItem&category={$request.category}&itemsCount=1&item_0={$request.id}{if $request.subBookmark}&subBookmark={$request.subBookmark}{/if}{if $request.page }&page={$request.page}{/if}'">
	{/if}
{/if}
</div>

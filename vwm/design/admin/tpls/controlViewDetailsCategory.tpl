<div align="center" class="control_panel_padd">	
<div class="control_panel" class="logbg" align="left">
<div class="control_panel_center">
<div style="margin:7 0 7 10">
{if $request.action != ""}
	{if $page}
	<input type="button" class="button" value="<< Back" 
	onclick="location.href='?action=browseCategory{if $request.companyID}&companyID={$request.companyID}{/if}{if $request.supplierID}&supplierID={$request.supplierID}{if $request.subBookmark}&subBookmark={$request.subBookmark}{/if}{if $request.letterpage}&letterpage={$request.letterpage}{/if}{/if}&subaction=Filter&category=product&page={$page}'">
	{else}	
	<input type="button" class="button" value="<< Back" 
	onclick="location.href='admin.php?action=browseCategory&category={if $request.category != 'users' && $request.category != 'accessory'}{$parent}&bookmark={$request.category}{else}{$request.category}{if $request.bookmark}&bookmark={$request.bookmark}{/if}{/if}{if $request.category == 'pfpLibrary' }&subBookmark={$request.subBookmark}{/if}{if $request.letterpage}&letterpage={$request.letterpage}{/if}{if $request.page }&page={$request.page}{/if}{if $request.productCategory }&productCategory={$request.productCategory}{/if}{if $request.subBookmark}&subBookmark={$request.subBookmark}{/if}'">
	{/if}
	{if $page}
	<input type="button" class="button" value="Edit" onclick="location.href='admin.php?action=edit&category={$request.category}{if $request.bookmark}&bookmark={$request.bookmark}{/if}{if $request.subBookmark}&subBookmark={$request.subBookmark}{/if}{if $request.letterpage}&letterpage={$request.letterpage}{/if}&id={$request.id}&page={$page}'">	
	{else}
	{if $request.category != 'issue'}
	<input type="button" class="button" value="Edit" onclick="location.href='admin.php?action=edit&category={$request.category}{if $request.bookmark}&bookmark={$request.bookmark}{/if}{if $request.subBookmark}&subBookmark={$request.subBookmark}{/if}{if $request.letterpage}&letterpage={$request.letterpage}{/if}&id={$request.id}{if $request.page }&page={$request.page}{/if}{if $request.productCategory }&productCategory={$request.productCategory}{/if}'">
	{/if}
	{/if}
	{*do not allow delete emissionfactors*}
	{if $request.category !== 'emissionFactor' && $request.category !== 'issue' && $request.category !== 'pfpLibrary' && $request.category !== 'accessory'}
	<input type="button" class="button" value="Delete" onclick="location.href='admin.php?action=deleteItem&category={$request.category}{if $request.bookmark}&bookmark={$request.bookmark}{/if}{if $request.subBookmark}&subBookmark={$request.subBookmark}{/if}{if $request.letterpage}&letterpage={$request.letterpage}{/if}&itemsCount=1&item_0={$request.id}&page={$page}'">
	{else}
	<input type="button" class="button" value="Delete" onclick="location.href='admin.php?action=deleteItem&category={$request.category}{if $request.bookmark}&bookmark={$request.bookmark}{/if}{if $request.subBookmark}&subBookmark={$request.subBookmark}{/if}&itemsCount=1&id[]={$request.id}{if $request.letterpage}&letterpage={$request.letterpage}{/if}{if $request.page }&page={$request.page}{/if}{if $request.productCategory }&productCategory={$request.productCategory}{/if}'">	
	{/if}
{/if}
</div>
</div></div></div>
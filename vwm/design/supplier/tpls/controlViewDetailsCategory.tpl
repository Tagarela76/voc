<div style="margin:7 0 0 10">
{if $request.action != ""}
	<input type="button" class="button" value="<< Back" 
	onclick="location.href='{if $cancelUrl}{$cancelUrl}{else}supplier.php?action=browseCategory&category={$request.category}{if $request.bookmark}&bookmark={$request.bookmark}{/if}{if $request.subBookmark}&subBookmark={$request.subBookmark}{/if}{if $request.tab}&tab={$request.tab}{/if}{if $request.page }&page={$request.page}{/if}{if $request.jobberID}&jobberID={$request.jobberID}{/if}{if $request.supplierID}&supplierID={$request.supplierID}{/if}{/if}'">
	{if $request.category != 'issue' && $order.order_status != 3 && $order.order_status != 4 }
	<input type="button" class="button" value="Edit" onclick="location.href='supplier.php?action=edit&category={$request.category}{if $request.tab}&tab={$request.tab}{/if}{if $request.id}&id={$request.id}{/if}{if $request.companyID}&companyID={$request.companyID}{/if}{if $request.facilityID}&facilityID={$request.facilityID}{/if}{if $request.jobberID}&jobberID={$request.jobberID}{/if}{if $request.supplierID}&supplierID={$request.supplierID}{/if}{if $request.page }&page={$request.page}{/if}'">
	{/if}
	{*do not allow delete emissionfactors*}
	{if $request.category != 'emissionFactor' && $request.category != 'orders' && $request.category != 'clients'}
	<input type="button" class="button" value="Delete" onclick="location.href='supplier.php?action=deleteItem&category={$request.category}&itemsCount=1&item_0={$request.id}{if $request.subBookmark}&subBookmark={$request.subBookmark}{/if}{if $request.page }&page={$request.page}{/if}{if $request.jobberID}&jobberID={$request.jobberID}{/if}{if $request.supplierID}&supplierID={$request.supplierID}{/if}'">
	{/if}
{/if}
</div>

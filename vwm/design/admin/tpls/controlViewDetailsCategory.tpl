<div style="margin:7 0 0 10">
{if $currentOperation == ""}
	<input type="button" class="button" value="<< Back" onclick="location.href='admin.php?action=browseCategory&categoryID={$categoryID}&itemID={$itemID}'">
	<input type="button" class="button" value="Edit" onclick="location.href='admin.php?action=edit&categoryID={$categoryID}&itemID={$itemID}&id={$ID}'">
	{*do not allow delete emissionfactors*}
	{if $itemID != 'emissionFactor'}
	<input type="button" class="button" value="Delete" onclick="location.href='admin.php?action=deleteItem&categoryID={$categoryID}&itemID={$itemID}&itemsCount=1&item_0={$ID}'">
	{/if}
{/if}
</div>

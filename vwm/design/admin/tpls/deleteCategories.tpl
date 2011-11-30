
	{if $color eq "green"}
		{include file="tpls:tpls/notify/greenNotify.tpl" text=$message}
	{/if}
	{if $color eq "orange"}
		{include file="tpls:tpls/notify/orangeNotify.tpl" text=$message}
	{/if}
	{if $color eq "blue"}
		{include file="tpls:tpls/notify/blueNotify.tpl" text=$message}
	{/if}
	
<script>{literal}
function CheckCB(Element){
	if(document.getElementById) {
		if(document.getElementById(Element.id.replace('cb','tr'))){Element.checked = !Element.checked;}
	}
}	{/literal}
</script>	
<form method="get" action="">
	  {*shadow*}
	<div class="shadow">
	<div class="shadow_top">
	<div class="shadow_bottom">
	 {**}
<table class="users_min" align="center" cellspacing="0" cellpadding="0">
    <tr class="users_top_red" height="27">
        <td class="users_u_top_red" width="10%">
            Select
        </td>
        {if $request.category == "bookmarks"}
            <td class="users_u_top_r_red">
            Boomarks
            </td>
         {else}
        <td>
            ID Number
        </td>
        <td {if $itemForDelete[0].links}{/if}{if $itemForDelete[0].parentName}{else}class="users_u_top_r_red"{/if}>
            {$itemID}{if $itemForDelete[0].parentName}Industry Sub-Category{else}name{/if}
        </td>
		
        {if $itemForDelete[0].links}
        {if $itemForDelete[0].links.productCnt}
        <td >
            Total linked products
        </td>
	
        {/if}
        <td>
            Total linked inventories
        </td>
        <td>
            Total linked equipments
        </td>
        <td class="users_u_top_r_red">
            Total linked mixes
        </td>
        {/if}
        {/if}
		{if $itemForDelete[0].parentName}
		<td class="users_u_top_r_red">
            Industry Type
        </td>	
		{/if}
    </tr>
    {if $itemsCount > 0}  
    {section name=i loop=$itemForDelete} 
    <tr height="10px">
        {if $request.category == "bookmarks"}            
            <td class="border_users_l border_users_r border_users_b">
                <input type="checkbox" {if $itemForDelete[i]->id == 1}disabled="disabled"{/if} value="{$itemForDelete[i]->id}" name="item_{$smarty.section.i.index}" onclick="return CheckCB(this);">
            </td>            
            <td class="border_users_r border_users_b">
                <div style="width:100%;">
                {$itemForDelete[i]->name}
                </div>
            </td>
        {else}
        <td class="border_users_l border_users_r border_users_b">
            <input type="checkbox" checked="checked" value="{$itemForDelete[i].id}" name="item_{$smarty.section.i.index}" onclick="return CheckCB(this);">
        </td>
        <td class="border_users_r border_users_b" width="15%">
            <div style="width:100%;">
                {$itemForDelete[i].id}
            </div>
        </td>
        <td class="border_users_r border_users_b">
            <div style="width:100%;">
                {$itemForDelete[i].name}
            </div>
        </td>
        {if $itemForDelete[0].links}
        {if $itemForDelete[0].links.productCnt}
        <td class="border_users_r border_users_b">
            <div style="width:100%;">
                {$itemForDelete[i].links.productCnt}
            </div>
        </td>
        {/if}
        <td class="border_users_r border_users_b">
            <div style="width:100%;">
                {$itemForDelete[i].links.inventoryCnt}
            </div>
        </td>
        <td class="border_users_r border_users_b">
            <div style="width:100%;">
                {$itemForDelete[i].links.equipmentCnt}
            </div>
        </td>
        <td class="border_users_r border_users_b">
            <div style="width:100%;">
                {$itemForDelete[i].links.mixCnt}
            </div>
        </td>
        {/if}
        {/if}
		{if $itemForDelete[i].parentName}
		<td class="border_users_r border_users_b">
            <div style="width:100%;">
                {$itemForDelete[i].parentName}
            </div>
        </td>	
		{/if}
    </tr>
    {/section} 
    <tr class="">
        <td class="border_users_l">
        </td>
        <td colspan="6" class="border_users_r">
        </td>
    </tr>
    {else}
    {*BEGIN	EMPTY LIST*}
    <tr class="">
        <td class="border_users_l">
        </td>
        <td colspan="6" class="border_users_r">
            No {$itemID} selected.
        </td>
    </tr>
    {*END	EMPTY LIST*} 
    {/if} 
    <tr>
    	<td height="20" class="users_u_bottom"></td>
        <td colspan="6" class="users_u_bottom_r"></td>
    </tr>
</table>
	{*shadow*}	
		</div>
        </div>
        </div>
		{**}
<div align="center">
<div style="width:650px;" align="right">
	{if $itemsCount > 0}
	
	{*<!-- admin.php?action=browseCategory&category={$request.category}&bookmark={$request.bookmark} -->*}
{if $gobackAction eq 'browseCategory'}		
{*<input type="button" value="No" class="button" onclick="location.href='admin.php?action=browseCategory&category=tables&bookmark={$request.category}'"/>*}
<input type="button" value="No" class="button" onclick="location.href='admin.php?action=browseCategory&category={if $request.category == 'pfpLibrary'}{$request.bookmark}{else}{$request.category}{/if}{if $request.bookmark}&bookmark={if $request.category == 'pfpLibrary'}{$request.category}{else}{$request.bookmark}{/if}{if $request.subBookmark}&subBookmark={$request.subBookmark}{/if}{if $request.page}&page={$request.page}{/if}{if $request.page }&productCategory={$request.productCategory}{/if}{/if}'"/>
{else}
<input type="button" value="No" class="button" onclick="location.href='admin.php?action=browseCategory&category={if $request.bookmark=="contacts"}salescontacts{else}{$request.category}{/if}&bookmark={$request.bookmark}'">
{/if}
<input type="submit" name="confirm" value="Yes" class="button" style="margin:0 7px">
{*<input type="submit" name="confirm" value="No">*}


{else}{if $request.category == "product"}
<input type="button" value="OK" class="button" onclick="location.href='admin.php?action=browseCategory&category={$request.category}'">
{else}
<input type="button" value="OK" class="button" onclick="location.href='admin.php?action=browseCategory&category={if $request.bookmark=="contacts"}salescontacts{else}{if $request.category == 'pfpLibrary'}{$request.bookmark}{else}{$request.category}{/if}{if $request.bookmark}&bookmark={if $request.category == 'pfpLibrary'}{$request.category}{else}{$request.bookmark}{/if}{if $request.subBookmark}&subBookmark={$request.subBookmark}{/if}{if $request.page }&productCategory={$request.productCategory}{/if}{/if}{/if}'">
{/if}
{/if}
<input type="hidden" name="itemsCount" value="{$itemsCount}">
<input type="hidden" name="bookmark" value="{$request.bookmark}">
<input type="hidden" name="subBookmark" value="{$request.subBookmark}">
<input type="hidden" name="category" value="{$request.category}">
<input type="hidden" name="action" value="confirmDelete">
{if $itemType=="inventory" && $deleteWithProducts==true}
	<input type="hidden" name="deleteWithProducts" value="yes">
{/if}
</div>
</div>
</form>
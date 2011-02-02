
	{if $color eq "green"}
		{include file="tpls:tpls/notify/greenNotify.tpl" text=$message}
	{/if}
	{if $color eq "orange"}
		{include file="tpls:tpls/notify/orangeNotify.tpl" text=$message}
	{/if}
	{if $color eq "blue"}
		{include file="tpls:tpls/notify/blueNotify.tpl" text=$message}
	{/if}

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
        <td>
            ID Number
        </td>
        <td {if $itemForDelete[0].links}{else}class="users_u_top_r_red"{/if}>
            {$itemID} name
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
    </tr>
    {if $itemsCount > 0}  
    {section name=i loop=$itemForDelete} 
    <tr height="10px">
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
<input type="button" value="No" class="button" onclick="location.href='admin.php?action=browseCategory&category={$request.category}&bookmark={$request.bookmark}'">
<input type="submit" name="confirm" value="Yes" class="button" style="margin:0 7px">
{*<input type="submit" name="confirm" value="No">*}


{else}
<input type="button" value="OK" class="button" onclick="location.href='admin.php?action=browseCategory&category={$request.category}&bookmark={$request.bookmark}'">

{/if}
<input type="hidden" name="itemsCount" value="{$itemsCount}">
<input type="hidden" name="bookmark" value="{$request.bookmark}">
<input type="hidden" name="category" value="{$request.category}">
<input type="hidden" name="action" value="confirmDelete">
{if $itemType=="inventory" && $deleteWithProducts==true}
	<input type="hidden" name="deleteWithProducts" value="yes">
{/if}
</div>
</div>
</form>
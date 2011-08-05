
	{if $color eq "green"}
		{include file="tpls:tpls/notify/greenNotify.tpl" text=$message}
	{/if}
	{if $color eq "orange"}
		{include file="tpls:tpls/notify/orangeNotify.tpl" text=$message}
	{/if}
	{if $color eq "blue"}
		{include file="tpls:tpls/notify/blueNotify.tpl" text=$message}
	{/if}
<form method='POST' action='admin.php?action={$request.action}&category=bookmarks&bookmark={$request.bookmark}{if $request.subBookmark}&subBookmark={$request.subBookmark}{*else}&subBookmark={$request.bookmark*}{/if}'>
<!--form method="get" action=""-->
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
        <td class="users_u_top_r_red">
            Bookmark
        </td>
    </tr>

    {section name=i loop=$bookmarks}
    <tr height="10px">
        <td class="border_users_l border_users_r border_users_b">
            <input type="checkbox"  value="{$bookmarks[i]->id}" name="item_{$bookmarks[i]->id}" onclick="return CheckCB(this);">
        </td>
        <td class="border_users_r border_users_b">
            <div style="width:100%;">
                {$bookmarks[i]->name|capitalize:true}
            </div>
        </td>
 
    </tr>
    {/section} 
    <tr class="">
        <td class="border_users_l">
        </td>
        <td colspan="6" class="border_users_r">
        </td>
    </tr>

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

<input type='button' class="button" id='cancelButton' value='Cancel'>
<input type="submit" name="confirm" value="Yes" class="button" style="margin:0 7px">

<!--input type="hidden" name="itemsCount" value="{$itemsCount}">
<input type="hidden" name="bookmark" value="{$request.bookmark}">
<input type="hidden" name="category" value="{$request.category}">
<input type="hidden" name="action" value="confirmDelete"-->
{*if $itemType=="inventory" && $deleteWithProducts==true}
	<input type="hidden" name="deleteWithProducts" value="yes">
{/if*}
</div>
</div>
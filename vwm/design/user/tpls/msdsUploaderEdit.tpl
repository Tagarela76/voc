<div align="center" class="padd7">
    Assign product <b>{$productDetails.product_nr}</b>
    to MSDS sheet:
</div>
	
{*PAGINATION*}
{include file="tpls:tpls/pagination.tpl"}
{*/PAGINATION*}

<form action="" method="get">
    <table class="users" cellspacing="0" cellpadding="0" align="center">
        <tr class="users_top_yellowgreen" height="27">
            <td class="users_u_top_yellowgreen" width="1%">
                Select
            </td>
            <td class="">
                ID
            </td>
            <td class="">
                Sheet Name
            </td>
            <td class="">
                File Name
            </td>
            <td class="users_u_top_r_yellowgreen">
                View/Download
            </td>
        </tr>
        {if !$unlinkedMsdsSheets}
        {* NO FREE MSDS SHEETS *}
        <tr class="">
            <td colspan="5" class="border_users_l border_users_r middle" align="center" height="100">
                There are no unassigned MSDS sheets at VOC WEB MANAGER.
                <br>
                To upload new MSDS sheets go to <a href="?action=msdsUploader&step=main&itemID=equipment&id={$id}">MSDS Uploader.</a>
            </td>
        </tr>
        {else}
        {* LIST OF FREE SHEETS *}
        {section name=i loop=$unlinkedMsdsSheets}
        <tr class="hov_company" height="10px">
            <td class="border_users_b border_users_l">
                <input type="radio" value="{$unlinkedMsdsSheets[i].id}" name="selectedSheet" onClick="javascript:document.getElementById('saveButton').disabled = 0;">
            </td>
            <td class="border_users_l border_users_b">
                {$unlinkedMsdsSheets[i].id} 
            </td>
            <td class="border_users_l border_users_b">
                {$unlinkedMsdsSheets[i].name} 
            </td>
            <td class="border_users_l border_users_b">
                {$unlinkedMsdsSheets[i].realName} 
            </td>
            <td class="border_users_l border_users_r border_users_b">
                <a href='{$unlinkedMsdsSheets[i].msdsLink}' target="_blank">VIEW</a>
            </td>
        </tr>
        {/section}
        {/if}
        <tr>
            <td height="25" class="users_u_bottom">
                &nbsp;
            </td>
            <td class="border_users" colspan="3">
            </td>
            <td class="users_u_bottom_r">
            </td>
        </tr>
    </table>
    <div class="floatright buttonpadd">
        <input type="hidden" name="productID" value="{$productDetails.product_id}">
		<input type="hidden" name="action" value="msdsUploader">
		<input type="hidden" name="step" value="saveEdit">
		<input type='hidden' name='itemID' value={$request.category}>
		<input type='hidden' name='id' value={$request.id}>
		
		<input id="saveButton" type="submit" value="Save" class="button">
    </div>
</form>
		
{*PAGINATION*}
{include file="tpls:tpls/pagination.tpl"}
{*/PAGINATION*}
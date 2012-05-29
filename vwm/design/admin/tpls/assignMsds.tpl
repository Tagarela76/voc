<div align="center" class="padd7">
    Assign product <b>{$productDetails.product_nr} {$productDetails.name}</b>
    to MSDS sheet:
</div>

<link href="modules/js/autocomplete/styles.css" rel="stylesheet" type="text/css"/>
{literal}
	<script>
		var options, a;
		jQuery(function(){
			options = { serviceUrl:'modules/ajax/autocomplete.php',
						minChars:2,
						delimiter: /(,|;)\s*/,
						params: {category: '{/literal}{$request.action}{literal}'},
						deferRequestBy:300
			};
			a = $('#search').autocomplete(options);
		});
	</script>
{/literal}
{include file="tpls:tpls/search.tpl" overrideAction=$request.action}


{*PAGINATION*}
{include file="tpls:tpls/pagination.tpl"}
{*/PAGINATION*}

<form action="?action=assignMsds&category=tables" method="post">
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
						|
						<a href='{$deleteFromFSLink}&msdsID={$unlinkedMsdsSheets[i].id}'>DELETE FROM FS</a>
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
		<input type='hidden' name='subBookmark' value={$request.subBookmark}>
		<input type='hidden' name='letterpage' value={$request.letterpage}>
		<input type='hidden' name='productPage' value={$request.productPage}>
		<input type='hidden' name='id' value={$request.id}>

		<input id="saveButton" type="submit" value="Save" class="button">
    </div>
</form>

{*PAGINATION*}
{include file="tpls:tpls/pagination.tpl"}
{*/PAGINATION*}
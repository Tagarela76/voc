{if $message}
<table cellspacing="0" cellpadding="0" width="100%" height="37px">
    <tr>
        <td bgcolor="white" valign="bottom">
            {if $color eq "green"}
            {include file="tpls:tpls/notify/greenNotify.tpl" text=$message}
            {/if}
            {if $color eq "orange"}
            {include file="tpls:tpls/notify/orangeNotify.tpl" text=$message}
            {/if}
            {if $color eq "blue"}
            {include file="tpls:tpls/notify/blueNotify.tpl" text=$message}
            {/if}
        </td>
    </tr>
</table>
{/if}
<br />
<table class="users" height="5" cellspacing="0" cellpadding="0" align="center">
    <tr class="users_top" height="27px">
        <td class="users_u_top" width="60px">
			PFP Group Details
        </td>
        <td class="users_u_top_r" width="120px">
        </td>
    </tr>
    <tr class="hov_company" height="10px">
        <td class="border_users_b border_users_l border_users_r" >
			Name:
        </td>
        <td class="border_users_b border_users_r">
			{$pfpTypes->name|escape}
        </td>
    </tr>
	<tr class="hov_company" height="10px">
        <td class="border_users_b border_users_l border_users_r" >
			Departments are allowed to use this PFP Group:
        </td>
        <td class="border_users_b border_users_r">
			{$pfpDepartmentsName|escape}
        </td>
    </tr>
    <tr>
        <td colspan="5" class="border_users_l border_users_r">
            &nbsp;
        </td>
    </tr>
    <tr>
        <td class="users_u_bottom">
        </td>
        <td class="users_u_bottom_r">
        </td>
    </tr>
</table>
<br /><br /><br />
<div style="text-align: center;">
	{*SEARCH*}
		<link href="modules/js/autocomplete/styles.css" rel="stylesheet" type="text/css"/>
		{literal}
			<script>
				var options, a;
				jQuery(function(){
					options = { serviceUrl:'modules/ajax/autocomplete.php',
						minChars:2,
						delimiter: /(,|;)\s*/,
						params: { {/literal}
						facilityID :'{$request.facilityID}{literal}',
						id:'{/literal}{$request.id}{literal}',
						category:'{/literal}{$request.category}{literal}',
						pfpTypes:'{/literal}{$request.pfpGroup}{literal}'},
						deferRequestBy:300
					};
					a = $('#search').autocomplete(options);
				});
			</script>
		{/literal}
		{include file="tpls:tpls/search.tpl"}
{*/SEARCH*}
</div>

{if $isAllPFP}
	&nbsp;&nbsp;&nbsp;&nbsp;<input type="button" class="button" name="action" value="Assign" onclick="assignPFP2Type('assign')">
{else}
	&nbsp;&nbsp;&nbsp;&nbsp;<input type="button" class="button" name="action" value="Unassign" onclick="assignPFP2Type('unassign')">
{/if}
<div class="link_bookmark" style="float:right;">
	{if $isAllPFP}
		<a href="?action=viewDetails&category=pfpTypes&id={$pfpTypes->id}&facilityID={$smarty.request.facilityID}&pfpGroup=group"> Group </a> <a href="?action=viewDetails&category=pfpTypes&id={$pfpTypes->id}&facilityID={$smarty.request.facilityID}&pfpGroup=all" class="active_link"> All </a>
	{else}
		<a href="?action=viewDetails&category=pfpTypes&id={$pfpTypes->id}&facilityID={$smarty.request.facilityID}&pfpGroup=group" class="active_link"> Group </a>  <a href="?action=viewDetails&category=pfpTypes&id={$pfpTypes->id}&facilityID={$smarty.request.facilityID}&pfpGroup=all"> All </a>
	{/if}
</div>
    <input type="hidden" id="pfptypeID" name="pfptypeID" value="{$pfpTypes->id}" />
    <input type="hidden" id="facilityID" name="facilityID" value="{$smarty.request.facilityID}" />
{*PAGINATION*}
{include file="tpls:tpls/pagination.tpl"}
{*/PAGINATION*}
<table class="users" height="200" cellspacing="0" cellpadding="0" align="center" id = "pfpContainer">
    <tr class="users_top" height="27px">
        <td class="users_u_top" width="60px">
            <span style='display:inline-block; width:60px;'> <a onclick="CheckAll(this)" style='color:white'>All</a>/<a style='color:white' onclick="unCheckAll(this)" >None</a></span>
        </td>

        <td class="" width="60px">
        	<a style='color:white;'>
            	<div style='width:100%;  color:white;'>
                	ID
				</div>
			</a>
        </td>
		<td class="" >
        	<a style='color:white;'>
            	<div style='width:100%;  color:white;'>
                	Manufacturers/Suppliers
				</div>
			</a>
        </td>
        <td class="" >
        	<a style='color:white;'>
            	<div style='width:100%;  color:white;'>
                	Description
				</div>
			</a>
        </td>
        <td class="" width="120px">
        	<a style='color:white;'>
            	<div style='width:100%;  color:white;'>
                	Ratio
				</div>
			</a>
        </td>
        <td class="users_u_top_r" width="120px">
        	<a style='color:white;'>
            	<div style='width:100%;  color:white;'>
                	Products count
				</div>
			</a>
        </td>
    </tr>
{if $pfps|@is_array and $pfps|@count > 0}
    {*BEGIN LIST*}
    {foreach from=$pfps item=pfp}
    {assign var='pfpid' value=$pfp->getId()}
    {assign var='departmentID' value=$smarty.request.id}
	<!-- Begin Highlighting -->
    <tr class="hov_company" height="10px">

        <td class="border_users_b border_users_l border_users_r" >
			<input type="checkbox" value="{$pfp->getId()}" name="pfpId[]">
        </td>

        <td class="border_users_b border_users_r" >
            <div style="width:100%;">
                {$pfp->getId()} &nbsp;
            </div>
        </td>
		<td class="border_users_b border_users_r" >
            <div style="width:100%;">
                {assign var="pfpProducts" value=$pfp->getProducts()}
				{foreach from=$pfpProducts item=item}
					{if $item->isPrimary()}
						{$item->supplier|escape} &nbsp;
					{/if}
				{/foreach}
            </div>
        </td>
        <td class="border_users_b border_users_r">
            <div style="width:100%;" align="left">
                {$pfp->getDescription()} &nbsp;
            </div>
            <div>
                <table style="font-size: 10; color: #8B7765;">
                    {assign var="pfpProducts" value=$pfp->getProducts()}
                    {foreach from=$pfpProducts item=item}
                        <tr>
                            <td>{$item->product_nr}</td>
                            <td>{$item->name}</td>
                        </tr>
                    {/foreach}
                </table>
            </div>
        </td>
        <td class="border_users_b border_users_r">
            <div style="width:100%;">
                {$pfp->getRatio()} &nbsp;{if $pfp->isRangePFP}(with range){/if}
            </div>
        </td>
        <td class="border_users_b border_users_r">
            <div style="width:100%;">
                {$pfp->getProductsCount()} &nbsp;
            </div>
        </td>
    </tr>
    {/foreach}
    <tr>
        <td colspan="6" class="border_users_l border_users_r">
            &nbsp;
        </td>
    </tr>
    {*END LIST*}
{else}
    {*BEGIN	EMPTY LIST*}
    <tr class="">
        <td colspan="6"class="border_users_l border_users_r" align="center">
            No pre formulated products with this type
        </td>
    </tr>
    {*END	EMPTY LIST*}
{/if}
    <tr>
        <td class="users_u_bottom">
        </td>
        <td colspan="4" height="15" class="border_users">
        </td>
        <td class="users_u_bottom_r">
        </td>
    </tr>
</table>
{*PAGINATION*}
{include file="tpls:tpls/pagination.tpl"}
{*/PAGINATION*}
</form>	{*close form that was opened at controlInsideDepartment.tpl*}

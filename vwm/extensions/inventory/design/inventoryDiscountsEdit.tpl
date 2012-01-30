{*ajax-preloader*}
<div style="height:16px;text-align:center;">
	<div id="preloader" style="display:none">
		<img src='images/ajax-loader.gif'>
	</div>
</div>

{if $color eq "green"}
{include file="tpls:tpls/notify/greenNotify.tpl" text=$message}
{/if}
{if $color eq "orange"}
{include file="tpls:tpls/notify/orangeNotify.tpl" text=$message}
{/if}
{if $color eq "blue"}
{include file="tpls:tpls/notify/blueNotify.tpl" text=$message}
{/if}

<div style="padding:7px;">
	{*if $parentCategory == 'facility'}
    <form method='POST' action='?action={$request.action}&category=inventory&id={$request.id}&facilityID={$request.facilityID}&tab={$inventory->getType()}'>    	
    {else $parentCategory == 'department'}
	<form method='POST' action='?action={$request.action}&category=inventory&id={$request.id}&departmentID={$request.departmentID}&tab={$inventory->getType()}'>
	{/if*}
	<form method='POST' action='?action={$request.action}&category=inventory&id={$request.id}&facilityID={$request.facilityID}&tab={$request.tab}'>
        <table class="users" align="center" cellpadding="0" cellspacing="0">
            <tr class="users_u_top_size users_top">
                <td class="users_u_top" width="30%">
                    <span>{if $request.action eq "addItem"}Adding for a new inventory{else}Editing supplier disscount{/if}</span>
                </td>
                <td class="users_u_top_r">
                </td>
            </tr>
            <tr>
                <td class="border_users_r border_users_l border_users_b" height="20">
                    Supplier Name :
                </td>
                <td class="border_users_r border_users_b">

					{$supplier.supplier}
				
                </td>
            </tr>
	
            <tr>
                <td class="border_users_r border_users_l border_users_b" height="20">
                    Discount :
                </td>
                <td class="border_users_r border_users_b">

                    <div align="left">
                        <input type='text' name='discount' value='{$supplier.discount}'> %
                    </div>
					{if $validStatus.summary eq 'false'}
                    {if $validStatus.inventory_desc eq 'failed'}
                    {*ERORR*}
                    <div class="error_img">
                        <span class="error_text">Error!</span>
                    </div>
                    {*/ERORR*}
                    {/if}
                    {/if}

                </td>
            </tr>				
			
            <tr>
                <td class="users_u_bottom">
                </td>
                <td bgcolor="" height="20" class="users_u_bottom_r">
                </td>
            </tr>
        </table>
					
        <div align="right" class="margin7">

				<input type='button' class="button" value='Cancel' onclick="location.href='?action=browseCategory&category=facility&id={$request.facilityID}&bookmark=inventory&tab={$request.tab}'">
			
      	
            <input type='submit' class="button" value='Save'>
			<input type='hidden' name="facilityID" value='{$request.facilityID}'>
			<input type='hidden' name="supplier_id" value='{$request.id}'>
			<input type='hidden' name="discount_id" value='{$supplier.discount_id}'>
			<input type='hidden' name="supplier" value='{$supplier.supplier}'>
        </div>									
    </form>
</div>

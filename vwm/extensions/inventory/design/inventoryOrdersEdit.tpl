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
                    <span>{if $request.action eq "addItem"}Adding for a new inventory order{else}Editing inventory order{/if}</span>
                </td>
                <td class="users_u_top_r">
                </td>
            </tr>
            <tr>
                <td class="border_users_r border_users_l border_users_b" height="20">
                    Order Name :
                </td>
                <td class="border_users_r border_users_b">

					{$order.order_name}
				
                </td>
            </tr>
	
            <tr>
                <td class="border_users_r border_users_l border_users_b" height="20">
                    Status :
                </td>
                <td class="border_users_r border_users_b">

                    <div align="left">
                            <select name="status" id="status">
							{section name=i loop=$status}
                                <option value='{$status[i].status_id}' {if $status[i].status_id  eq $order.order_status}  selected="selected" {/if}> {$status[i].status_name}  </option>
                            {/section}
                            </select>
							
					</div>
                </td>
            </tr>	
            <tr>
                <td class="border_users_r border_users_l border_users_b" height="20">
                    Amount :
                </td>
                <td class="border_users_r border_users_b">

					{$order.order_amount}
				
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
 <span style="color: red; font-size: 14px;">{if $request.action eq "addItem"}Be careful! Will be sent a letter with order's changes to the supplier!{else}
	 Be careful! You must to phone to the supplier, to inform about changes in the order! {/if}</span>
				<input type='button' class="button" value='Cancel' onclick="location.href='?action=browseCategory&category=facility&id={$request.facilityID}&bookmark=inventory&tab={$request.tab}'">
			
      	
            <input type='submit' class="button" value='Save'>
			<input type='hidden' name="facilityID" value='{$request.facilityID}'>
			<input type='hidden' name="order_id" value='{$order.order_id}'>
			<input type='hidden' name="amount" value='{$order.amount}'>
                  
        </div>	
			 
    </form>
</div>

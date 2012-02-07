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
{literal}
<script>
									
$(document).ready(function() {
	$('#product_nr').attr('value', $('#order_product_id > option:selected').attr('title'));
});
</script>
{/literal}
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
                    Product :
                </td>
                <td class="border_users_r border_users_b">

								{*NICE PRODUCT LIST*}	
								<select name="order_product_id" id="order_product_id" class="addInventory" onchange="getPoduct_nr();" title>
									{*<option selected="selected" >Select Product</option>*}
									{if $products}				

											{section name=i loop=$products}

												<option title="{$products[i].product_nr}" value='{$products[i].product_id}' {if $productsArr[i].disabled}disabled="disabled"{/if}> {$products[i].supplier} >> {$products[i].product_nr} >> {$products[i].name} </option>

											{/section}
																			
									{else}
										<option value='0'> no products </option>
									{/if}
								</select>	
								{literal}
									<script>
									function getPoduct_nr(){
										$('#product_nr').attr('value', $('#order_product_id > option:selected').attr('title'));

									}
									</script>
								{/literal}
								{if $request.error eq "exist"}<span style="color: red; font-size: 14px;">Order for this product already exist!</span>{/if}									
                </td>
            </tr>				
            <tr>
                <td class="border_users_r border_users_l border_users_b" height="20">
                    Amount :
                </td>
                <td class="border_users_r border_users_b">

					<input type='text' name="order_amount" value=''>
				
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
 <span style="color: red; font-size: 14px;">{if $request.action eq "addItem"}Be careful! Will be sent a letter with order's info to the supplier!{else}
	 Be careful! You must to phone to the supplier, to inform about new order! {/if}</span>
				<input type='button' class="button" value='Cancel' onclick="location.href='?action=browseCategory&category=facility&id={$request.facilityID}&bookmark=inventory&tab={$request.tab}'">
			
      	
            <input type='submit' class="button" value='Save'>
			<input type='hidden' name="facilityID" value='{$request.facilityID}'>
			<input type='hidden' name="order_id" value='{$order.order_id}'>
			<input type="hidden" name="product_nr" id="product_nr"  value=""/>
                  
        </div>	
			 
    </form>
</div>

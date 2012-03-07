{if $color eq "green"}
{include file="tpls:tpls/notify/greenNotify.tpl" text=$message}
{/if}
{if $color eq "orange"}
{include file="tpls:tpls/notify/orangeNotify.tpl" text=$message}
{/if}
{if $color eq "blue"}
{include file="tpls:tpls/notify/blueNotify.tpl" text=$message}
{/if}

<form method="POST" action="{$action}" style="margin:0px">
    {*shadow
    <div class="shadow">
        <div class="shadow_top">
            <div class="shadow_bottom">
                непонятно нужно или нет, Аллаааааааааа!*}
                <table class="users_min" align="center" cellspacing="0" cellpadding="0">
                    {**header**}
                    <tr class="users_header_red">
                        <td width="10%"><div class="users_header_red_l">
                            <div>ID Number</div></div>
                        </td>

                        <!-- Special Headers -->                                               
                         {if $itemType eq "order" }
                            <td><div class="users_header_red_r"><div>Order Name</div></div></td>
                         {/if}                        
                                         
                    </tr>
                    {**/header**}  
                    

                
                    <tr class="hov_company border_users_b border_users_r">

                        <td class="border_users_l">
                            <div style="width:100%;">

                                {$order.order_id}

                            </div>
                        </td>

                        <td>
                            <div style="width:100%;">
                                {$order.order_name}
                            </div>
                        </td>

                    </tr>
                

{if $order == ''}
                    <tr>
                        <td colspan="2" class="border_users_l border_users_r" align="center">
                            No orders selected.
                        </td>
                    </tr>
{/if}             
                    <tr class="users_u_top_size">

                        <td bgcolor="" height="20" class="users_u_bottom " >&nbsp;</td>
                   
                        <td bgcolor="" height="20" class="users_u_bottom_r ">
                            &nbsp;
                        </td>
                    </tr>
					
                </table>

    <div align="center" class="padd7">
		 {if $check && $check == 'false'}<span style="color: red; font-size: 14px;"> Error! Can't convert product usage to stock unit type, because the density  do not specify! Call John! </span>{/if}
        <div align="right" style="width:690px ;padding:0 50px" >
				<input type="button" value="No" class="button" onclick="location.href='{$cancelUrl}'">
				<input type="submit" name="confirm" value="Yes" class="button">				

				<input type='hidden' name='facility_id' value="{$order.order_facility_id}">
				<input type='hidden' name='order_id' value="{$order.order_id}">


        </div>
    </div>
</form>
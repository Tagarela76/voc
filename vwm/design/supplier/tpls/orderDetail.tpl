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
    <table class="users" width="100%" cellpadding="0" cellspacing="0" align="center">
        <tr class="users_top_yellowgreen">
            <td class="users_u_top_yellowgreen" width="27%" height="30">
                <span>View Order</span>
            </td>
            <td class="users_u_top_r_yellowgreen" width="300">
            </td>
        </tr>
        <tr>
            <td class="border_users_l border_users_b" height="20">
                Order Name :
            </td>
            <td class="border_users_l border_users_r border_users_b">
                <div align="left">
                    &nbsp;{$order.order_name}
                </div>
            </td>
        </tr>
        <tr>
            <td class="border_users_l border_users_b" height="20">
                Amount : 
            </td>
            <td class="border_users_l border_users_r border_users_b">
                <div align="left">
                    &nbsp;{$order.order_amount} {$order.type}
                </div>
            </td>
        </tr>
        <tr>
            <td class="border_users_l border_users_b" height="20">
                Status : 
            </td>
            <td class="border_users_l border_users_r border_users_b">
                <div align="left">
                    &nbsp;{if $order.order_status == 1}In Progress {elseif $order.order_status == 2}Confirm{elseif $order.order_status == 3}Completed{elseif $order.order_status == 4}Canceled{/if}
                </div>
            </td>
        </tr>
{if $request.action != 'processororder'}		
        <tr>
            <td class="border_users_l border_users_b" height="20">
                Price : 
            </td>
            <td class="border_users_l border_users_r border_users_b">
                <div align="left">
                    &nbsp;$ {$order.order_price} 
                </div>
            </td>
        </tr>	
        <tr>
            <td class="border_users_l border_users_b" height="20">
                Discount : 
            </td>
            <td class="border_users_l border_users_r border_users_b">
                <div align="left">
                    &nbsp;{$order.order_discount} %
                </div>
            </td>
        </tr>
{/if}		
        <tr>
            <td class="border_users_l border_users_b" height="20">
                Total : 
            </td>
            <td class="border_users_l border_users_r border_users_b">
                <div align="left">
                    &nbsp;$ {$order.order_total}
                </div>
            </td>
        </tr>	
		
        <tr>
            <td class="border_users_l border_users_b" height="20">
                Date : 
            </td>
            <td class="border_users_l border_users_r border_users_b">
                <div align="left">
                    &nbsp;{$order.order_created_date}
                </div>
            </td>
        </tr>		

        <tr>
            <td height="15" class="users_u_bottom">
            </td>
            <td height="15" class="users_u_bottom_r">
            </td>
        </tr>
    </table>
</div>

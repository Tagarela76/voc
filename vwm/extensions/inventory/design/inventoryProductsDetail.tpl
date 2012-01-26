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
                <span>View details</span>
            </td>
            <td class="users_u_top_r_yellowgreen" width="300">
            </td>
        </tr>
        <tr>
            <td class="border_users_l border_users_b" height="20">
                Product Name :
            </td>
            <td class="border_users_l border_users_r border_users_b">
                <div align="left">
                    &nbsp;{$product->get_product_nr()}
                </div>
            </td>
        </tr>
        <tr>
            <td class="border_users_l border_users_b" height="20">
                In stock : 
            </td>
            <td class="border_users_l border_users_r border_users_b">
                <div align="left">
                    &nbsp;{$product->get_in_stock()}
                </div>
            </td>
        </tr>
        <tr>
            <td class="border_users_l border_users_b" height="20">
                Usage : 
            </td>
            <td class="border_users_l border_users_r border_users_b">
                <div align="left">
                    &nbsp;{$product->get_usage()}
                </div>
            </td>
        </tr>
				

                </table>
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

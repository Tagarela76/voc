{if $color eq "green"}
{include file="tpls:tpls/notify/greenNotify.tpl" text=$message}
{/if}
{if $color eq "orange"}
{include file="tpls:tpls/notify/orangeNotify.tpl" text=$message}
{/if}
{if $color eq "blue"}
{include file="tpls:tpls/notify/blueNotify.tpl" text=$message}
{/if}
<div class="padd7">
    <table class="users" align="center" cellpadding="0" cellspacing="0">
        <tr class="users_top_yellowgreen users_u_top_size">
            <td class="users_u_top_yellowgreen" width="37%" height="30">
                <span>View details</span>
            </td>
            <td class="users_u_top_r_yellowgreen" width="300">
            </td>
        </tr>
        <tr>
            <td class="border_users_l border_users_b" height="20">
                Accessory NR :
            </td>
            <td class="border_users_l border_users_b border_users_r">
                <div align="left">
                    &nbsp;{$accessory.id}
                </div>
            </td>
        </tr>
        <tr>
            <td class="border_users_l border_users_b" height="20">
                Description :
            </td>
            <td class="border_users_l border_users_b border_users_r">
                <div align="left">
                    &nbsp;{$accessory.name}
                </div>
            </td>
        </tr>
        <tr>
            <td height="20" class="users_u_bottom">
            </td>
            <td colspan="2" height="20" class="users_u_bottom_r">
            </td>
        </tr>
    </table>
</div>
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
                Equipment NR :
            </td>
            <td class="border_users_l border_users_b border_users_r">
                <div align="left">
                    &nbsp;{$equipment.equipment_nr}
                </div>
            </td>
        </tr>
        <tr>
            <td class="border_users_l border_users_b" height="20">
                <b>Description :</b>
            </td>
            <td class="border_users_l border_users_b border_users_r">
                <div align="left">
                    <b>&nbsp;{$equipment.equip_desc}</b>
                </div>
            </td>
        </tr>
        
        {if $show.inventory}
        	{include file="tpls:inventory/design/viewEquipment.tpl}
        {/if}
                
        <tr>
            <td class="border_users_l border_users_b" height="20">
                Permit :
            </td>
            <td class="border_users_l border_users_b border_users_r">
                <div align="left">
                    &nbsp;{$equipment.permit}
                </div>
            </td>
        </tr>
        <tr>
            <td class="border_users_l border_users_b" height="20">
                Expire :
            </td>
            <td class="border_users_l border_users_b border_users_r">
                <div align="left">
                    &nbsp;{$equipment.expire}
                </div>
            </td>
        </tr>
        <tr>
            <td class="border_users_l border_users_b" height="20">
                Daily :
            </td>
            <td class="border_users_l border_users_b border_users_r">
                <div align="left">
                    &nbsp;{$equipment.daily}
                </div>
            </td>
        </tr>
        <tr>
            <td class="border_users_l border_users_b" height="20">
                Department track :
            </td>
            <td class="border_users_l border_users_b border_users_r">
                <div align="left">
                    &nbsp;{$equipment.dept_track}
                </div>
            </td>
        </tr>
        <tr>
            <td class="border_users_l border_users_b" height="20">
                Facility track :
            </td>
            <td class="border_users_l border_users_b border_users_r">
                <div align="left">
                    &nbsp;{$equipment.facility_track}
                </div>
            </td>
        </tr>
        <tr>
            <td class="border_users_l border_users_b" height="20">
                Status :
            </td>
            <td class="border_users_l border_users_b border_users_r">
                <div align="left">
                    &nbsp;{$equipment.status}
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
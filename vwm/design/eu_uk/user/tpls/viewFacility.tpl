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
            <td class="users_u_top_yellowgreen" width="27%">
                <span>View facility details</span>
            </td>
            <td class="users_u_top_r_yellowgreen" width="300">
            </td>
        </tr>
        <tr>
            <td class="border_users_l border_users_b" height="20">
                EPA ID number:
            </td>
            <td class="border_users_l border_users_b border_users_r">
                <div align="left">
                    &nbsp;{$facility.epa}
                </div>
            </td>
        </tr>
        <tr>
            <td class="border_users_l border_users_b" height="20">
                VOC monthly limit:
            </td>
            <td class="border_users_l border_users_b border_users_r">
                <div align="left">
                    &nbsp;{$facility.voc_limit}
                </div>
            </td>
        </tr>
        <tr>
            <td class="border_users_l border_users_b" height="20">
                VOC annual limit:
            </td>
            <td class="border_users_l border_users_b border_users_r">
                <div align="left">
                    &nbsp;{$facility.voc_annual_limit}
                </div>
            </td>
        </tr>
        <tr>
            <td class="border_users_l border_users_b" height="20">
                Facility name:
            </td>
            <td class="border_users_l border_users_b border_users_r">
                <div align="left">
                    &nbsp;{$facility.name} 
                </div>
            </td>
        </tr>
        <tr>
            <td class="border_users_l border_users_b" height="20">
                Address:
            </td>
            <td class="border_users_l border_users_b border_users_r">
                <div align="left">
                    &nbsp;{$facility.address}
                </div>
            </td>
        </tr>
        <tr>
            <td class="border_users_l border_users_b" height="20">
                City:
            </td>
            <td class="border_users_l border_users_b border_users_r">
                <div align="left">
                    &nbsp;{$facility.city}
                </div>
            </td>
        </tr>
        <tr>
            <td class="border_users_l border_users_b" height="20">
                County:
            </td>
            <td class="border_users_l border_users_b border_users_r">
                <div align="left">
                    &nbsp;{$facility.county}
                </div>
            </td>
        </tr>
       {* <tr>
            <td class="border_users_l border_users_b" height="20">
                State/Providence:
            </td>
            <td class="border_users_l border_users_b border_users_r">
                <div align="left">
                    &nbsp;{$facility.state}
                </div>
            </td>
        </tr>*}
        <tr>
            <td class="border_users_l border_users_b" height="20">
                (Zip/Postal code):
            </td>
            <td class="border_users_l border_users_b border_users_r">
                <div align="left">
                    &nbsp;{$facility.zip}
                </div>
            </td>
        </tr>
        <tr>
            <td class="border_users_l border_users_b" height="20">
                Country:
            </td>
            <td class="border_users_l border_users_b border_users_r">
                <div align="left">
                    &nbsp;{$facility.country}
                </div>
            </td>
        </tr>
        <tr>
            <td class="border_users_l border_users_b" height="20">
                Phone:
            </td>
            <td class="border_users_l border_users_b border_users_r">
                <div align="left">
                    &nbsp;{$facility.phone}
                </div>
            </td>
        </tr>
        <tr>
            <td class="border_users_l border_users_b" height="20">
                Fax:
            </td>
            <td class="border_users_l border_users_b border_users_r">
                <div align="left">
                    &nbsp;{$facility.fax} 
                </div>
            </td>
        </tr>
        <tr>
            <td class="border_users_l border_users_b" height="20">
                Email:
            </td>
            <td class="border_users_l border_users_b border_users_r">
                <div align="left">
                    &nbsp;{$facility.email}
                </div>
            </td>
        </tr>
        <tr>
            <td class="border_users_l border_users_b" height="20">
                Contact:
            </td>
            <td class="border_users_l border_users_b border_users_r">
                <div align="left">
                    &nbsp;{$facility.contact} 
                </div>
            </td>
        </tr>
        <tr>
            <td class="border_users_l border_users_b" height="20">
                Title:
            </td>
            <td class="border_users_l border_users_b border_users_r">
                <div align="left">
                    &nbsp;{$facility.title}
                </div>
            </td>
        </tr>
        <tr>
            <td height="20" class="users_u_bottom">
            </td>
            <td height="20" class="users_u_bottom_r">
            </td>
        </tr>
    </table>
    <div align="right">
    </div>    
</div>
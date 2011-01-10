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
                <span>View details</span>
            </td>
            <td class="users_u_top_r_yellowgreen" width="300">
            </td>
        </tr>
        <tr>
            <td class="border_users_l border_users_b us_gray" height="23px">
                Company name:
            </td>
            <td class="border_users_r border_users_l border_users_b">
                <div align="left">
                    &nbsp;{$company.name} 
                </div>
            </td>
        </tr>
        <tr>
            <td class="border_users_l border_users_b us_gray" height="23px">
                Address:
            </td>
            <td class="border_users_r border_users_l border_users_b">
                <div align="left">
                    &nbsp;{$company.address}
                </div>
            </td>
        </tr>
        <tr>
            <td class="border_users_l border_users_b us_gray" height="23px">
                City:
            </td>
            <td class="border_users_r border_users_l border_users_b">
                <div align="left">
                    &nbsp;{$company.city}
                </div>
            </td>
        </tr>
        <tr>
            <td class="border_users_l border_users_b us_gray" height="23px">
                County:
            </td>
            <td class="border_users_r border_users_l border_users_b">
                <div align="left">
                    &nbsp;{$company.county}
                </div>
            </td>
        </tr>
        <tr>
            <td class="border_users_l border_users_b us_gray" height="23px">
                Country:
            </td>
            <td class="border_users_r border_users_l border_users_b">
                <div align="left">
                    &nbsp;{$company.country}
                </div>
            </td>
        </tr>
        <tr>
            <td class="border_users_l border_users_b us_gray" height="23px">
                State:
            </td>
            <td class="border_users_r border_users_l border_users_b">
                <div align="left">
                    &nbsp;{$company.state}
                </div>
            </td>
        </tr>
        <tr>
            <td class="border_users_l border_users_b us_gray" height="23px">
                (Zip/Postal code):
            </td>
            <td class="border_users_r border_users_l border_users_b">
                <div align="left">
                    &nbsp;{$company.zip}
                </div>
            </td>
        </tr>
        <tr>
            <td class="border_users_l border_users_b us_gray" height="23px">
                Phone:
            </td>
            <td class="border_users_r border_users_l border_users_b">
                <div align="left">
                    &nbsp;{$company.phone}
                </div>
            </td>
        </tr>
        <tr>
            <td class="border_users_l border_users_b us_gray" height="23px">
                Fax:
            </td>
            <td class="border_users_r border_users_l border_users_b">
                <div align="left">
                    &nbsp;{$company.fax}
                </div>
            </td>
        </tr>
        <tr>
            <td class="border_users_l border_users_b us_gray" height="23px">
                Email:
            </td>
            <td class="border_users_r border_users_l border_users_b">
                <div align="left">
                    &nbsp;{$company.email}
                </div>
            </td>
        </tr>
        <tr>
            <td class="border_users_l border_users_b us_gray" height="23px">
                Contact:
            </td>
            <td class="border_users_r border_users_l border_users_b">
                <div align="left">
                    &nbsp;{$company.contact} 
                </div>
            </td>
        </tr>
        <tr>
            <td class="border_users_l border_users_b us_gray" height="23px">
                Title:
            </td>
            <td class="border_users_r border_users_l border_users_b">
                <div align="left">
                    &nbsp;{$company.title}
                </div>
            </td>
        </tr>
        <tr>
            <td class="border_users_l border_users_b us_gray" height="23px">
                VOC UnitType:
            </td>
            <td class="border_users_r border_users_l border_users_b">
                <div align="left">
                    &nbsp;{$voc_unittype_desc}
                </div>
            </td>
        </tr>
        <tr>
            <td class="users_u_bottom" width="18%">
            </td>
            <td height="23px" class="users_u_bottom_r">
            </td>
        </tr>
    </table>
</div>


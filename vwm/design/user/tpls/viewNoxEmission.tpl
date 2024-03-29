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
                <span>View NOx Emission Details</span>
            </td>
            <td class="users_u_top_r_yellowgreen" width="300">
            </td>
        </tr>
        <tr>
            <td class="border_users_l border_users_b" height="20">
                ID:
            </td>
            <td class="border_users_l border_users_b border_users_r">
                <div align="left">
                    &nbsp;{$noxEmission->nox_id|escape}
                </div>
            </td>
        </tr>
		<tr>
            <td class="border_users_l border_users_b" height="20">
                Description:
            </td>
            <td class="border_users_l border_users_b border_users_r">
                <div align="left">
                    &nbsp;{$noxEmission->description|escape} 
                </div>
            </td>
        </tr>	
		<tr>
            <td class="border_users_l border_users_b" height="20">
                Burner:
            </td>
            <td class="border_users_l border_users_b border_users_r">
                <div align="left">
                    &nbsp;{$burnerDetails.model|escape} &gt; {$burnerDetails.serial|escape}
                </div>
            </td>
        </tr>
        <tr>
            <td class="border_users_l border_users_b" height="20">
               Start Time:
            </td>
            <td class="border_users_l border_users_b border_users_r">
                <div align="left">
                    &nbsp;{$noxEmission->start_time|escape}
                </div>
            </td>
        </tr>
   
        <tr>
            <td class="border_users_l border_users_b" height="20">
                End Time:
            </td>
            <td class="border_users_l border_users_b border_users_r">
                <div align="left">
                    &nbsp;{$noxEmission->end_time|escape}
                </div>
            </td>
        </tr>
        <tr>
            <td class="border_users_l border_users_b" height="20">
                Gas Unit Used:
            </td>
            <td class="border_users_l border_users_b border_users_r">
                <div align="left">
                    &nbsp;{$noxEmission->gas_unit_used|escape}
                </div>
            </td>
        </tr>
		<tr>
            <td class="border_users_l border_users_b" height="20">
                Notes:
            </td>
            <td class="border_users_l border_users_b border_users_r">
                <div align="left">
                    &nbsp;{$noxEmission->note|escape}
                </div>
            </td>
        </tr>
		<tr>
            <td class="border_users_l border_users_b" height="20">
                NOx Amount:
            </td>
            <td class="border_users_l border_users_b border_users_r">
                <div align="left">
                    &nbsp;{$noxEmission->nox|escape}
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
{if $mixList|@is_array and $mixList|@count > 0}
<div style="padding:7px;">
 <div style="padding:7px;">
    <table class="users" align="center" cellpadding="0" cellspacing="0">
        <tr class="users_top_yellowgreen users_u_top_size">
            <td class="users_u_top_yellowgreen" width="10%" height="30">
                <div style='width:100%;  color:white;'>
                    Mix ID
                </div>
            </td>
            <td class="border_users_b" width="60%">
                <div style='width:100%;  color:white;'>
                    Description
                </div>
            </td>
            <td class="border_users_b" width="10%">
                <div style='width:100%;  color:white;'>
                    VOC
                </div>
            </td>
            <td class="border_users_b" width="15%">
                    <div style='width:100%;  color:white;'>
                        Creation Date
                    </div>
            </td>
			<td class="users_u_top_r_yellowgreen" width="15%">
                    <div style='width:100%;  color:white;'>
                        Price
                    </div>
            </td>
        </tr>
    {*BEGIN LIST*}
    {foreach from=$mixList item=mix}
    <tr class="hov_company"	height="10px">
        <td class="border_users_l border_users_b border_users_r" >
            <div style="width:100%;">
                {$mix->mix_id} &nbsp;
            </div>
        </td>
        <td class="border_users_b border_users_r">
            <div style="width:100%;" align="left">
                {$mix->description|escape} &nbsp;
            </div>
        </td>
        <td class="border_users_b border_users_r">
            <div style="width:100%;">
                {$mix->voc} &nbsp;
            </div>
        </td>
        <td class="border_users_b border_users_r">
            <div style="width:100%;" >
                {$mix->creation_time} &nbsp;
            </div>
        </td>
		<td class="border_users_b border_users_r">
            <div style="width:100%;" >
                $ {$mix->price} &nbsp;
            </div>
        </td>
    </tr>
    {/foreach}
    <tr>
        <td colspan="5" class="border_users_l border_users_r">
            &nbsp;
        </td>
    </tr>
    {*END LIST*}
    <tr>
        <td class="users_u_bottom">
        </td>
        <td colspan="3" height="15" class="border_users">
        </td>
        <td class="users_u_bottom_r">
        </td>
    </tr>
</table>
</div>
{/if} 

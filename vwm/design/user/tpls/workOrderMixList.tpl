{if $mixList|@is_array and $mixList|@count > 0}
<div style="padding:7px;">
 <div style="padding:7px;">
    <table class="users" align="center" cellpadding="0" cellspacing="0">
        <tr class="users_top_yellowgreen users_u_top_size">
            <td class="users_u_top_yellowgreen" width="10%" height="30">
                <div style='width:10%;  color:white;'>
                    Mix ID
                </div>
            </td>
            <td class="border_users_b" width="40%">
                <div style='width:40%;  color:white;'>
                    Description
                </div>
            </td>
            <td class="border_users_b" width="10%">
                <div style='width:10%;  color:white;'>
                    VOC
                </div>
            </td>
            <td class="border_users_b" width="20%">
                    <div style='width:20%;  color:white;'>
                        Creation Date
                    </div>
            </td>
			<td class="users_u_top_r_yellowgreen" width="20%">
                    <div style='width:20%;  color:white;'>
                        Price
                    </div>
            </td>
        </tr>
    {*BEGIN LIST*}
    {foreach from=$mixList item=mix}
    <tr class="hov_company"	height="10px">
        <td class="border_users_l border_users_b border_users_r" >
            <div>
                {$mix->mix_id|escape} &nbsp;
            </div>
        </td>
        <td class="border_users_b border_users_r">
			<div>
                {$mix->description|escape} &nbsp;
            </div>
        </td>
        <td class="border_users_b border_users_r">
            <div>
                {$mix->voc|escape} &nbsp;
            </div>
        </td>
        <td class="border_users_b border_users_r">
            <div>
                {$mix->creation_time|escape} &nbsp;
            </div>
        </td>
		<td class="border_users_b border_users_r">
            <div>
                $ {$mix->price|escape} &nbsp;
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

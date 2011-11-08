<div class="padd7" align="center">
    {if $color eq "green"}
		{include file="tpls:tpls/notify/greenNotify.tpl" text=$message}
    {/if}
    {if $color eq "orange"}
		{include file="tpls:tpls/notify/orangeNotify.tpl" text=$message}
    {/if}
    {if $color eq "blue"}
		{include file="tpls:tpls/notify/blueNotify.tpl" text=$message}
    {/if}
    <table class="users" width="100%" cellspacing="0" cellpadding="0" bgcolor="#EFEFEF">
        <tr class="users_u_top_size users_top_blue">
            <td class="users_u_top_blue" width="60">

            </td>
			<td class="users_u_top_r_blue">

            </td>
        </tr>
		<tr>
            <td colspan="2" class="border_users_l border_users_r" align="center">
                No Forms
            </td>
        </tr>
        <tr>
            <td class="users_u_bottom ">
            </td>
            <td colspan="2" bgcolor="" height="30" class="users_u_bottom_r">
            </td>
        </tr>
    </table>
</div>
</form>

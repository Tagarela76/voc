
{if $color eq "green"}
    {include file="tpls:tpls/notify/greenNotify.tpl" text=$message}
{/if}
{if $color eq "orange"}
    {include file="tpls:tpls/notify/orangeNotify.tpl" text=$message}
{/if}
{if $color eq "blue"}
    {include file="tpls:tpls/notify/blueNotify.tpl" text=$message}
{/if}

{*reminder filter list*}
{include file="tpls:tpls/reminderUserFilterList.tpl"}

<div class="padd7">
    <table class="users" align="center" cellpadding="0" cellspacing="0" >
        <tr class="users_top_green users_u_top_size">
            <td class="users_u_top_green" width="27%" colspan="3">
                <span>Reminder Users</span>
            </td>
            <td class="users_u_top_r_green" width="5%">
            </td>
        </tr>
        <tr class="users_top_lightgray">
            <td width="5%">
                User Id
            </td>	
            <td class="border_users_b border_users_r" width="25%">
                Name
            </td>
            <td class="border_users_b border_users_r" width = "35%">
                Email
            </td>
            <td class="border_users_b border_users_r" width = "35%">
                Telephone Number
            </td>
        </tr>
        {foreach from=$usersList item=user}
            <tr>
                <td class="border_users_b border_users_r border_users_l">
                    <a href="?action=viewItemDetails&category=reminderUsers&reminderUserId={$user.reminderUserId|escape}&facilityId={$facilityId|escape}">
                        {$user.user_id|escape}
                    </a>
                </td>
                <td class="border_users_b border_users_r">
                    <a href="?action=viewItemDetails&category=reminderUsers&reminderUserId={$user.reminderUserId|escape}&facilityId={$facilityId|escape}">
                        {$user.username|escape}
                    </a>
                </td>
                <td class="border_users_b border_users_r">
                    <a href="?action=viewItemDetails&category=reminderUsers&reminderUserId={$user.reminderUserId|escape}&facilityId={$facilityId|escape}">
                        {$user.email|escape}
                    </a>
                </td>
                <td class="border_users_b border_users_r">
                    <a href="?action=viewItemDetails&category=reminderUsers&reminderUserId={$user.reminderUserId|escape}&facilityId={$facilityId|escape}">
                        {$user.mobile|escape}
                    </a>
                </td>
            </tr>
        {/foreach}
    </table>
    <div align="center"><div class="users_bottom"><div class="users_u_bottom"><div class="users_u_bottom_r"></div></div></div></div>
    <!--<input type='text' name='tab' value='{$tab|escape}'>-->
    <input type='hidden' id='facilityId' value="{$facilityId}">
</div>
{*PAGINATION*}
{include file="tpls:tpls/pagination.tpl"}
{*/PAGINATION*}
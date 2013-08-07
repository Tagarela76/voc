{*reminder filter list*}
{include file="tpls:tpls/reminderUserFilterList.tpl"}

<div class="padd7">
    <table class="users" align="center" cellpadding="0" cellspacing="0" >
        <tr class="users_top_green users_u_top_size">
            <td class="users_u_top_green" width="27%" colspan="1">
                <span>Reminder Emails</span>
            </td>
            <td class="users_u_top_r_green" width="5%">
            </td>
        </tr>
        <tr class="users_top_lightgray">
            <td class="border_users_b border_users_r" width = "5%">
                Id
            </td>
            <td class="border_users_b border_users_r" width = "95%">
                Email
            </td>
        </tr>
        {if !empty($reminderUserList)}
            {foreach from=$reminderUserList item='reminderUser'}
                <tr>
                    <td class="border_users_b border_users_r border_users_l">
                        <a href="?action=viewItemDetails&category=reminderUsers&reminderUserId={$reminderUser->getId()|escape}&facilityId={$facilityId|escape}">
                            {$reminderUser->getId()|escape}
                        </a>
                    </td>
                    <td class="border_users_b border_users_r">
                        <a href="?action=viewItemDetails&category=reminderUsers&reminderUserId={$reminderUser->getId()|escape}&facilityId={$facilityId|escape}">
                            {$reminderUser->getEmail()|escape}
                        </a>
                    </td>
                </tr>
            {/foreach}
        {else}
            <tr>
                <td colspan="2"class="border_users_l border_users_r" align="center">
                    no reminder user emails
                </td>
            </tr>
        {/if}
    </table>
    <div align="center"><div class="users_bottom"><div class="users_u_bottom"><div class="users_u_bottom_r"></div></div></div></div>
    <!--<input type='text' name='tab' value='{$tab|escape}'>-->
    <input type='hidden' id='facilityId' value="{$facilityId}">
</div>
{*PAGINATION*}
{include file="tpls:tpls/pagination.tpl"}
{*/PAGINATION*}
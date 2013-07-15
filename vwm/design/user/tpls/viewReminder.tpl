
{if $color eq "green"}
    {include file="tpls:tpls/notify/greenNotify.tpl" text=$message}
{/if}
{if $color eq "orange"}
    {include file="tpls:tpls/notify/orangeNotify.tpl" text=$message}
{/if}
{if $color eq "blue"}
    {include file="tpls:tpls/notify/blueNotify.tpl" text=$message}
{/if}
<div style="padding:7px;">
    <table class="users" align="center" cellpadding="0" cellspacing="0">
        <tr class="users_top_yellowgreen users_u_top_size">
            <td class="users_u_top_yellowgreen" width="37%" height="30">
                <span>View reminder</span>
            </td>
            <td class="users_u_top_r_yellowgreen" width="300">
            </td>
        </tr>
        <tr>
            <td class="border_users_l border_users_b" height="20">
                Reminder name:
            </td>
            <td class="border_users_l border_users_b border_users_r">
                <div align="left">
                    &nbsp; {$reminder->getName()|escape}
                </div>
            </td>
        </tr>
        <tr>
            <td class="border_users_l border_users_b" height="20">
                Reminder Date:
            </td>
            <td class="border_users_l border_users_b border_users_r">
                <div align="left">
                    &nbsp; {$reminder->getDeliveryDate()|escape}
                </div>
            </td>
        </tr>
        <tr>
            <td class="border_users_l border_users_b" height="20">
                Reminder Type:
            </td>
            <td class="border_users_l border_users_b border_users_r">
                <div align="left">
                    &nbsp; {$reminder->getType()|escape}
                </div>
            </td>
        </tr>
        <tr>
            <td class="border_users_l border_users_b" height="20">
                Priority Level:
            </td>
            <td class="border_users_l border_users_b border_users_r">
                <div align="left">
                    &nbsp; {$reminder->getPriority()|escape}%
                </div>
            </td>
        </tr>
        <tr>
            <td class="border_users_l border_users_b" height="20">
                Reminder Timing:
            </td>
            <td class="border_users_l border_users_b border_users_r">
                <div align="left">
                    {assign var=i value=$reminder->getPeriodicity()}
                    &nbsp; {$reminderTimingList[$i].description|escape}
                </div>
            </td>
        </tr>
        <tr>
            <td class="border_users_l border_users_b" height="20">
                Users:
            </td>
            <td class="border_users_l border_users_b border_users_r">
                <div align="left" id="usersList">
                    &nbsp; {$usersNames|escape}
                </div>
            </td>
        </tr>
        <tr>
            <td class="border_users_l border_users_b" height="20">
                ACTIVE:
            </td>
            <td class="border_users_l border_users_b border_users_r">
                <div align="left" id="usersList">
                    {if $reminder->getActive()}
                        &nbsp; on
                    {else}
                       &nbsp; off
                    {/if}
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


<div class="padd7">

    <table class="users" align="center" cellpadding="0" cellspacing="0" >
        <tr class="users_top_yellowgreen users_u_top_size">
            <td class="users_u_top_yellowgreen" width="27%" colspan="3">
                <span>Reminder Users</span>
            </td>
            <td class="users_u_top_r_yellowgreen" width="5%">
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
                    {$user.user_id|escape}
                </td>
                <td class="border_users_b border_users_r">
                    {$user.username|escape}
                </td>
                <td class="border_users_b border_users_r">
                    {$user.email|escape}
                </td>
                <td class="border_users_b border_users_r">
                    {$user.mobile|escape}
                </td>
            </tr>
        {/foreach}
    </table>
    <div align="center"><div class="users_bottom"><div class="users_u_bottom"><div class="users_u_bottom_r"></div></div></div></div>
    <input type='hidden' name='tab' value='{$tab|escape}'>
    {*PAGINATION*}
    {include file="tpls:tpls/pagination.tpl"}
    {*/PAGINATION*}

</div>
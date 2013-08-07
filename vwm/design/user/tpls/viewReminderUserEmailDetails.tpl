<div>
    <input type='button' class="button" value="<<<Back" style="margin: 0 0 10px 25px;" onclick='window.history.back()'>
    <input type='button' class="button" value="Delete" style="margin: 0 0 0 5px;" onclick='location.href="?action=deleteItem&category=reminderUsers&reminderUserId={$reminderUser->getId()}&facilityId={$reminderUser->getFacilityId()}"'>
</div>

<div style="padding:7px;">
    <table class="users" align="center" cellpadding="0" cellspacing="0">
        <tbody><tr class="users_top_yellowgreen users_u_top_size">
                <td class="users_u_top_yellowgreen" width="37%" height="30">
                    <span>View User</span>
                </td>
                <td class="users_u_top_r_yellowgreen" width="300">
                </td>
            </tr>
            <tr>
                <td height="20" class="border_users_l border_users_b">
                    User Email
                </td>
                <td height="20" class="border_users_l border_users_b border_users_r">
                    {$reminderUser->getEmail()|escape}
                </td>
            </tr>
            <tr>
                <td height="20" class="users_u_bottom">
                </td>
                <td height="20" class="users_u_bottom_r">
                </td>
            </tr>
        </tbody></table>
    <div align="right">
    </div>    
</div>

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

    <input type='hidden' id='sort'>
    <table class="users" width="100%" cellspacing="0" cellpadding="0" bgcolor="#EFEFEF">
        <tr class="users_header_yellowgreen">
            <td width="7%">
                <div class="users_header_yellowgreen_l">
                    <div>
                        Reminder Id
                    </div>
                </div>
            </td>
            <td>
                <div class="users_header_yellowgreen">
                    Name
                </div>
            </td>
            <td>
                <div class="users_header_yellowgreen">
                    Reminder Type
                </div>
            </td>
            <td>
                <div class="users_header_yellowgreen">
                    Priority Level
                </div>
            </td>
            <td>
                <div class="users_header_yellowgreen">
                    Reminder Timing
                </div>
            </td>
            <td>
                <div class="users_header_yellowgreen_r">
                    <div>Date</div>
                </div>
            </td>
        </tr>

        {if $childCategoryItems}
            {foreach from=$childCategoryItems item=reminder}
                <tr class="hov_company" height="10px">
                    <td class="border_users_l border_users_b">
                        <a href="?action=viewDetails&category=reminder&id={$reminder->id|escape}&facilityID={$facilityId}">
                            {$reminder->id|escape}
                        </a>
                    </td>
                    <td class="border_users_b border_users_l">
                        <div style="width:100%;">
                            <a href="?action=viewDetails&category=reminder&id={$reminder->id|escape}&facilityID={$facilityId}">
                                {$reminder->getName()|escape}
                            </a>
                        </div>
                    </td>
                    <td class="border_users_b border_users_l">
                        <div style="width:100%;">
                            <a href="?action=viewDetails&category=reminder&id={$reminder->id|escape}&facilityID={$facilityId}">
                                {$reminder->getType()|escape}
                            </a>
                        </div>
                    </td>
                    <td class="border_users_b border_users_l">
                        <div style="width:100%;">
                            <a href="?action=viewDetails&category=reminder&id={$reminder->id|escape}&facilityID={$facilityId}">
                                {$reminder->getPriority()|escape}%
                            </a>
                        </div>
                    </td>
                    <td class="border_users_b border_users_l">
                        <div style="width:100%;">
                            <a href="?action=viewDetails&category=reminder&id={$reminder->id|escape}&facilityID={$facilityId}">
                                {$reminder->getPeriodicity()|escape}
                            </a>
                        </div>
                    </td>
                    <td style="width:250px;" class="border_users_b border_users_l border_users_r">
                        <div style="width:100%;">
                            <a href="?action=viewDetails&category=reminder&id={$reminder->id|escape}&facilityID={$facilityId}">
                                {$reminder->getDateInOutputFormat()|escape}
                            </a>
                        </div>
                    </td>
                </tr>
            {/foreach}
        {else}

            {*BEGIN	EMPTY LIST*}
            <tr>
                <td colspan="6"class="border_users_l border_users_r" align="center">
                    No reminders in the list
                </td>
            </tr>
            {*END	EMPTY LIST*}

        {/if}
        <tr>
            <td class="users_u_bottom ">
            </td>
            <td colspan="5" bgcolor="" height="30" class="users_u_bottom_r">
            </td>
        </tr>
    </table>
    {*PAGINATION*}
    {include file="tpls:tpls/pagination.tpl"}
    {*/PAGINATION*}
</div>
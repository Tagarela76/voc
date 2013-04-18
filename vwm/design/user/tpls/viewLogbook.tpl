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
    
    <table class="users" align="center" cellpadding="0" cellspacing="0" >
        <tr class="users_u_top_size users_top_brown">
            <td class="users_u_top_brown" width="27%" colspan="3">
                <span>List of last records at logbook</span>
            </td>
            <td class="users_u_top_r_brown" width="5%">
            </td>
        </tr>
        <tr class="users_top_lightgray">
            <td class="border_users_b border_users_r border_users_l" width="10%">
                All/None
            </td>
            <td class="border_users_b border_users_r" width="10%">
                Date
            </td>
            <td class="border_users_b border_users_r" width = "10%">
                Time
            </td>
            <td class="border_users_b border_users_r" width = "80%">
                Type Description
            </td>
        </tr>
        {foreach from=$logbookList item=logbook}
             <tr>
            <td class="border_users_b border_users_r border_users_l">
                <input type='checkbox'>
            </td>
            <td class="border_users_b border_users_r">
                {$logbook.creationDate}
            </td>
            <td class="border_users_b border_users_r">
                {$logbook.creationTime}
            </td>
            <td class="border_users_b border_users_r">
                <a href="?action=viewLogbookDetails&category=logbook&facilityID={$facilityId}&id={$logbook.logbookId}">
                    {$logbook.inspectionType}
                </a>
            </td>
        </tr>
        {/foreach}
    </table>

</div>
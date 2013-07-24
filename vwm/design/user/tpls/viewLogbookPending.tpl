{*PAGINATION*}
{include file="tpls:tpls/pagination.tpl"}
{*/PAGINATION*}
<div class="padd7">
    <table class="users" align="center" cellpadding="0" cellspacing="0" >
        <tr class="users_u_top_size users_top_brown">
            <td class="users_u_top_brown" width="27%" colspan="6">
                <span>List of remind logbook records</span>
            </td>
            <td class="users_u_top_r_brown" width="5%">
            </td>
        </tr>
        <tr class="users_top_lightgray">
            <td class="border_users_b border_users_r" width="10%">
                Parent Logbook Id
            </td>
            <td class="border_users_b border_users_r" width="15%">
                Parent Logbook Creating date
            </td>
            <td class="border_users_b border_users_r" width = "10%">
                Inspected By
            </td>
            <td class="border_users_b border_users_r" width = "20%">
                Inspection Type
            </td>
            <td class="border_users_b border_users_r" width = "30%">
                Condition
            </td>
            <td class="border_users_b border_users_r" width = "15%">
                Notes
            </td>
            <td class="border_users_b border_users_r" width = "10%">
                Periodicity
            </td>
        </tr>
        {if !empty($logbookPendingRecordList)}
            {foreach from=$logbookPendingRecordList item=logbook}
                <tr>
                    <td class="border_users_b border_users_r border_users_l">
                        <a href="?action=addItem&category=logbook&logbookPendingRecordId={$logbook.logbookId}&facilityID={$facilityId}&tab={$tab}">
                            {$logbook.parentId|escape}
                        </a>
                    </td>
                    <td class="border_users_b border_users_r border_users_l">
                        <a href="?action=addItem&category=logbook&logbookPendingRecordId={$logbook.logbookId}&facilityID={$facilityId}&tab={$tab}">
                            {$logbook.creationDate|escape}
                        </a>
                    </td>
                    <td class="border_users_b border_users_r">
                        <a href="?action=addItem&category=logbook&logbookPendingRecordId={$logbook.logbookId}&facilityID={$facilityId}&tab={$tab}">
                            {$logbook.inspectionPersonName|escape}
                        </a>
                    </td>
                    <td class="border_users_b border_users_r">
                        <a href="?action=addItem&category=logbook&logbookPendingRecordId={$logbook.logbookId}&facilityID={$facilityId}&tab={$tab}">
                            {$logbook.inspectionType->typeName|escape}
                        </a>
                    </td>
                    <td class="border_users_b border_users_r">
                        <a href="?action=addItem&category=logbook&logbookPendingRecordId={$logbook.logbookId}&facilityID={$facilityId}&tab={$tab}">
                            {$logbook.condition|escape}
                        </a>
                    </td>
                    <td class="border_users_b border_users_r">
                        <a href="?action=addItem&category=logbook&logbookPendingRecordId={$logbook.logbookId}&facilityID={$facilityId}&tab={$tab}">
                            {$logbook.notes|escape}
                        </a>
                    </td>
                    <td class="border_users_b border_users_r">
                        <a href="?action=addItem&category=logbook&logbookPendingRecordId={$logbook.logbookId}&facilityID={$facilityId}&tab={$tab}">
                            {$logbook.periodicity|escape}
                        </a>
                    </td>
                </tr>
            {/foreach}
        {else}
            <tr>
                <td colspan="7"class="border_users_l border_users_r" align="center">
                    No pending logbook records in facility for today
                </td>
            </tr>
        {/if}
    </table>
    <div align="center"><div class="users_bottom"><div class="users_u_bottom"><div class="users_u_bottom_r"></div></div></div></div>
    <input type='hidden' name='tab' value='{$tab|escape}'>
</div>
{*PAGINATION*}
{include file="tpls:tpls/pagination.tpl"}
{*/PAGINATION*}
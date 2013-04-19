<div style="width: 100%; padding: 10px 13px;">
    <input type='button' class="button" value="<< Back" onclick="history.back();">
    <input type='button' class="button" value="Edit" onclick="location.href='?action=addItem&category=logbook&logbookId={$logbook->getId()}&facilityID={$logbook->getFacilityID()}'">
</div>
<div>
    <table class="users" align="center" cellpadding="0" cellspacing="0">
        <tr  class="users_u_top_size users_top_brown">
            <td class="users_u_top_brown" width='15%'>
                Logbook record #{$logbook->getId()}
            </td>
            <td class="users_u_top_r_brown">
            </td>
        </tr>
        <tr>
            <td class="border_users_b border_users_r border_users_l">
                Logbook ID
            </td>
            <td class="border_users_b border_users_r">
                {$logbook->getId()}
            </td>
        </tr>
        <tr>
            <td class="border_users_b border_users_r border_users_l">
                Facility Id
            </td>
            <td class="border_users_b border_users_r">
                {$logbook->getFacilityID()}
            </td>
        </tr>
        <tr>
            <td class="border_users_b border_users_r border_users_l">
                Inspection Person
            </td>
            <td class="border_users_b border_users_r">
                {$inspectionPerson->getName()}
            </td>
        </tr>
        <tr>
            <td class="border_users_b border_users_r border_users_l">
                Inspection Type
            </td>
            <td class="border_users_b border_users_r">
                {$logbook->getInspectionType()}
            </td>
        </tr>
        <tr>
            <td class="border_users_b border_users_r border_users_l">
                Inspection Sub Type
            </td>
            <td class="border_users_b border_users_r">
                {$logbook->getInspectionSubType()}
            </td>
        </tr>
        {if $logbook->getHasSubTypeNotes()}
            <tr>
                <td class="border_users_b border_users_r border_users_l">
                    Sub type notes
                </td>
                <td class="border_users_b border_users_r">
                    {$logbook->getSubTypeNotes()}
                </td>
            </tr>
        {/if}
        <tr>
            <td class="border_users_b border_users_r border_users_l">
                Description
            </td>
            <td class="border_users_b border_users_r">
                {$logbook->getDescription()}
            </td>
        </tr>
        {if $logbook->getHasDescriptionNotes()}
            <tr>
                <td class="border_users_b border_users_r border_users_l">
                    Description notes
                </td>
                <td class="border_users_b border_users_r">
                    {$logbook->getDescriptionNotes()}
                </td>
            </tr>
        {/if}
        <tr>
            <td class="border_users_b border_users_r border_users_l">
                Date/Time
            </td>
            <td class="border_users_b border_users_r">
                {$creationTime}
            </td>
        </tr>
        {*addition Fialds*}
        {if $logbook->getHasPermit()}
            <tr>
                <td class="border_users_b border_users_r border_users_l">
                    Permit
                </td>
                <td class="border_users_b border_users_r">
                    {if $logbook->getPermit()}
                        yes
                    {else}
                        no
                    {/if}
                </td>
            </tr>
        {/if}
        {if $logbook->getHasQty()}
            <tr>
                <td class="border_users_b border_users_r border_users_l">
                    Qty
                </td>
                <td class="border_users_b border_users_r">
                    {$logbook->getQty()}
                </td>
            </tr>
        {/if}
    </table>
        <div align="center"><div class="users_bottom"><div class="users_u_bottom"><div class="users_u_bottom_r"></div></div></div></div>
</div>

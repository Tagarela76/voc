<div style="width: 100%; padding: 10px 13px;">
    <input type='button' class="button" value="<< Back" onclick="location.href = '?action=browseCategory&category=facility&id={$facilityId|escape}&bookmark=logbook&tab=logbookCustomDescription'">
    <input type='button' class="button" value="Edit" onclick="location.href = '?action=addItem&category=logbook&facilityID={$facilityId|escape}&tab=logbookCustomDescription&logbookCustomDescriptionId={$logbookCustomDescription->getId()|escape}'">
</div>

<div>
    <table class="users" align="center" cellpadding="0" cellspacing="0">
        <tr  class="users_u_top_size users_top_brown">
            <td class="users_u_top_brown" width='15%'>
                Logbook Custom Description #{$logbookCustomDescription->getId()|escape}
            </td>
            <td class="users_u_top_r_brown">
            </td>
        </tr>
        <tr>
            <td class="border_users_b border_users_r border_users_l">
                Logbook Custom Description ID
            </td>
            <td class="border_users_b border_users_r">
                {$logbookCustomDescription->getId()|escape}
            </td>
        </tr>
        <tr>
            <td class="border_users_b border_users_r border_users_l">
                Facility Id
            </td>
            <td class="border_users_b border_users_r">
                {$facilityId|escape}
            </td>
        </tr>
        <tr>
            <td class="border_users_b border_users_r border_users_l">
                Description
            </td>
            <td class="border_users_b border_users_r">
                {$logbookCustomDescription->getDescription()|escape}
            </td>
        </tr>
        <tr>
            <td class="border_users_b border_users_r border_users_l">
                Notes
            </td>
            <td class="border_users_b border_users_r">
                {if $logbookCustomDescription->getNotes()|escape}
                    yes
                {else}
                    no
                {/if}
            </td>
        </tr>
        <tr>
            <td class="border_users_b border_users_r border_users_l">
                Inspection Type Id
            </td>
            <td class="border_users_b border_users_r">
                {$logbookCustomDescription->getInspectionTypeId()|escape}
            </td>
        </tr>
    </table>
    <div align="center"><div class="users_bottom"><div class="users_u_bottom"><div class="users_u_bottom_r"></div></div></div></div>
</div>
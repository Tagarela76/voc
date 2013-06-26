<div style="width: 100%; padding: 10px 13px;">
    <input type='button' class="button" value="<< Back" onclick="location.href = '?action=browseCategory&category=facility&id=125&bookmark=logbook&tab=logbookEquipment'">
    <input type='button' class="button" value="Edit" onclick="location.href = '?action=addItem&category=logbook&facilityID={$logbookEquipment->getFacilityId()|escape}&tab=logbookEquipment&logbookEquipmentId={$logbookEquipment->getId()|escape}'">
</div>
<div>
    <table class="users" align="center" cellpadding="0" cellspacing="0">
        <tr  class="users_u_top_size users_top_brown">
            <td class="users_u_top_brown" width='15%'>
                Logbook Equipment #{$logbookEquipment->getId()|escape}
            </td>
            <td class="users_u_top_r_brown">
            </td>
        </tr>
        <tr>
            <td class="border_users_b border_users_r border_users_l">
                Logbook Equipment ID
            </td>
            <td class="border_users_b border_users_r">
                {$logbookEquipment->getId()|escape}
            </td>
        </tr>
        <tr>
            <td class="border_users_b border_users_r border_users_l">
                Facility Id
            </td>
            <td class="border_users_b border_users_r">
                {$logbookEquipment->getFacilityId()|escape}
            </td>
        </tr>
        <tr>
            <td class="border_users_b border_users_r border_users_l">
                Name
            </td>
            <td class="border_users_b border_users_r">
                {$logbookEquipment->getName()|escape}
            </td>
        </tr>
    </table>
    <div align="center"><div class="users_bottom"><div class="users_u_bottom"><div class="users_u_bottom_r"></div></div></div></div>
</div>
<div style="width: 100%; padding: 10px 13px;">
    <input type='button' class="button" value="<< Back" onclick="location.href = '?action=browseCategory&category=facility&id={$person->getFacilityId()|escape}&bookmark=logbook&tab=inspectionPerson'">
    <input type='button' class="button" value="Edit" onclick="location.href = '?action=editItem&category=logbook&facilityID={$person->getFacilityId()|escape}&tab=inspectionPerson&inspectionPersonId={$person->getId()|escape}'">
</div>
<div>
    <table class="users" align="center" cellpadding="0" cellspacing="0">
        <tr  class="users_u_top_size users_top_brown">
            <td class="users_u_top_brown" width='15%'>
                Logbook Inspection Person #{$person->getId()|escape}
            </td>
            <td class="users_u_top_r_brown">
            </td>
        </tr>
        <tr>
            <td class="border_users_b border_users_r border_users_l">
                Logbook Inspection Person ID
            </td>
            <td class="border_users_b border_users_r">
                {$person->getId()|escape}
            </td>
        </tr>
        <tr>
            <td class="border_users_b border_users_r border_users_l">
                Facility Id
            </td>
            <td class="border_users_b border_users_r">
                {$person->getFacilityId()|escape}
            </td>
        </tr>
        <tr>
            <td class="border_users_b border_users_r border_users_l">
                Name
            </td>
            <td class="border_users_b border_users_r">
                {$person->getName()|escape}
            </td>
        </tr>
    </table>
    <div align="center"><div class="users_bottom"><div class="users_u_bottom"><div class="users_u_bottom_r"></div></div></div></div>
</div>
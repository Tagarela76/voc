<div style="width: 100%; padding: 10px 13px;">
    <input type='button' class="button" value="<< Back" onclick="location.href = '?action=browseCategory&category=logbook&bookmark=logbookInspectionType'">
    <input type='button' class="button" value="Edit" onclick="location.href = '?action=addItem&category=logbook&typeId={$logbookInspectionType.id|escape}&bookmark=logbookInspectionType'">
</div>
<div>
    <table class="users" align="center" cellpadding="0" cellspacing="0">
        <tr  class="users_u_top_size users_top_brown">
            <td class="users_u_top_brown" width='15%'>
                Logbook InspectionType #{$logbookInspectionType.id|escape}
            </td>
            <td class="users_u_top_r_brown">
            </td>
        </tr>
        <tr>
            <td class="border_users_b border_users_r border_users_l">
                Logbook InspectionType ID
            </td>
            <td class="border_users_b border_users_r">
                {$logbookInspectionType.id|escape}
            </td>
        </tr>
        <tr>
            <td class="border_users_b border_users_r border_users_l">
                Template Ids
            </td>
            <td class="border_users_b border_users_r">
                {$logbookInspectionType.templateIds|escape}
            </td>
        </tr>
        <tr>
            <td class="border_users_b border_users_r border_users_l">
                Name
            </td>
            <td class="border_users_b border_users_r">
                {$logbookInspectionType.name|escape}
            </td>
        </tr>
        <tr>
            <td class="border_users_b border_users_r border_users_l">
                Permit
            </td>
            <td class="border_users_b border_users_r">
                {$logbookInspectionType.permit|escape}
            </td>
        </tr>
        <tr>
            <td class="border_users_b border_users_r border_users_l">
                <b>Subtype</b>
            </td>
            <td class="border_users_b border_users_r">
                {$logbookInspectionType.subtypes}
            </td>
        </tr>
         <tr>
            <td class="border_users_b border_users_r border_users_l">
                <b>Gauge Types</b>
            </td>
            <td class="border_users_b border_users_r">
                {$logbookInspectionType.gaugeTypes}
            </td>
        </tr>
        <tr>
            <td class="border_users_b border_users_r border_users_l">
                <b>Descriptions</b>
            </td>
            <td class="border_users_b border_users_r">
                {$logbookInspectionType.descriptions}
            </td>
        </tr>
    </table>
    <div align="center"><div class="users_bottom"><div class="users_u_bottom"><div class="users_u_bottom_r"></div></div></div></div>
</div>
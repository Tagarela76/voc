<form action="?action=saveLogbookCustomDescription&category=logbook" method="post">
    <div class="padd7">
        <table class="users" align="center" cellpadding="0" cellspacing="0">
            <tr class="users_u_top_size users_top_brown">
                <td class="users_u_top_brown">
                    <span >Add Logbook Custom Description</span>
                </td>
                <td class="users_u_top_r_brown">
                    &nbsp;
                </td>
            </tr>
            <tr class="border_users_b border_users_r" height='30'>
                <td class="border_users_l" width = '30%'>
                    Logbook Custom Description
                </td>
                <td class="border_users_l" width = '70%'>
                    <input type='text' name = 'logbookCustomDescription' value='{$logbookCustomDescription->getDescription()|escape}'>
                    {foreach from=$violationList item="violation"}
                        {if $violation->getPropertyPath() eq 'description'}
                            {*ERROR*}
                            <div class="error_img" style="float: left;"><span class="error_text">{$violation->getMessage()|escape}</span></div>
                            {*/ERROR*}
                        {/if}
                    {/foreach}
                </td>
            </tr>
            <tr class="border_users_b border_users_r" height='30'>
                <td class="border_users_l" width = '30%'>
                    Is Description Has Notes
                </td>
                <td class="border_users_l" width = '70%'>
                    <input type='checkbox' name='notes' {if $logbookCustomDescription->getNotes()}checked='true'{/if}>
                </td>
            </tr>
            <tr class="border_users_b border_users_r" height='30'>
                <td class="border_users_l" width = '30%'>
                    Inspection Type
                </td>
                <td class="border_users_l" width = '70%'>
                    <select name='inspectionType'>
                        {foreach from=$inspectionTypesList item='inspectionTypes'}
                            {assign var="inspectionTypeSettings" value=$inspectionTypes->getInspectionType()}
                            <option value="{$inspectionTypes->getId()|escape}" {if $logbookCustomDescription->getInspectionTypeId() ==  $inspectionTypes->getId()}selected='selected'{/if}>{$inspectionTypeSettings->typeName}</option>
                        {/foreach}
                    </select>
                        {foreach from=$violationList item="violation"}
                            {if $violation->getPropertyPath() eq 'inspection_type_id'}
                                {*ERROR*}
                                <div class="error_img" style="float: left;"><span class="error_text">{$violation->getMessage()|escape}</span></div>
                                {*/ERROR*}
                            {/if}
                        {/foreach}
                </td>
            </tr>
        </table>
        <div align="center" ><div class="users_bottom"><div class="users_u_bottom"><div class="users_u_bottom_r"></div></div></div></div>
        
        <input type='hidden' name="facilityId" value="{$facilityId|escape}">
        
        <div align="right" style="padding: 12px 12px">
            <input type="submit" value="Save" class="button">
            <input type="button" value="Cancel" class="button" onclick="location.href = '?action=browseCategory&category=facility&id={$facilityId}&bookmark=logbook&tab=logbookCustomDescription'">
        </div>
    </div>
</form>
            <div name = 'errors'>
                {foreach from=$violationList item="violation"}
                    {if $violation->getPropertyPath() eq 'facility_id'}
                        {*ERROR*}
                        <div class="error_img" style="float: left;"><span class="error_text">Facility ID {$violation->getMessage()|escape}</span></div>
                            {*/ERROR*}
                        {/if}
                    {/foreach}             
            </div>
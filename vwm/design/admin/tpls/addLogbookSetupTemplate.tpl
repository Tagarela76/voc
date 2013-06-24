<form method='POST' action='admin.php?action=saveLogbookTemplate&category=logbook'>
<div class="padd7" align="center">
    <table class="users"  cellspacing="0" cellpadding="0">
        <table class="users"  cellspacing="0" cellpadding="0">
            <tr class="users_u_top_size users_top_brown" width='100%'>
                <td class="users_u_top_brown">
                    Add Logbook Template
                </td>
                <td  class="users_u_top_r_brown">

                </td>
            </tr>
            <tr>
                <td class="border_users_b border_users_r border_users_l" width ='10%' height = '30px'>
                    Template Name
                </td>
                <td class="border_users_b border_users_r border_users_l">
                    <input type='textbox' name='templateName' id='templateName' style="width: 240px;" value='{$logbookSetupTemplate->getName()|escape}'>
                    {foreach from=$violationList item="violation"}
                        {if $violation->getPropertyPath() eq 'name'}
                            {*ERROR*}					
                            <div class="error_img" style="float: left;"><span class="error_text">{$violation->getMessage()|escape}</span></div>
                            {*/ERROR*}						    
                        {/if}
                    {/foreach}	
                </td>
            </tr>
            <tr>
                <td class="border_users_b border_users_r border_users_l" width ='10%' height = '30px'>
                    Facility
                </td>
                <td class="border_users_b border_users_r border_users_l">
                    <div id = 'addFacilityIdsContainer'>
                        {$facilityIds|escape}
                    </div>
                    <a onclick='inspection.addLogbookTemplateFacilityDialog.openDialog();'>edit</a>
                </td>
            </tr> 
        </table>
        <div align="center"><div class="users_bottom"><div class="users_u_bottom"><div class="users_u_bottom_r"></div></div></div></div>
</div>
<div style='margin: 15px 10px 10px 20px'>
    <input type="button" class="button" value="<<<Back" onclick="window.location = '?action=browseCategory&category=logbook&bookmark=logbookSetupTemplate'">
    <input type="submit" class="button" value="Save" onclick="">
    <div id='typeSaveErrors' style='color: #ff0000; margin: 20px 1px 1px 1px;'>
    </div>
</div>

<input type='hidden' value="{$facilityIds|escape}" id='selectedFacilityIds' name="selectedFacilityIds" >
<input type='hidden' value="{$companyIds|escape}" id='selectedCompanyIds'>
<input type='hidden' value="{$logbookSetupTemplate->getId()|escape}" id='logbookSetupTemplateId' name='logbookSetupTemplateId'>
</form>
<div id="addLogbookTemplateFacilityContainer" title="Set Inspection Gauge Type" style="display:none;">Loading ...</div>

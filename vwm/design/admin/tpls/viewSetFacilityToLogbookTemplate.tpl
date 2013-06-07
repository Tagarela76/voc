<a onclick="logbookTemplateManager.checkAllFacilityTemplate(this)" style="color:black">All</a>/<a style="color:black" onclick="logbookTemplateManager.unCheckAllFacilityTemplate(this)">None</a>
{assign var='count' value=0}
<table class='popup_table' >
    {section name=i loop=$companyList}
        {assign var='facilityList' value=$companyList[i].facilityList}
        <tr>
            <td>
                <input type='checkbox' id='showFacilityList_{$smarty.section.i.index}' value="{$companyList[i].id}" 
                       onclick="logbookTemplateManager.showFacilityList({$smarty.section.i.index})"
                       {if in_array($companyList[i].id, $selectedCompanyIds)}checked='checked'{/if}>
            </td>
            <td>
                <b>{$companyList[i].name}</b>
            </td>
        </tr>
        <tr id='companyFacilityList_{$smarty.section.i.index}' {if !in_array($companyList[i].id, $selectedCompanyIds)}style='display:none'{/if}>
            <td>
            </td>
            <td>    
                <table id='companyListContainer_{$smarty.section.i.index}'>
                    {foreach from=$facilityList item=facility}
                        <tr>
                            <td >
                                <input type='checkbox' value='{$facility.id}' id='facility_{$facility.id}' {if in_array($facility.id, $selectedFacilityIds)}checked='checked'{/if}>
                            </td>
                            <td  >
                                {$facility.name}
                            </td> 
                        </tr>
                    {/foreach}
                </table>
            </td>
        </tr>
    {/section}
</table>

<input type='hidden' id='companyCount' value="{$companyCount}">

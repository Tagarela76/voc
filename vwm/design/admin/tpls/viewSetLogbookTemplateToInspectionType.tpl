<div style="float: left;margin-top: 3px">
    <div style='float: left;'>
        Company List<br>
        <select id='companyId' name='companyId' onchange="inspection.setInspectionTypeToTemplate.loadContent();">
            <option value='null'>All</option>
            {foreach from=$companyList item=company}
                <option value='{$company.id|escape}' {if $companyId == $company.id}selected='selected'{/if}>{$company.name|escape}</option>
            {/foreach}
        </select>
    </div>
    <div style='float: right; margin: 0px 0px 0px 10px;'>
        Facility List<br>
        <select id='facilityId' name='facilityId' onchange="inspection.setInspectionTypeToTemplate.loadContent();">
            <option value='null'>All</option>
            {foreach from=$facilityList item=facility}
                <option value='{$facility->getFacilityId()|escape}' {if $facilityId == $facility->getFacilityId()}selected='selected'{/if}>{$facility->getName()|escape}</option>
            {/foreach}
        </select>
    </div>
</div>

<table width="100%" cellpadding="0" cellspacing="0" class="popup_table" align="center" id='logbookTemplateList' style="margin-top: 40px">
    <tr class="table_popup_rule">
        <td>
            Logbook Templates
        </td>
        <td>
        </td>
    </tr>
    {foreach from = $logbookSetupTemplateList item = logbookSetupTemplate}
        <tr>
            <td>
                {$logbookSetupTemplate->getName()|escape}
            </td>
            <td>
                <input type = checkbox id='logbookItem_{$logbookSetupTemplate->getId()|escape}' value="{$logbookSetupTemplate->getId()|escape}"
                {if in_array($logbookSetupTemplate->getId(), $logbookTemplatesIds)}checked = 'true'{/if}>
        </td>
    </tr>
{/foreach}
</table>

{*PAGINATION*}
{include file="tpls:tpls/pagination.tpl"}
{*/PAGINATION*}
<div style="float: right; margin: 25px; width: 335px">
    <div style='float: left;'>
        Company List<br>
        <select id='companyId' name='companyId' onchange="manager.getFacilityList();">
            <option value='null'>All</option>
            {foreach from=$companyList item=company}
                <option value='{$company.id|escape}' {if $companyId == $company.id}selected='selected'{/if}>{$company.name|escape}</option>
            {/foreach}
        </select>
    </div>
    <div style='float: right;'>
        Facility List<br>
        <select id = 'facilityId' name='facilityId' onchange="logbookTemplateManager.changelogbookTemplateList();">
            <option value='null'>All</option>
            {foreach from=$facilityList item=facility}
                <option value='{$facility->getFacilityId()|escape}' {if $facilityId == $facility->getFacilityId()}selected='selected'{/if}>{$facility->getName()|escape}</option>
            {/foreach}
        </select>
    </div>
</div>


<div class="padd7" align="center" id = 'inspectionTypeList'>
    <table  class="users"  cellspacing="0" cellpadding="0">
        <tr class="users_u_top_size users_top_brown">
            <td class="users_u_top_brown" width="5%">
                <a onclick="CheckAll(this)" style='color:white'>All</a>/<a style='color:white' onclick="unCheckAll(this)" >None</a>
            </td>
            <td width="10%">
                ID
            </td>
            <td width="25%">
                Template Name
            </td>
            <!--<td  class="users_u_top_r_brown" width="10%">
                Edit
            </td>-->
        </tr>
        {section name=i loop=$logbookSetupTemplateList}
            <tr>
                <td class="border_users_b border_users_r border_users_l" width="5%">
                    <input type='checkbox' id='item_{$smarty.section.i.index}' name='item_{$smarty.section.i.index}' value="{$logbookSetupTemplateList[i]->getId()|escape}">
                </td >
                <td class="border_users_b border_users_r" width="10%">
                    <a href="admin.php?action=viewLogbookDetails&category=logbook&bookmark=logbookSetupTemplate&logbookTemplateId={$logbookSetupTemplateList[i]->getId()|escape}">
                        {$logbookSetupTemplateList[i]->getId()|escape}
                    </a>
                </td>
                <td class="border_users_b border_users_r" width="25%">
                    <a href="?action=viewLogbookDetails&category=logbook&bookmark=logbookSetupTemplate&logbookTemplateId={$logbookSetupTemplateList[i]->getId()|escape}">
                        {$logbookSetupTemplateList[i]->getName()|escape}
                    </a>
                </td>
                <!--<td  class="border_users_b border_users_r" class="users_u_top_r_brown" width="10%">
                    <a href = "admin.php?action=addItem&category=logbook&bookmark=logbookSetupTemplate&logbookTemplateId={$logbookSetupTemplateList[i]->getId()|escape}" >edit</a>
                </td>-->
            </tr>
        {/section}
    </table>
    <div align="center"><div class="users_bottom"><div class="users_u_bottom"><div class="users_u_bottom_r"></div></div></div></div>
</div>
{*PAGINATION*}
{include file="tpls:tpls/pagination.tpl"}
{*/PAGINATION*}
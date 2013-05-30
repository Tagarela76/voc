<div style="float: right; margin: 25px; width: 335px">
    <div style='float: left;'>
        Company List<br>
        <select id='companyId' name='companyId' onchange="manager.getFacilityList();">
            <option value='null'>All</option>
            {foreach from=$companyList item=company}
                <option value='{$company.id|escape}'>{$company.name|escape}</option>
            {/foreach}
        </select>
    </div>
    <div style='float: right;'>
        Facility List<br>
        <select id = 'facilityId' name='facilityId' onchange="manager.getInspectionTypeList();">
            <option value='null'>All</option>
            {foreach from=$facilityList item=facility}
                <option value='{$facility->getFacilityId()|escape}'>{$facility->getName()|escape}</option>
            {/foreach}
        </select>
    </div>
</div>

<div class="padd7" align="center" id = 'inspectionTypeList'>
    
    <table  class="users"  cellspacing="0" cellpadding="0">
        <tr class="users_u_top_size users_top_brown">
            <td class="users_u_top_brown" width="5%">
                All/None
            </td>
            <td width="10%">
                ID
            </td>
            <td width="25%">
                Inspection Type Name
            </td>
            <td width="10%">
                Facility Id
            </td>
            <td width="10%">
                Has Permit
            </td>
            <td  class="users_u_top_r_brown" width="10%">
                Edit
            </td>
        </tr>
        
        {foreach from=$inspectionTypeList item=inspectionType}
        {assign var="settings" value=$inspectionType->getInspectionType()}
            <tr>
                <td class="border_users_b border_users_r border_users_l" width="5%">
                    <input type='checkbox'>
                </td>
                <td class="border_users_b border_users_r" width="10%">
                    {$inspectionType->getId()|escape}
                </td>
                <td class="border_users_b border_users_r" width="25%">
                    {$settings->typeName|escape}
                </td>
                <td class="border_users_b border_users_r">
                    {$inspectionType->getFacilityId()|escape}
                </td>
                <td class="border_users_b border_users_r">
                    {if $settings->permit}
                        yes
                    {else}
                        no
                    {/if}
                </td>
                <td class="border_users_b border_users_r">
                    edit
                </td>
            </tr>
        {/foreach}
    </table>
    <div align="center"><div class="users_bottom"><div class="users_u_bottom"><div class="users_u_bottom_r"></div></div></div></div>
</div>
    <input type='hidden' id='action' value="{$action}">
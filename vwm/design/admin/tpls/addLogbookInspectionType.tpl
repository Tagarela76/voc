{literal}
    <script type='text/javascript'>
        var logbookInspectionType = new LogbookInspectionType();
        var temporarySubTypeId = 0;
    </script>
{/literal}
<div class="padd7" align="center">
    <table class="users"  cellspacing="0" cellpadding="0">
        <tr class="users_u_top_size users_top_brown">
            <td class="users_u_top_brown">
                Edit Inspection Type
            </td>
            <td  class="users_u_top_r_brown">

            </td>
        </tr>
        <tr>
            <td class="border_users_b border_users_r border_users_l" width ='10%' height = '30px'>
                Inspection Type Name
            </td>
            <td class="border_users_b border_users_r border_users_l">
                <input type='textbox' name='inspectionTypeName' id='inspectionTypeName' style="width: 240px;">
            </td>
        </tr>
        <tr>
            <td class="border_users_b border_users_r border_users_l" width ='10%' height = '30px'>
                Company
            </td>
            <td class="border_users_b border_users_r border_users_l">
                <select id='companyId' name='companyId' onchange="manager.getFacilityList();">
                    {foreach from=$companyList item=company}
                        <option value='{$company.id|escape}' {if $companyId == $company.id} selected = 'selected'{/if}>
                            {$company.name|escape}
                        </option>
                    {/foreach}
                </select>
            </td>
        </tr>
        <tr>
            <td class="border_users_b border_users_r border_users_l" width ='10%' height = '30px'>
                Facility
            </td>
            <td class="border_users_b border_users_r border_users_l">
                <select id = 'facilityId' name='facilityId' id = 'facilityId' >
                    {foreach from=$facilityList item=facility}
                        <option value='{$facility->getFacilityId()|escape}' {if $facilityId == $facility->getFacilityId()} selected = 'selected'{/if}>
                            {$facility->getName()|escape}
                        </option>
                    {/foreach}
                </select>
            </td>
        </tr>
        <tr>
            <td class="border_users_b border_users_r border_users_l" width ='10%' height = '30px'>
                Permit
            </td>
            <td class="border_users_b border_users_r border_users_l">
                <input type='checkbox' id='inspectionTypePermit'>
            </td>
        </tr>
    </table>
    <div align="center"><div class="users_bottom"><div class="users_u_bottom"><div class="users_u_bottom_r"></div></div></div></div>
</div>

<div style='margin: 1px 0 0 25px; font-size: 20'>
    <div>
        Sub Types
        <input type='checkbox' id='showSubTypesList' onchange="manager.showSubTypesList()">
    </div>
</div>

<div class="padd7" align="center" id = 'subTypeList' hidden = 'hidden'>
    <div style = 'float: left; margin: 0 0 10px 18px;'>
        <input type = 'button' class="button" value="Add SubType" onclick='inspection.inspectionSubTypeAddEdit.openDialog();'>
        <input type = 'button' class="button" value="Delete SubTypes" onclick='manager.deleteInspectionSubTypes();'>
    </div>
    <table class="users"  cellspacing="0" cellpadding="0" id='inspectionSubTypeDetails'>
        <tr class="users_u_top_size users_top_brown">
            <td class="users_u_top_brown" width="5%">
                All/None
            </td>
            <td width="10%">
                Name
            </td>
            <td width="25%">
                Has Notes
            </td>
            <td width="10%">
                Has Qty
            </td>
            <td width="10%">
                Has Gauge
            </td>
            <td  class="users_u_top_r_brown" width="10%">
                Edit
            </td>
        </tr>
    </table>
    <div align="center"><div class="users_bottom"><div class="users_u_bottom"><div class="users_u_bottom_r"></div></div></div></div>
</div>

<div style='margin: 1px 0 0 25px; font-size: 20'>
    <div>
        Gauge Types
        <input type='checkbox' id='showGaugeTypeList' onchange="manager.showGaugeTypeList()">
    </div>
</div>

<div class="padd7" align="center" id = 'gaugeTypeList' hidden="hidden">
    <div style = 'float: left; margin: 0 0 10px 18px;'>
        <input type = 'button' class="button" value="Add Gauge Type">
    </div>
    <table class="users"  cellspacing="0" cellpadding="0" id=''>
        <tr class="users_u_top_size users_top_brown">
            <td class="users_u_top_brown" width="5%">
                All/None
            </td>
            <td width="10%">
                Name
            </td>
            <td width="25%">
                Gauge Type
            </td>
            <td  class="users_u_top_r_brown" width="10%">
                Edit
            </td>
        </tr>
    </table>
    <div align="center"><div class="users_bottom"><div class="users_u_bottom"><div class="users_u_bottom_r"></div></div></div></div>
</div>
<div>
    <input type="button" class="button" value="Save" onclick="manager.saveInspectionType();">
</div>

<div id="addInspectionSubTypeContainer" title="Add new Inspection Sub Type" style="display:none;">Loading ...</div>
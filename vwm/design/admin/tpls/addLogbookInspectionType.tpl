{literal}
    <script type='text/javascript'>
        var logbookInspectionType = new LogbookInspectionType();
        var temporarySubTypeId = 0;
        var temporaryGaugeTypeId = 0;
        var isEdit = '{/literal}{$isEdit}{literal}';
        
        //initialize inspection type if edit
        if(isEdit=='1'){
            logbookInspectionType.load({/literal}'{$json}'{literal});
        }
        
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
                <input type='textbox' name='inspectionTypeName' id='inspectionTypeName' style="width: 240px;" value='{$settings->typeName}'>
            </td>
        </tr>
        <tr>
            <td class="border_users_b border_users_r border_users_l" width ='10%' height = '30px'>
                Company
            </td>
            <td class="border_users_b border_users_r border_users_l">
                <select id='companyId' name='companyId' onchange="manager.getFacilityList();">
                    <option value='null'>All</option>
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
                    <option value='null'>All</option>
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
                <input type='checkbox' id='inspectionTypePermit' {if $settings->permit|escape}checked = 'true'{/if}>
            </td>
        </tr>
    </table>
    <div align="center"><div class="users_bottom"><div class="users_u_bottom"><div class="users_u_bottom_r"></div></div></div></div>
</div>

<div style='margin: 1px 0 0 25px; font-size: 20'>
    <div>
        Sub Types
        <input type='checkbox' id='showSubTypesList' onchange="manager.showSubTypesList()" {if $settings->subtypes|escape}checked = 'true'{/if}>
    </div>
</div>

<div class="padd7" align="center" id = 'subTypeList' {if !$settings->subtypes|escape}hidden = 'hidden'{/if}>
    <div style = 'float: left; margin: 0 0 10px 18px;'>
        <input type = 'button' class="button" value="Add SubType" onclick='inspection.checkNewDialog(0, "add"); inspection.inspectionSubTypeAddDialog.openDialog();'>
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
        {assign var='subtypes' value=$settings->subtypes}
            {section name=i loop=$subtypes}
                <tr id="subType_detail_{$smarty.section.i.index}">
                    <td class="border_users_b border_users_l" width="5%">
                        <div>
                            <input type = 'checkbox' value="{$smarty.section.i.index}">
                        </div>
                    </td>
                    <td width="10%" class="border_users_b border_users_l" name='{$smarty.section.i.index}'>
                        <div id="subtype_name_{$smarty.section.i.index}">
                            {$subtypes[i]->name|escape}
                        </div>
                    </td>
                    <td width="25%" class="border_users_b border_users_l">
                        <div id="subtype_notes_{$smarty.section.i.index}">
                            {if $subtypes[i]->notes|escape}yes{else}no{/if}
                        </div>
                    </td>
                    <td width="10%" class="border_users_b border_users_l">
                        <div id="subtype_qty_{$smarty.section.i.index}">
                            {if $subtypes[i]->qty|escape}yes{else}no{/if}
                        </div>
                    </td>
                    <td width="10%" class="border_users_b border_users_l">
                        <div id="subtype_gauge_{$smarty.section.i.index}">
                            {if $subtypes[i]->valueGauge|escape}yes{else}no{/if}
                        </div>
                    </td>
                    <td class="border_users_b border_users_r border_users_l">
                        <div>
                            <a onclick="inspection.checkNewDialog('{$smarty.section.i.index}', 'edit'); inspection.inspectionSubTypeAddDialog.openDialog();">edit</a>
                        </div>
                    </td>
                </tr>
            {/section}
    </table>
    <div align="center"><div class="users_bottom"><div class="users_u_bottom"><div class="users_u_bottom_r"></div></div></div></div>
</div>

<div style='margin: 1px 0 0 25px; font-size: 20'>
    <div>
        Gauge Types
        <input type='checkbox' id='showGaugeTypeList' onchange="manager.showGaugeTypeList()" {if $settings->additionFieldList|escape}checked='true'{/if}>
    </div>
</div>

<div class="padd7" align="center" id = 'gaugeTypeList' {if !$settings->additionFieldList|escape}hidden = 'hidden'{/if}>
    <div style = 'float: left; margin: 0 0 10px 18px;'>
        <input type = 'button' class="button" value="Add Gauge Type" onclick="inspection.checkNewDialog(0, 'add');inspection.inspactionGaugeTypeDialog.openDialog()">
        <input type = 'button' class="button" value="Delete Gauge Types" onclick='manager.deleteInspectionGaugeTypes();'>
    </div>
    <table class="users"  cellspacing="0" cellpadding="0" id='inspectionGaugeTypeDetails'>
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
        {assign var='additionFieldList' value=$settings->additionFieldList}
        {section name=i loop=$additionFieldList}
            <tr id='gaugeType_detail_{$smarty.section.i.index}'>
            <td class="border_users_b border_users_l" width="5%">
                <input type='checkbox' value='{$smarty.section.i.index}'>
            </td>
            <td width="10%" class="border_users_b border_users_l">
                <div id="gauge_name_{$smarty.section.i.index}">
                    {$additionFieldList[i]->name|escape}
                </div>
            </td>
            <td width="25%" class="border_users_b border_users_l" id="gauge_type_{$smarty.section.i.index}">
                {assign var='gaugeId' value=$additionFieldList[i]->gaugeType}
                    {$gaugeList[$gaugeId].name}
            </td>
            <td  class="border_users_b border_users_r border_users_l" width="10%">
                <div>
                    <a onclick="inspection.checkNewDialog('{$smarty.section.i.index}', 'edit'); inspection.inspactionGaugeTypeDialog.openDialog();">edit</a>
                </div>
            </td>
        </tr>
        {/section}
    </table>
    <div align="center"><div class="users_bottom"><div class="users_u_bottom"><div class="users_u_bottom_r"></div></div></div></div>
</div>
<div style='margin: 15px 10px 10px 20px'>
    <input type="button" class="button" value="Save" onclick="manager.saveInspectionType();">
    <input type="button" class="button" value="<<<Back" onclick="window.location='?action=browseCategory&category=logbook'">
    <div id='typeSaveErrors' style='color: #ff0000; margin: 20px 1px 1px 1px;'>
    </div>
</div>
    <input type='hidden' id='logbookInspectionTypeId' value="{$logbookInspectionType->getId()}">
<div id="addInspectionSubTypeContainer" title="Add new Inspection Sub Type" style="display:none;">Loading ...</div>
<div id="inspectionGaugeTypeContainer" title="Add Inspection Gauge Type" style="display:none;">Loading ...</div>
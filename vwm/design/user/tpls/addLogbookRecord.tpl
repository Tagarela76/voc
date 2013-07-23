{literal}
   <script type="text/javascript" src="modules/js/jquery-ui-1.8.2.custom/jquery-plugins/slider/js/jquery.slider.js"></script>
     <script>
        var jSlider = jQuery;
    </script>
    <script type="text/javascript" src="modules/js/manageLogbookRecord.js"></script>
    <script type="text/javascript" src="modules/js/jquery-1.5.2.js"></script>
    <script type="text/javascript" src="modules/js/jquery-ui-1.8.2.custom/js/jquery-ui-1.8.2.custom.min.js"></script>
    <script type="text/javascript" src="modules/js/jquery-ui-1.8.2.custom/jquery-plugins/timepicker/jquery-ui-timepicker-addon.js"></script>


    <script type="text/javascript">
        var itlManager = new ManageLogbookRecord();
        itlManager.setjSonInspectionType({/literal}{$jsonInspectionalTypeList}{literal});
        itlManager.setjSonDescriptionType({/literal}{$jsonDescriptionTypeList}{literal});
        var facilityId ={/literal}'{$facilityId}'{literal};
        var inspectionPerson = new InspectionPersonSettings();
        
       $(function() {
            $('#dateTime').datetimepicker({
                                dateFormat: '{/literal}{$dataChain->getFromTypeController('getFormatForCalendar')}{literal}',
                                ampm: true
                        });
            itlManager.inspectionTypeList.getSubTypesAdditionFields({/literal}{$logbook->getValueGaugeType()}{literal});
            itlManager.description.showNotes();
            itlManager.gauges.setGaugeRanges({/literal}{$gaugeListJson}{literal});
            itlManager.gauges.initGauges('{/literal}{$logbook->getGaugeValueFrom()}{literal}','{/literal}{$logbook->getGaugeValueTo()}{literal}');
            itlManager.gauges.checkGaugeValueRange();
            itlManager.gauges.changeGauge();
            itlManager.equipmant.showEquipmentList();
            
            inspectionPerson.addInspectionPerson.iniDialog();
        });
    </script>
{/literal}
{if $color eq "green"}
    {include file="tpls:tpls/notify/greenNotify.tpl" text=$message}
{/if}
{if $color eq "orange"}
    {include file="tpls:tpls/notify/orangeNotify.tpl" text=$message}
{/if}
{if $color eq "blue"}
    {include file="tpls:tpls/notify/blueNotify.tpl" text=$message}
{/if}

<form action="" method="post">
    <div class="padd7">
        <table class="users" align="center" cellpadding="0" cellspacing="0">
            <tr class="users_u_top_size users_top_brown">
                <td class="users_u_top_brown">
                    <span >Add Logbook record</span>
                </td>
                <td class="users_u_top_r_brown">
                    &nbsp;
                </td>
            </tr>

            <tr class="border_users_b border_users_r" height='30'>
                <td class="border_users_l">
                    Inspection Person
                </td>
                <td>
                    <select id = 'InspectionPersons' name = 'InspectionPersons'>
                        {foreach from=$inspectionPersonList item=inspectionPerson}
                            <option value="{$inspectionPerson->getId()|escape}" {if $inspectionPerson->getId()==$logbook->getInspectionPersonId()}selected='selected'{/if}>
                                {$inspectionPerson->getName()|escape}{if $inspectionPerson->getIsDeleted()} (deleted){/if}
                            </option>
                        {/foreach}
                    </select>
                    <a onclick='inspectionPerson.addInspectionPerson.openDialog()'>add Inspection Person</a>
                </td>
            </tr>
            {if $inspectionTypesList}
            <tr class="border_users_b border_users_r" height='30' >
                <td class="border_users_l">
                    Inspection Type
                </td>
                <td>
                    <div>
                        <select id='inspectionType' name='inspectionType' onchange="itlManager.inspectionTypeList.changeSubTypeList(); itlManager.inspectionTypeList.changeLogbookDescriptionList()">
                            {section name=i loop=$inspectionTypesList}
                                <option value="{$inspectionTypesList[i]->id|escape}" {if $logbook->getInspectionTypeId() == $inspectionTypesList[i]->id}selected='selected'{/if}>
                                    {$inspectionTypesList[i]->typeName|escape}
                                </option>
                            {/section}
                        </select>
                    </div>
                    <div id ='inspectionAdditionListTypeContainer' hidden="hidden">
                        <select id ='inspectionAdditionListType' name='inspectionAdditionListType'  onchange="itlManager.inspectionTypeList.getSubTypesAdditionFields();itlManager.gauges.changeGauge()">
                            {section name=i loop=$inspectionAdditionTypesList}
                                <option value="{$inspectionAdditionTypesList[i]->name|escape}" {if $logbook->getInspectionAdditionType() == $inspectionAdditionTypesList[i]->name}selected='selected'{/if}>
                                    {$inspectionAdditionTypesList[i]->name|escape}
                                </option>
                            {/section}
                        </select>
                    </div>    
                    <div >
                        <select id='inspectionSubType' name='inspectionSubType' onchange="itlManager.inspectionTypeList.changeSubType()" {if !$inspectionSubTypesList}hidden='hidden'{/if}>
                            {section name=i loop=$inspectionSubTypesList}
                                <option value="{$inspectionSubTypesList[i]->name|escape}" {if $logbook->getInspectionSubType() == $inspectionSubTypesList[i]->name}selected='selected'{/if}>
                                    {$inspectionSubTypesList[i]->name|escape}
                                </option>
                            {/section}
                        </select>
                    </div>
                </td>
            </tr>
            {/if}
            {* types and sub types addition fields*}

            <!--<tr class="border_users_b border_users_r" height='30' id='logBookPermit' hidden="hidden">
                <td class="border_users_l">
                    Permit
                </td>
                <td>
                    <input type="checkbox" name='permit' id ='permit'{if $logbook->getPermit() == 1}checked='checked'{/if}>
                </td>
                {foreach from=$violationList item="violation"}
                    {if $violation->getPropertyPath() eq 'permit'}
                        {*ERROR*}
                    <div class="error_img" style="float: left;"><span class="error_text">{$violation->getMessage()}</span></div>
                    {*/ERROR*}
                    {/if}
                {/foreach}
            </tr>-->

            <tr class="border_users_b border_users_r" height='30' id='subTypeQty' hidden="hidden">
                <td class="border_users_l">
                    QTY
                </td>
                <td>
                    <input type="number" name =  "qty"  id='qty' value="{$logbook->getQty()}">
                </td>
                {foreach from=$violationList item="violation"}
                    {if $violation->getPropertyPath() eq 'qty'}
                        {*ERROR*}
                    <div class="error_img" style="float: left;"><span class="error_text">{$violation->getMessage()}</span></div>
                        {*/ERROR*}
                    {/if}
                {/foreach}
            </tr>

            <tr class="border_users_b border_users_r" height='30' id='logBookSubTypeNotes' hidden="hidden">
                <td class="border_users_l">
                    Sub Type Notes
                </td>
                <td>
                    <textarea name="subTypeNotes" id='subTypeNotes'>{if $logbook->getSubTypeNotes() != 'NONE'}{$logbook->getSubTypeNotes()}{/if}</textarea>
                </td>
            </tr>
            {*gauges*}

                <tr class="border_users_b border_users_r" height='30' id='logbookValueGauge' hidden="hidden">
                    <td class="border_users_l">
                        Value Gauge
                    </td>
                    <td>
                        <div>
                            <select name="gaugeType" id='gaugeType' onchange="itlManager.gauges.changeGauge(); itlManager.gauges.checkGaugeValueRange();">
                                <option value="null">Select Gauge</option>
                                {section name=i loop=$gaugeList}
                                    <option value="{$smarty.section.i.index}" {if $logbook->getValueGaugeType() == $smarty.section.i.index}selected='selected'{/if}>{$gaugeList[i].name}</option>
                                {/section}
                            </select>
                        </div>
                        {*slider*}
                        <div id='gaugeSlider' hidden="hidden">
                            <div id = 'gaugeRange' style="margin: 0 0 0 0; display: inline-block;">
                                from<input type='number' id = 'gaugeRangeFrom'  name='gaugeRangeFrom' style="width:40px" value='{$logbook->getMinGaugeRange()}'>
                                to<input type='number' id = 'gaugeRangeTo' name='gaugeRangeTo' style="width:40px" value='{$logbook->getMaxGaugeRange()}'>
                                <a onclick="itlManager.gauges.updateGauge()">
                                    Update Gauge
                                </a>
                            </div>
                            <div style="width: 400px; padding: 25px 7px"  id='gaugeConteiner'>
                                <input id="LogbookGauge" type="slider" name="gaugeValue" value="{$logbook->getGaugeValueFrom()};{$logbook->getGaugeValueTo()}" height="20"/>
                            </div>
                            <div id='temperatureCelContainer'>
                                <select onchange="itlManager.gauges.changeGaugeUnitType()" id='gaugeDimension'  style='width: 45px;'>
                                    {section name=i loop=$unitTypeList}
                                    <option value='{$unitTypeList[i]->getUnitTypeId()|escape}' {if $unitTypeList[i]->getUnitTypeId() == $logbook->getUnittypeId()}selected='true'{/if}>
                                        {$unitTypeList[i]->getName()|escape}
                                    </option>
                                    {/section}
                                </select>
                            </div>
                            <input type='hidden' id='gaugeUnitTypeId' name='gaugeUnitType' value={$logbook->getUnittypeId()}>
                            {assign var=unitType value=$logbook->getLogbookUnitType()|escape}
                            <input type='hidden' id='gaugeUnitTypeDescription' name='gaugeUnitTypeDescription' value='{if $unitType}{$unitType->getName()|escape}{/if}'>
                        </div>
                    </td>
                </tr>
                <tr class="border_users_b border_users_r" height='30' id='logbookReplacedBulbs' >
                    <td class="border_users_l">
                        Replaced Bulbs
                    </td>
                    <td>
                        <input type='checkbox' id = 'replacedBulbs' name='replacedBulbs'>
                    </td>
                </tr>
            <tr class="border_users_b border_users_r" height='30'>
                <td class="border_users_l">
                    Description
                </td>
                <td>
                    <select id="logBookDescription" name = "logBookDescription" onchange="itlManager.description.changeDescription();">
                         <option value="None">
                               None
                         </option>
                        {section name=i loop=$logbookDescriptionsList}
                            <option value="{$logbookDescriptionsList[i]->id|escape}" {if $logbook->getDescriptionId() == $logbookDescriptionsList[i]->id}selected='selected'{/if}>
                                {$logbookDescriptionsList[i]->description|escape}
                                {if $logbookDescriptionsList[i]->deleted|escape} (deleted){/if}
                            </option>
                        {/section}
                    </select>
                    <div>
                        <textarea name="logBookDescriptionNotes" id="logBookDescriptionNotes" hidden="hidden">{if $logbook->getDescriptionNotes() != 'NONE'}{$logbook->getDescriptionNotes()}{/if}</textarea>
                    </div>
                </td>
            </tr>

            <tr class="border_users_b border_users_r" height='30'>
                <td class="border_users_l">
                    Date/Time
                </td>
                <td>
                    <div align="left">
                        <input type="text" name="dateTime" id="dateTime" class="calendarFocus" value='{$creationTime|escape}'/>
                    </div>
                    {foreach from=$violationList item="violation"}
                        {if $violation->getPropertyPath() eq 'date_time'}
                            {*ERROR*}
                            <div class="error_img" style="float: left;"><span class="error_text">{$violation->getMessage()}</span></div>
                                {*/ERROR*}
                            {/if}
                        {/foreach}
                </td>
            </tr>

            <tr class="border_users_b border_users_r" height='30'>
                <td class="border_users_l">
                    Equipment or H&S 
                </td>
                <td>
                    <select onchange="itlManager.equipmant.showEquipmentList();" id='isEquipment' name="isEquipment">
                        <option value="equipment" >
                            Equipment 
                        </option>
                        <option value="facility" {if $logbook->getEquipmentId()=='0'}selected='selected'{/if}>
                            Facility Health & Safety (H&S)
                        </option>
                    </select>
                </td>
            </tr>
            <tr class="border_users_b border_users_r" height='30' hidden="hidden" id = 'showEquipmentList'>
                <td class="border_users_l">
                    Equipment
                </td>
                <td>
                    <div id='equipmentListContainer'>
                        <div style="width: 150px">
                            Select Equipment
                        </div>
                        
                        <select id ='equipmentList' name='logbookEquipmentId'>
                            {foreach from=$logbookEquipmentList item="logbookEquipment"}
                                <option value="{$logbookEquipment.id|escape}" {if $logbookEquipment.id == $logbook->getEquipmentId()}selected = 'selected'{/if}>
                                    {$logbookEquipment.description|escape}
                                </option>
                            {/foreach}
                        </select>
                    </div>
                    {foreach from=$violationList item="violation"}
                        {if $violation->getPropertyPath() eq 'equipment_id'}
                            {*ERROR*}
                            <div class="error_img" style="float: left;"><span class="error_text">{$violation->getMessage()}</span></div>
                                {*/ERROR*}
                            {/if}
                        {/foreach}
                </td>
            </tr>
        </table>
        <div align="center" ><div class="users_bottom"><div class="users_u_bottom"><div class="users_u_bottom_r"></div></div></div></div>

        <div align="right" style="padding: 12px 12px">
            <input type="submit" value="Save" class="button">
            <input type="button" value="Cancel" class="button" onclick='history.back()'>
        </div>
    </div>

    <input type='hidden' name="action" value="{$action}">
    <input type='hidden' name="category" value="{$category}">
    <input type='hidden' name="addToEquipment" value="{$addToEquipment}">
</form>

{*add inspection perswon dialog container*}
<div id='addInspectionPersonContainer' title="Add New Inspection Person" style="display:none;">Loading ...</div>




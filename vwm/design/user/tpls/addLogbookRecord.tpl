{literal}
    <script type="text/javascript">
        var itlManager = new ManageLogbookRecord();
        itlManager.setjSon({/literal}{$jsonInspectionalTypeList}{literal});
        var facilityId ={/literal}{$facilityId}{literal}
                $(function() {
            $('#dateTime').datetimepicker({dateFormat: '{/literal}{$dataChain->getFromTypeController('getFormatForCalendar')}{literal}'});
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
            <tr class="users_u_top_size users_top">
                <td class="users_u_top">
                    <span >Add Logbook record</span>
                </td>
                <td class="users_u_top_r">
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
                            <option value="{$inspectionPerson->getId()}">
                                {$inspectionPerson->getName()}
                            </option>
                        {/foreach}
                    </select>
                    <a onclick='inspectionPerson.addInspectionPerson.openDialog();'>add Inspection Person</a>
                </td>
            </tr>

            <tr class="border_users_b border_users_r" height='30'>
                <td class="border_users_l">
                    Inspection Type
                </td>
                <td>
                    <div>
                        <select id='inspectionType' name='inspectionType' onchange="itlManager.inspectionTypeList.changeSubTypeList()">
                            {section name=i loop=$inspectionTypesList}
                                <option value="{$smarty.section.i.index}">
                                    {$inspectionTypesList[i]->typeName|escape}
                                </option>
                            {/section}
                        </select>
                    </div>
                    <div>
                        <select id='inspectionSubType' name='inspectionSubType' onchange="itlManager.inspectionTypeList.getSubTypesAdditionFields()">
                            {section name=i loop=$inspectionSubTypesList}
                                <option value="{$smarty.section.i.index}">
                                    {$inspectionSubTypesList[i]->name|escape}
                                </option>
                            {/section}
                        </select>
                    </div>
                </td>
            </tr>
            {* types and sub types addition fields*}

            <tr class="border_users_b border_users_r" height='30' id='logBookPermit' hidden="hidden">
                <td class="border_users_l">
                    Permit
                </td>
                <td>
                    <input type="checkbox" name='permit'>
                </td>
            </tr>

            <tr class="border_users_b border_users_r" height='30' id='subTypeQty' hidden="hidden">
                <td class="border_users_l">
                    QTY
                </td>
                <td>
                    <input type="number" name =  "qty">
                </td>
            </tr>

            <tr class="border_users_b border_users_r" height='30' id='logBookSubTypeNotes' hidden="hidden">
                <td class="border_users_l">
                    Sub Type Notes
                </td>
                <td>
                    <textarea name="logBookSubTypeNotes"></textarea>
                </td>
            </tr>

            <tr class="border_users_b border_users_r" height='30'>
                <td class="border_users_l">
                    Description
                </td>
                <td>
                    <select id="logBookDescription" name = "logBookDescription" onchange="itlManager.description.showNotes();">
                        {section name=i loop=$logbookDescriptionsList}
                            <option value="{$smarty.section.i.index}">
                                {$logbookDescriptionsList[i]->name|escape}
                            </option>
                        {/section}
                    </select>
                    <div>
                        <textarea name="logBookDescriptionNotes" id="logBookDescriptionNotes" hidden="hidden"></textarea>
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
                    Reports
                </td>
                <td>
                    <select>
                    </select>
                </td>
            </tr>
        </table>
        <div align="right">
            <input type="submit" value="Save" class="button">
            <input type="button" value="Cancel" class="button" onclick='history.back()'>
        </div>
    </div>

    <input type='hidden' name="action" value="{$action}">
    <input type='hidden' name="category" value="{$category}">
</form>

{*add inspection perswon dialog container*}
<div id='addInspectionPersonContainer' title="Add New Inspection Person" style="display:none;">Loading ...</div>

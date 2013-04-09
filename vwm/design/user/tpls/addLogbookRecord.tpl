{literal}
<script type="text/javascript">
	$(function() {
        var itlManager = new manageInspectionTypeList();
        itlManager.jsonString = {/literal}{$jsonInspectionalTypeList}{literal}
        //alert(itlManager.jsonString.description[1].name);
        $('#date').datepicker({ dateFormat: '{/literal}{$dataChain->getFromTypeController('getFormatForCalendar')}{literal}' }); 
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

<form>
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
                    <select>
                    </select>
                </td>
            </tr>

            <tr class="border_users_b border_users_r" height='30'>
                <td class="border_users_l">
                    Inspection Type
                </td>
                <td>
                    <div>
                        <select id='inspectionType' name='inspectionType'>
                            {section name=i loop=$inspectionTypesList}
                                <option value="{$smarty.section.i.index}">
                                    {$inspectionTypesList[i]->typeName|escape}
                                </option>
                            {/section}
                        </select>
                    </div>
                    <div>
                        <select id='inspectionSubType' name='inspectionSubType'>
                            {section name=i loop=$inspectionSubTypesList}
                                <option value="{$smarty.section.i.index}">
                                    {$inspectionSubTypesList[i]->name|escape}
                                </option>
                            {/section}
                        </select>
                    </div>
                </td>
            </tr>

            <tr class="border_users_b border_users_r" height='30'>
                <td class="border_users_l">
                    Permit
                </td>
                <td>
                    <input type='checkbox'>
                </td>
            </tr>

            <tr class="border_users_b border_users_r" height='30'>
                <td class="border_users_l">
                    Description
                </td>
                <td>
                    <select>
                        {section name=i loop=$logbookDescriptionsList}
                            <option value="{$smarty.section.i.index}">
                                {$logbookDescriptionsList[i]->name|escape}
                            </option>
                        {/section}
                    </select>
                </td>
            </tr>

            <tr class="border_users_b border_users_r" height='30'>
                <td class="border_users_l">
                    Date
                </td>
                <td>
                    <div align="left">
                        <input type="text" name="creationTime" id="date" class="calendarFocus" value='{$creationTime|escape}'/>
                    </div>
                </td>
            </tr>

            <tr class="border_users_b border_users_r" height='30'>
                <td class="border_users_l">
                    Time
                </td>
                <td>
                    <div align="left">
                        <input type="text" name="creationTime" id="time" class="calendarFocus" value='{$creationTime|escape}'/>
                    </div>
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

</form>
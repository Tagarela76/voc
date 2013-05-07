<form method='GET' action='' id='createReportForm'>
    <div align="center" width="100%">
        <div align="left" style="display:block;width:100%">
            {*shadow_table*}
            <table cellspacing="0" cellpadding="0" align="center" width="90%">
                <tr>
                    <td valign="top" class="report_uploader_t_l">
                    </td>
                    <td valign="top" class="report_uploader_t">
                    </td>
                    <td valign="top" class="report_uploader_t_r">
                    </td>
                </tr>
                <tr>
                    <td valign="top" class="report_uploader_l">
                    </td>
                    <td valign="top" class="report_uploader_c">
                        {*shadow_table*}
						<h1><b>{$reportName|escape}</b></h1>

                        <table width="100%" align="center" cellspacing="0">
                            <tr>
                                <td width="25%" class="pcenter">
                                    Format
                                    <div id="csvFormatLabel" style="display:none;">
                                        Separated by:
                                        <br>
                                        Text delimiter:
                                    </div>
                                </td>
                                <td class="pcenter">
                                    <div align="left">
                                        <select name="format" onchange="onChangeFormat(this)">
                                            {*<option value="html" {if $data.reportType=="html"}selected{/if}>HTML</option>*}
											<option value="pdf" {if $data.reportType=="pdf"}selected{/if}>PDF</option>
                                            {*<option value="csv" {if $data.reportType=="csv"}selected{/if}>CSV</option>
                                            <option value="excel" {if $data.reportType=="excel"}selected{/if}>EXCEL</option>*}
                                        </select>
                                        <div id="csvFormatInputs" style="display:none;">
                                            <input type="text" name="commaSeparator" value=','/>
                                            <br>
                                            <input type="text" name="textDelimiter" value='"'/>
                                        </div>
                                    </div>
                                </td>
                            </tr>

                            <tr id="dateBegin">
                                <td class="pcenter">
                                    Date begin:
                                </td>
                                <td class="pcenter">
                                    <div align="left">
                                        <input type="text" name="date_begin" id="calendar1" class="calendarFocus" value=''/>
                                    </div>
                                </td>
                            </tr>
                            <tr id="dateEnd">
                                <td class="pcenter">
                                    Date end:
                                </td>
                                <td class="pcenter">
                                    <div align="left">
                                        <input type="text" name="date_end" id="calendar2" class="calendarFocus" value=''/><span id="waitDate" style="display:none;"></span>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td class="pcenter">
                                    Equipment
                                </td>
                                <td>
                                    <select name ='equipmentId'>
                                        {foreach from=$equipments item=equipment}
                                            <option value="{$equipment.equipment_id}">
                                                {$equipment.equip_desc}
                                            </option>
                                        {/foreach}
                                    </select>
                                    </td>
                            </tr>
                            <tr>
                                <td>
                                </td>
                                <td height="40px" valign="middle" align="right">
                                    <input class="button" align="right" type="button" name="goBack" value="Back" onClick="{if $backUrl}location.href='{$backUrl}'{else}history.go(-1);return true;{/if}"/>
                                    <input class="button" align="right" type="button" value="Submit" onClick='{*CheckDate()*}form.submit()'/>{*PLEASE ADD HERE NEW DATE VALIDATOR BY ITS TYPE*}
                                </td>
                            </tr>
                        </table>
						<input type="hidden" name="action" value="prepareSendLogbookReport">
						<input type="hidden" name="reportType" value="{$subReport}">
						{*<input type='hidden' id="itemID" name='itemID' value='{$itemID}'>*}
						<input type='hidden' id="itemID" name='itemID' value='logbookReports'>
						{*<input type='hidden' id="categoryLevel" name='categoryLevel' value='{$categoryLevel}'>*}
						<input type='hidden' id="category" name='category' value='logbookReports'>
						{*<input type="hidden" id="id" name="id" value="{$id}">*}
						<input type="hidden" id="id" name="id" value="{$request.id}">
						{*/shadow_table*}
                    </td>
                    <td valign="top" class="report_uploader_r">
                    </td>
                </tr>
                <tr>
                    <td valign="top" class="report_uploader_b_l">
                    </td>
                    <td valign="top" class="report_uploader_b">
                    </td>
                    <td valign="top" class="report_uploader_b_r">
                    </td>
                </tr>
            </table>
            {*/shadow_table*}
        </div>
    </div>
</form>
{literal}
<script>
    function clearInputBox(item){
        item.value = "";
    }

    $(document).ready(function(){
      /*  popUpCal.dateFormat = 'MDY/';
        $("#calendar1, #calendar2").calendar();*/
		 $('#calendar1, #calendar2').datepicker({ dateFormat: '{/literal}{$dataChain->getFromTypeController('getFormatForCalendar')}{literal}'});
    });

</script>
{/literal}
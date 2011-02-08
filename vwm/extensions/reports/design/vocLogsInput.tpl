<form id="createReportForm" method='GET' action='' target='{$target}'>
    <div align="center" width="100%">
        <div align="left" style="display:block;width:90%;padding-bottom:10px;display:table">
            <h1><b>Daily Emissions Report</b></h1>
            <table width="30%">
                <tr>
                    <td>
                        Format
                        <div id="csvFormatLabel" style="display:none;">
                            Separated by:
                            <br>
                            Text delimiter:
                        </div>
                    </td>
                    <td>
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
                <tr>
                    <td>
                        Rule
                    </td>
                    <td>
                        <select name="logs" id="logs">
                            {section name=i loop=$rules}
                            	<option value='{$rules[i].rule_id}' {if $rules[i].rule_id  eq $data.rule}selected="selected"{/if}> {$rules[i].rule_nr}  </option>
                            {/section} 
                        </select>
                    </td>
                </tr>
                <tr id="dateBegin">
                    <td>
                        Date begin:
                    </td>
                    <td>
                        <div align="left">
                            <input type="text" name="date_begin" id="calendar1" class="calendarFocus" value=''/>
                        </div>
                    </td>
                </tr>
                <tr id="dateEnd">
                    <td>
                        Date end:
                    </td>
                    <td>
                        <div align="left">
                            <input type="text" name="date_end" id="calendar2" class="calendarFocus" value=''/><span id="waitDate" style="display:none;"></span>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
    </div>
    {*shadow*}
    <div class="shadow_list">
        <div class="shadow_list_r">
            {**}
            <table class="daily_emissions_report" width="90%" align="center" cellspacing="0">
                <tr class="daily_emissions_report report_top">
                    <td colspan="2">
                        <b>Daily Emissions Report</b>
                    </td>
                    <td colspan="2">
                        <b>PERIOD</b>
                    </td>
                    <td colspan="2" style="border-right:0px solid #E0E4EE">
                        <b>Coating and Solvent Usage</b>
                    </td>
                </tr>
                <tr>
                    <td>
                        Facility name:
                    </td>
                    <td class="report_right">
                        &nbsp;
                    </td>
                    <td class=daily_emissions_report>
                        Equip:
                    </td>
                    <td class="report_right">
                        &nbsp;
                    </td>
                    <td>
                        Responsible Person:
                    </td>
                    <td>
                        <input type="text" name="responsiblePerson" value="[Responsible Person]" onClick="clearInputBox(this)"/>
                    </td>
                </tr>
                <tr>
                    <td>
                        Address:
                    </td>
                    <td class="report_right">
                        &nbsp;
                    </td>
                    <td>
                        Permit No:
                    </td>
                    <td class="report_right">
                        &nbsp;
                    </td>
                    <td>
                        Title:
                    </td>
                    <td>
                        <input type="text" name="title" value="[Title]" onClick="clearInputBox(this)"/>
                    </td>
                </tr>
                <tr>
                    <td>
                        City, State, Zip:
                    </td>
                    <td class="report_right">
                        &nbsp;
                    </td>
                    <td>
                        Facility ID:
                    </td>
                    <td class="report_right">
                        &nbsp;
                    </td>
                    <td>
                        &nbsp;
                    </td>
                    <td>
                        &nbsp;
                    </td>
                </tr>
                <tr>
                    <td>
                        County:
                    </td>
                    <td class="report_right">
                        &nbsp;
                    </td>
                    <td>
                        Rule No:
                    </td>
                    <td class="report_right">
                        &nbsp;
                    </td>
                    <td>
                        &nbsp;
                    </td>
                    <td>
                        &nbsp;
                    </td>
                </tr>
                <tr>
                    <td>
                        Phone:
                    </td>
                    <td class="report_right">
                        &nbsp;
                    </td>
                    <td>
                        GCG No:
                    </td>
                    <td class="report_right">
                        &nbsp;
                    </td>
                    <td>
                        &nbsp;
                    </td>
                    <td>
                        &nbsp;
                    </td>
                </tr>
                <tr class="daily_emissions_report">
                    <td>
                        Fax:
                    </td>
                    <td class="report_right">
                        &nbsp;
                    </td>
                    <td>
                        Notes:
                    </td>
                    <td class="report_right">
                        <input type="text" name="notes" value="[Notes]" onClick="clearInputBox(this)"/>
                    </td>
                    <td>
                        &nbsp;
                    </td>
                    <td>
                        &nbsp;
                    </td>
                </tr>
            </table>
            {*shadow*} 
        </div>
    </div>
    {**}
    <table width="90%" align="center">
        <tr>
            <td>
            </td>
            <td valign="middle" align="right">
                <input class="button" align="right" type="button" name="goBack" value="Back" onClick="{if $backUrl}location.href='{$backUrl}'{else}history.go(-1);return true;{/if}"/>
				<input class="button" align="right" type="button" value="Submit" onClick='{*CheckDate()*}form.submit()'/>
            </td>
        </tr>
    </table>
	<input type="hidden" name="action" value="sendSubReport">
	<input type="hidden" name="reportType" value="vocLogs">
	
	{*<input type='hidden' id="itemIDlevel" name='itemID' value='{$itemID}'>
	<input type='hidden' id="categoryLevel" name='categoryLevel' value='{$categoryLevel}'>
	<input type="hidden" id="id" name="id" value="{$id}">*}
	
	<input type='hidden' id="itemIDlevel" name='itemID' value='{$request.category}'>				
	<input type='hidden' id="categoryLevel" name='categoryLevel' value='{$request.category}'>				
	<input type="hidden" id="id" name="id" value="{$request.id}">			
</form>

{literal}
<script>
    function clearInputBox(item){
        item.value = "";
    }
    
     $(document).ready(function(){
      /*  popUpCal.dateFormat = 'MDY/';
        $("#calendar1, #calendar2").calendar();*/		
		 $('#calendar1, #calendar2').datepicker({ dateFormat: '{/literal}{$dataChain->getFromTypeController('getFormatForCalendar')}{literal}' }); 
    });
</script>
{/literal}
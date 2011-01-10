<form method='GET' action='' target='{$target}'>
    <div align="center" width="100%">
        <div align="left" style="display:block;width:90%;padding-bottom:20px">
            <h1><b>Project Coating Report</b></h1>
            <table width="35%">
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
                                <!--	<option value="html" {if $data.reportType=="html"}selected{/if}>HTML</option>--><option value="pdf" {if $data.reportType=="pdf"}selected{/if}>PDF</option>
                                <!--	<option value="csv" {if $data.reportType=="csv"}selected{/if}>CSV</option>
                                <option value="excel" {if $data.reportType=="excel"}selected{/if}>EXCEL</option>-->
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
                            {section name=i loop=$rules}<option value='{$rules[i].rule_id}' {if $rules[i].rule_id  eq $data.rule}selected="selected"{/if}> {$rules[i].rule_nr}  </option>
                            {/section} 
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>
                        Frequency: 
                    </td>
                    <td>
                        <select name="frequency" onchange="onChangeFreq(this)">
                            <!--<option value="daily" {if $data.reportType=="daily"}selected{/if}>Daily</option>
                            <option value="weekly" {if $data.reportType=="weekly"}selected{/if}>Weekly</option>--><option value="monthly" {if $data.reportType=="monthly"}selected{/if}>Monthly</option>
                            <!--<option value="yearToDate" {if $data.reportType=="year to date"}selected{/if}>Year to date</option>--><option value="annualy" {if $data.reportType=="annualy"}selected{/if}>Annually</option>
                        </select>
                    </td>
                </tr>
                <tr id="monthYear">
                    <td>
                        Month/Year:
                    </td>
                    <td>
                        <div align="left">
                            <select name="monthYearSelect" id="monthYearSelect">
                                {section name=i loop=$monthes}<option value='{$monthes[i].value}'>{$monthes[i].text} </option>
                                {/section} 
                            </select>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
    </div>
    {*shadow*}
    <div class="shadow_list">
        <div class="shadow_list_r" style="">
            {**}
            <table class="daily_emissions_report" width="90%" align="center">
                <tr>
                    <td width="20%">
                        Client Name:
                    </td>
                    <td colspan=3>
                        <input type="text" name="clientName" value="[Client Name]"/  onClick="clearInputBox(this)">
                    </td>
                </tr>
                <tr>
                    <td>
                        Client Specification:
                    </td>
                    <td colspan=2>
                        <textarea name="clientSpecification" rows="5" cols="35"/  onClick="clearTextArea(this)">[Client Specification]
                        </textarea>
                    </td>
                    <td>
                        If folow fields "Contact Person", "Telephone", "Report By" are empty at pdf report ask your Supervisor to fill it at Admin Interface.
                    </td>
                </tr>
                <tr>
                    <td>
                        &nbsp;
                    </td>
                    <td>
                        Name#1
                    </td>
                    <td>
                        Name#2
                    </td>
                    <td>
                        Name#3
                    </td>
                </tr>
                <tr>
                    <td>
                        Name of Coating Manufacturer Contacted:
                    </td>
                    <td>
                        <select name="supplier1" id="supplier1">
                            <option value="">[manufacturer]</option>
                            {section name=j loop=$supplierList} <option value="{$supplierList[j].supplier_id}">{$supplierList[j].supplier_desc}</option>
                            {/section}
                        </select>
                    </td>
                    <td>
                        <select name="supplier2" id="supplier2">
                            <option value="">[manufacturer]</option>
                            {section name=j loop=$supplierList} <option value="{$supplierList[j].supplier_id}">{$supplierList[j].supplier_desc}</option>
                            {/section}
                        </select>
                    </td>
                    <td>
                        <select name="supplier3" id="supplier3">
                            <option value="">[manufacturer]</option>
                            {section name=j loop=$supplierList} <option value="{$supplierList[j].supplier_id}">{$supplierList[j].supplier_desc}</option>
                            {/section}
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>
                        Reason for Non-Availability:
                    </td>
                    <td>
                        <textarea name="reason1" rows="5" cols="35" onClick="clearTextArea(this)">[Reason for Non-Availability Name#1]
                        </textarea>
                    </td>
                    <td>
                        <textarea name="reason2" rows="5" cols="35" onClick="clearTextArea(this)">[Reason for Non-Availability Name#2]
                        </textarea>
                    </td>
                    <td>
                        <textarea name="reason3" rows="5" cols="35" onClick="clearTextArea(this)">[Reason for Non-Availability Name#3]
                        </textarea>
                    </td>
                </tr>
                <tr class="daily_emissions_report">
                    <td colspan="4">
                        Summary for compliant coating problem/failure:
                    </td>
                </tr>
                <tr class="daily_emissions_report">
                    <td colspan="4">
                        <textarea name="summary" rows="10" cols="85" onClick="clearTextArea(this)">[Summary for compliant coating problem/failure]
                        </textarea>
                    </td>
                </tr>
            </table>{*shadow*} 
        </div>
    </div>
    {**}
    <table width="90%" align="center">
        <tr>
            <td>
            </td>
            <td align="right">
                <input class="button" type="button" name="goBack" value="Back" onClick="{if $backUrl}location.href='{$backUrl}'{else}history.go(-1);return true;{/if}"/>
				<input class="button" type="submit" value="Submit"/>
            </td>
        </tr>
    </table>	
	<input type="hidden" name="action" value="sendSubReport">
	<input type="hidden" name="reportType" value="projectCoat">
	
	{*<input type='hidden' id="itemIDlevel" name='itemID' value='{$itemID}'>
	<input type="hidden" id="id" name="id" value="{$id}">
	<input type='hidden' id="categoryLevel" name='categoryLevel' value='{$categoryLevel}'>*}
	
	<input type='hidden' id="itemIDlevel" name='itemID' value='{$request.category}'>				
	<input type='hidden' id="categoryLevel" name='categoryLevel' value='{$request.category}'>				
	<input type="hidden" id="id" name="id" value="{$request.id}">			
</form> 
{literal}
<script>
    function clearInputBox(item){
        item.value = "";
    }
    
    function clearTextArea(item){
        item.innerHTML = "";
    }
</script>
{/literal}
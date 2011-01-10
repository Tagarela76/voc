{*shadow_table*} 
<table width="90%"cellspacing="0" cellpadding="0" align="center">
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
			<h1 class="titleinfo"><b>Exempt Coating Operations</b></h1>
            
			
			<form method='GET' action='' target='{$target}'>
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
                                    <!--	<option value="html" {if $data.reportType=="html"}selected{/if}>HTML</option>-->
									<option value="pdf" {if $data.reportType=="pdf"}selected{/if}>PDF</option>
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
                        <td class="pcenter">
                            Rule
                        </td>
                        <td class="pcenter">
                            <select name="logs" id="logs">
                                {section name=i loop=$rules}
									<option value='{$rules[i].rule_id}' {if $rules[i].rule_id  eq $data.rule}selected="selected"{/if}> {$rules[i].rule_nr}  </option>
                                {/section} 
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td class="pcenter">
                            Frequency: 
                        </td>
                        <td class="pcenter">
                            <select name="frequency" onchange="onChangeFreq(this)">
                                <!--<option value="daily" {if $data.reportType=="daily"}selected{/if}>Daily</option>
                                <option value="weekly" {if $data.reportType=="weekly"}selected{/if}>Weekly</option>-->
								<option value="monthly" {if $data.reportType=="monthly"}selected{/if}>Monthly</option>
                                <!--<option value="yearToDate" {if $data.reportType=="year to date"}selected{/if}>Year to date</option>--><option value="annualy" {if $data.reportType=="annualy"}selected{/if}>Annually</option>
                            </select>
                        </td>
                    </tr>
                    <tr id="monthYear">
                        <td class="pcenter">
                            Month/Year:
                        </td>
                        <td class="pcenter">
                            <div align="left">
                                <select name="monthYearSelect" id="monthYearSelect">
                                    {section name=i loop=$monthes}
										<option value='{$monthes[i].value}'>{$monthes[i].text} </option>
                                    {/section} 
                                </select>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                        </td>
                        <td align="right">
                            <div class="floatright margintop10 padd_left">
                                <input type="submit" value="Submit" class="button"/>
                            </div>
                            <div class="floatright margintop10">
                                <input class="button" type="button" name="goBack" value="Back" onClick="{if $backUrl}location.href='{$backUrl}'{else}history.go(-1);return true;{/if}"/>
                            </div>
                        </td>
                    </tr>
                </table>
				<input type="hidden" name="action" value="sendSubReport">
				<input type="hidden" name="reportType" value="exemptCoat">
				
				{*<input type='hidden' id="itemIDlevel" name='itemID' value='{$itemID}'>
				<input type='hidden' id="categoryLevel" name='categoryLevel' value='{$categoryLevel}'>
				<input type="hidden" id="id" name="id" value="{$id}">*}
								
				<input type='hidden' id="itemIDlevel" name='itemID' value='{$request.category}'>				
				<input type='hidden' id="categoryLevel" name='categoryLevel' value='{$request.category}'>				
				<input type="hidden" id="id" name="id" value="{$request.id}">			
            </form> 
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
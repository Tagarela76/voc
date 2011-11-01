{if $color eq "green"}
    {include file="tpls:tpls/notify/greenNotify.tpl" text=$message}
{/if}
{if $color eq "orange"}
    {include file="tpls:tpls/notify/orangeNotify.tpl" text=$message}
{/if}
{if $color eq "blue"}
    {include file="tpls:tpls/notify/blueNotify.tpl" text=$message}
{/if}

<form enctype="multipart/form-data" method="POST" action="?action=companySetupRequest&category={$request.category}&id={$request.id}">
    {*shadow*}
    <table class="report_issue" cellspacing="0" cellpadding="0" align="center">
        <tr>
            <td valign="top" class="report_issue_top">
            </td>
        </tr>
        <tr>
            <td valign="top" class="report_issue_center" align="center">			
				{if $request.category == 'company'}
					<h2>Facility Setup Request Form</h2>
					<hr width="400px">
					<table width="440px" cellspacing="0" cellpadding="0">
						{if $error neq ''}
						<tr>
							<td colspan="2" align="center">
								<font color="red">{$error}</font>
							</td>
						</tr>
						{/if}
						<tr>
							<td>
								EPA/ID number:<font color="red">&nbsp;<big>*</big></font>
							</td>
							<td>
								<input type="text" name="epa" value="{$setupRequest->getEPANumber()}"/>
							</td>
						</tr>
						<tr>
							<td>
							VOC monthly limit:<font color="red">&nbsp;<big>*</big></font>
							</td>
							<td>
								<input type="text" name="voc_monthly_limit" value="{$setupRequest->getVOCMonthlyLimit()}"/>
							</td>
						</tr>
						<tr>
							<td>
							VOC annual limit:<font color="red">&nbsp;<big>*</big></font>
							</td>
							<td>
								<input type="text" name="voc_annual_limit" value="{$setupRequest->getVOCAnnualLimit()}"/>
							</td>
						</tr>
						<tr>
							<td>
							Facility name:<font color="red">&nbsp;<big>*</big></font>
							</td>
							<td>
								<input type="text" name="facility_name" value="{$setupRequest->getName()}"/>
							</td>
						</tr>
						<tr>
							<td>
							Address:
							</td>
							<td>
								<input type="text" name="address" value="{$setupRequest->getAddress()}"/>
							</td>
						</tr>
						<tr>
							<td>
							City:
							</td>
							<td>
								<input type="text" name="city" value="{$setupRequest->getCity()}"/>
							</td>
						</tr>
						<tr>
							<td>
							County:
							</td>
							<td>
								<input type="text" name="county" value="{$setupRequest->getCounty()}"/>
							</td>
						</tr>
						<tr>
							<td>
							State:
							</td>
							<td id="stateText" style="display: block;">
								{*<div id="stateText" style="display: table-row;">*}
									<input type="text" name="stateText" value="{$setupRequest->getState()}"/>
							</td>
							<td id="stateSelect" style="display: none;">
								{*</div>
								<div id="stateSelect" style="display: none;">*}
									<select name="stateSelect">
										{foreach from=$stateList item=state}
											<option value="{$state.id}">{$state.name}</option>
										{/foreach}	
									</select>
								{*</div>*}
							</td>
						</tr>
						<tr>
							<td>
							Zip/Postal code:
							</td>
							<td>
								<input type="text" name="zip_code" value="{$setupRequest->getZipCode()}"/>
							</td>
						</tr>
						<tr>
							<td>
							Country:
							</td>
							<td>
								<select name="country" onclick="clickCountry(value);">
									{foreach from=$countryList item=country}
										<option value="{$country.country_id}">{$country.name}</option>
									{/foreach}	
								</select>	
							</td>
						</tr>
						<tr>
							<td>
							Phone:
							</td>
							<td>
								<input type="text" name="phone" value="{$setupRequest->getPhone()}"/>
							</td>
						</tr>
						<tr>
							<td>
							Fax:
							</td>
							<td>
								<input type="text" name="fax" value="{$setupRequest->getFax()}"/>
							</td>
						</tr>
						<tr>
							<td>
							Email:<font color="red">&nbsp;<big>*</big></font>
							</td>
							<td>
								<input type="text" name="email" value="{$setupRequest->getEmail()}"/>
							</td>
						</tr>
						<tr>
							<td>
							Contact:
							</td>
							<td>
								<input type="text" name="contact" value="{$setupRequest->getContact()}"/>
							</td>
						</tr>
						<tr>
							<td>
							Title:
							</td>
							<td>
								<input type="text" name="title" value="{$setupRequest->getTitle()}"/>
							</td>
						</tr>
						<tr>
							<td colspan="2" align="right">
								<font color="red">&nbsp;<big>*</big></font>&nbsp;-&nbsp;required
							</td>
						</tr>
					</table>
				{/if}
				{if $request.category == 'facility'}
					<h2>Department Setup Request Form</h2>
					<table width="440px" cellpadding="0" cellspacing="0">
						{if $error neq ''}
						<tr>
							<td colspan="2" align="center">
								<font color="red">{$error}</font>
							</td>
						</tr>
						{/if}
						<tr>
							<td>
							Department Name:<font color="red">&nbsp;<big>*</big></font>
							</td>
							<td>
								<input type="text" name="department_name" value="{$setupRequest->getName()}"/>
							</td>
						</tr>
						<tr>
							<td>
							VOC monthly limit:<font color="red">&nbsp;<big>*</big></font>
							</td>
							<td>
								<input type="text" name="voc_monthly_limit" value="{$setupRequest->getVOCMonthlyLimit()}"/>
							</td>
						</tr>
						<tr>
							<td>
							VOC annual limit:<font color="red">&nbsp;<big>*</big></font>
							</td>
							<td>
								<input type="text" name="voc_annual_limit" value="{$setupRequest->getVOCAnnualLimit()}"/>
							</td>
						</tr>
						<tr>
							<td colspan="2" align="right">
								<font color="red">&nbsp;<big>*</big></font>&nbsp;-&nbsp;required
							</td>
						</tr>	
					</table>
				{/if}	
				<div style="padding-top: 10px;">
					<input name="submitForm" type="submit" class="button" value="Submit"/>
					<input type="button" class="button" value="Cancel" 
							{if $request.category eq 'company'}
							onclick="location.href='?action=browseCategory&category={$request.category}&id={$request.id}'"
							{elseif $request.category eq 'facility'}
							onclick="location.href='?action=browseCategory&category={$request.category}&id={$request.id}&bookmark=department'"
							{/if}/>
				</div>
			</td>
		</tr>
		<tr>
			<td valign="top" class="report_issue_bottom">
			</td>
		</tr>
	</table>
	{**}
</form>
{literal}
<script type="text/javascript">
	function clickCountry(value){
		if (value == 215){
			$('#stateText').hide();
			$('#stateSelect').show();	
		} else {
			$('#stateText').show();
			$('#stateSelect').hide();
		}	
	}
</script>
{/literal}
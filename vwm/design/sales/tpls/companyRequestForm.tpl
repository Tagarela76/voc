{if $color eq "green"}
    {include file="tpls:tpls/notify/greenNotify.tpl" text=$message}
{/if}
{if $color eq "orange"}
    {include file="tpls:tpls/notify/orangeNotify.tpl" text=$message}
{/if}
{if $color eq "blue"}
    {include file="tpls:tpls/notify/blueNotify.tpl" text=$message}
{/if}

<form enctype="multipart/form-data" method="POST" action="sales.php?action=browseCategory&category=forms&bookmark=companyRequest">
    <table class="report_issue" cellspacing="0" cellpadding="0" align="center">
        <tr>
            <td valign="top" class="report_issue_top">
            </td>
        </tr>
        <tr>
            <td valign="top" class="report_issue_center" align="center">
				<h2>Company Setup Request Form</h2>
				<hr width="400px"/>
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
							Company Name:<font color="red">&nbsp;<big>*</big></font>
						</td>
						<td>
							<input type="text" name="name" value="{$setupRequest->getName()}"/>
						</td>
					</tr>
					<tr>
						<td>
							Address:<font color="red">&nbsp;<big>*</big></font>
						</td>
						<td>
							<input type="text" name="address" value="{$setupRequest->getAddress()}"/>
						</td>
					</tr>
					<tr>
						<td>
							City:<font color="red">&nbsp;<big>*</big></font>
						</td>
						<td>
							<input type="text" name="city" value="{$setupRequest->getCity()}"/>
						</td>
					</tr>
					<tr>
						<td>
							Country:<font color="red">&nbsp;<big>*</big></font>
						</td>
						<td>
							<select name="country" onclick="clickCountry(value);">
								{foreach from=$countryList item=country}
									<option value="{$country.country_id}" {if $setupRequest->getCountryID() eq $country.country_id} selected {/if}>{$country.name}</option>
								{/foreach}	
							</select>	
						</td>
					</tr>
					<tr>
						<td>
							State:
						</td>
						<td id="stateText" {if $setupRequest->getCountryID() neq '215'} style="display: block;" {else} style="display: none;" {/if}>
							<input type="text" name="stateText" value="{$setupRequest->getState()}"/>
						</td>
						<td id="stateSelect" {if $setupRequest->getCountryID() eq '215'} style="display: block;" {else} style="display: none;" {/if}>
							<select name="stateSelect">
								{foreach from=$stateList item=state}
									<option value="{$state.id}" {if $setupRequest->getStateID() eq $state.id} selected {/if}>{$state.name}</option>
								{/foreach}	
							</select>
						</td>
					</tr>
					<tr>
						<td>
							Zip/Postal Code:
						</td>
						<td>
							<input type="text" name="zip" value="{$setupRequest->getZipCode()}"/>
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
			<div style="padding-top: 10px;">
				<input name="submitForm" type="submit" class="button" value="Submit"/>
				<input type="button" class="button" value="Cancel" onclick="location.href='sales.php?action=browseCategory&category=dashboard'"/>
			</div>
			</td>
		</tr>
		<tr>
			<td valign="top" class="report_issue_bottom">
			</td>
		</tr>
	</table>
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
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
						<tr>
							<td>
							EPA ID number:
							</td>
							<td>
								<input type="text" name="epa"/>
							</td>
						</tr>
						<tr>
							<td>
							VOC monthly limit:
							</td>
							<td>
								<input type="text" name="voc_limit"/>
							</td>
						</tr>
						<tr>
							<td>
							VOC annual limit:
							</td>
							<td>
								<input type="text" name="voc_annual_limit"/>
							</td>
						</tr>
						<tr>
							<td>
							Facility name:
							</td>
							<td>
								<input type="text" name="name"/>
							</td>
						</tr>
						<tr>
							<td>
							Address:
							</td>
							<td>
								<input type="text" name="adress"/>
							</td>
						</tr>
						<tr>
							<td>
							City:
							</td>
							<td>
								<input type="text" name="city"/>
							</td>
						</tr>
						<tr>
							<td>
							County:
							</td>
							<td>
								<input type="text" name="county"/>
							</td>
						</tr>
						<tr>
							<td>
							State:
							</td>
							<td>
								<select name="state"/>
							</td>
						</tr>
						<tr>
							<td>
							Zip/Postal code:
							</td>
							<td>
								<input type="text" name="zip"/>
							</td>
						</tr>
						<tr>
							<td>
							Country:
							</td>
							<td>
								<select name="country"/>
							</td>
						</tr>
						<tr>
							<td>
							Phone:
							</td>
							<td>
								<input type="text" name="phone"/>
							</td>
						</tr>
						<tr>
							<td>
							Fax:
							</td>
							<td>
								<input type="text" name="fax"/>
							</td>
						</tr>
						<tr>
							<td>
							Email:
							</td>
							<td>
								<input type="text" name="email"/>
							</td>
						</tr>
						<tr>
							<td>
							Contact:
							</td>
							<td>
								<input type="text" name="contact"/>
							</td>
						</tr>
						<tr>
							<td>
							Title:
							</td>
							<td>
								<input type="text" name="title"/>
							</td>
						</tr>
					</table>
				{/if}
				{if $request.category == 'facility'}
					<h2>Department Setup Request Form</h2>
					<table width="440px" cellpadding="0" cellspacing="0">
						<tr>
							<td>
							Department Name:
							</td>
							<td>
								<input type="text" name="name"/>
							</td>
						</tr>
						<tr>
							<td>
							VOC monthly limit:
							</td>
							<td>
								<input type="text" name="voc_limit"/>
							</td>
						</tr>
						<tr>
							<td>
							VOC annual limit:
							</td>
							<td>
								<input type="text" name="voc_annual_limit"/>
							</td>
						</tr>
					</table>
				{/if}	
				<div style="padding-top: 10px;">
					<input type="button" class="button" value="Cancel"/>
					<input type="submit" class="button" value="Submit"/>
				</div>
				{*<input type="hidden" name="action" value="reportIssue">*}
				{*shadow*}
			</td>
		</tr>
		<tr>
			<td valign="top" class="report_issue_bottom">
			</td>
		</tr>
	</table>
	{**}
</form>
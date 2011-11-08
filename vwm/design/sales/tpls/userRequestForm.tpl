{if $color eq "green"}
    {include file="tpls:tpls/notify/greenNotify.tpl" text=$message}
{/if}
{if $color eq "orange"}
    {include file="tpls:tpls/notify/orangeNotify.tpl" text=$message}
{/if}
{if $color eq "blue"}
    {include file="tpls:tpls/notify/blueNotify.tpl" text=$message}
{/if}
<form enctype="multipart/form-data" method="POST" action="sales.php?action=browseCategory&category=forms&bookmark=userRequest">
    <table class="report_issue" cellspacing="0" cellpadding="0" align="center">
        <tr>
            <td valign="top" class="report_issue_top">
            </td>
        </tr>
        <tr>
            <td valign="top" class="report_issue_center" align="center">
                <h2>New User Request Form</h2>
                <hr width="400px">
                <table width="440px" id="myclass">
					{if $error neq ''}
					<tr>
						<td colspan="2" align="center">
							<font color="red">{$error}</font>
						</td>
					</tr>
					{/if}
					<tr>
						<td width="35%">
                            Access Name:<font color="red">&nbsp;<big>*</big></font>
                        </td>
                        <td width="55%">
                            <input type="text" name="new_accessname" value="{$userRequest->getNewAccessName()}"/>
                        </td>
					</tr>
					<tr>
						<td width="35%">
                            New User Name:<font color="red">&nbsp;<big>*</big></font>
                        </td>
                        <td width="55%">
                            <input type="text" name="new_username" value="{$userRequest->getNewUserName()}"/>
                        </td>
					</tr>
					<tr>
						<td width="35%">
                            Email:<font color="red">&nbsp;<big>*</big></font>
                        </td>
                        <td width="55%">
                            <input type="text" name="email" value="{$userRequest->getEmail()}"/>
                        </td>
					</tr>
					<tr>
						<td width="35%">
                            Phone:
                        </td>
                        <td width="55%">
                            <input type="text" name="phone" value="{$userRequest->getPhone()}"/>
                        </td>
					</tr>
					<tr>
						<td width="35%">
                            Mobile:
                        </td>
                        <td width="55%">
                            <input type="text" name="mobile" value="{$userRequest->getMobile()}"/>
                        </td>
					</tr>
					<tr>
						<td width="35%">
							Company Name:<font color="red">&nbsp;<big>*</big></font>
						</td>
                        <td width="55%">
							<input type="text" name="company"/>
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
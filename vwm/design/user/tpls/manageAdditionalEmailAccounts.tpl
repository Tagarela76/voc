<table class="popup_table" align="center" cellspacing="0" cellpadding="0">
	<tr align="right">
		<td>
			<a href="#" onclick="settings.manageAdditionalEmailAccounts.addNewEmailAccount();"> Add </a> 
			{if $additionalEmailAccountsList|@is_array and $additionalEmailAccountsList|@count > 0}/
				<a href="#" onclick="settings.manageAdditionalEmailAccounts.deleteSelectedEmailAccount();"> Delete </a> 
			{/if}
		</td>
	</tr>
	<tr>
		<td>
			<div class="error_img"  id="emailAccountDeleteItemError" style="display:none;"><span class="error_text" >Check at least one item!</span></div>
		</td>
		<td><input type='hidden' name='companyId' id='companyId' value='{$companyId}'/></td>
	</tr>
<tr>
	<td>
		<div id="userAccountListContainer">
			<table align="left" cellspacing="0" cellpadding="0">
				<tr>
					<td class="control_list" colspan="3" style="border-bottom:0px solid #fff;padding-left:0px">
						Select: <a onclick="CheckAll(this)" name="allEmailAccountsList" class="id_company1">All</a>
						/<a onclick="unCheckAll(this)" name="allEmailAccountsList" class="id_company1">None</a>
					</td>
				</tr>
				<tr class="table_popup_rule">
					<td align="center" width="10%">
						Select
					</td>
					<td>
						Name
					</td>
					<td>
						Email
					</td>
				</tr>
				{if $additionalEmailAccountsList|@is_array and $additionalEmailAccountsList|@count > 0}
				{foreach from=$additionalEmailAccountsList item="emailAccount"}
					<tr>
						<td>
							<input type="checkbox" name="emailAccountUserId" id="emailAccountUserId" value="{$emailAccount->id}" />
						</td>
						<td>
							{$emailAccount->username}
						</td>
						<td>
							{$emailAccount->email}
						</td>
					</tr>
				{/foreach}
				{/if}
					<tr>
						<td></td>
						<td><input type='text' name='emailAccountUserName' id='emailAccountUserName' value=''/></td>
						<td><input type='text' name='emailAccountUserEmail' id='emailAccountUserEmail' value=''/></td>
					</tr>
					<tr>
						<td>
							
						</td>
						<td>
							<div class="error_img"  id="emailAccountUserIdError" style="display:none;"><span class="error_text" >Email is already in use!</span></div>
							<div class="error_img"  id="emailAccountUserNameError" style="display:none;"><span class="error_text" >Fill name!</span></div>
						</td>
						<td>
							<div class="error_img"  id="emailAccountUserEmailError" style="display:none;"><span class="error_text" >Fill email!!</span></div>
						</td>
					</tr>
			</table>
		</div>
	</td>
</tr>
</table>
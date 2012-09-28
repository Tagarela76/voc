<table class="popup_table" align="left" cellspacing="0" cellpadding="0">
<tr>
	<td>
		<div id="usersListContainer">
			<table align="left" cellspacing="0" cellpadding="0">
				<tr>
					<td class="control_list" colspan="3" style="border-bottom:0px solid #fff;padding-left:0px">
						Select: <a onclick="CheckAll(this)" name="allUsersList" class="id_company1">All</a>
						/<a onclick="unCheckAll(this)" name="allUsersList" class="id_company1">None</a>
					</td>
				</tr>
				<tr class="table_popup_rule">
					<td align="center" width="10%">
						Select
					</td>
					<td>
						Name
					</td>
				</tr>
				{if $usersList|@is_array and $usersList|@count > 0}
				{foreach from=$usersList item="user"}
					<tr>
						<td>
							<input type="checkbox" name="userId" id="userId" value="{$user.id}" {if $user.check} CHECKED {/if}/>
						</td>
						<td>
							{$user.name}
						</td>
					</tr>
                    <tr>
                        <td> <input type="hidden" id="remindId" name="remindid" value="{$remindId}" /> </td>
                    </tr>    
				{/foreach}
				{/if}
			</table>
		</div>
	</td>
</tr>
</table>
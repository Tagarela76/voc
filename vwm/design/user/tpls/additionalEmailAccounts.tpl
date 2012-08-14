<table class="popup_table" align="center" width="750px" cellspacing="0" cellpadding="0">
	<tr>
		<td class="control_list" style="border-bottom:0px solid #fff;padding-left:0px" colspan="3">
			<select id="departmentSwitcher" onchange="settings.managePermissions.showDepartament(this);">
				{foreach from=$departments item="department"}
					<option value="{$department.id}">{$department.name|escape}</option>
				{/foreach}
			</select>
		</td>
	</tr>
	<tr  class="table_popup_rule">
		<td>
		{php} echo VOCAPP::t('general', 'All Users'){/php}
	</td>
	<td>
	{php} echo VOCAPP::t('general', 'Controls'){/php}
</td>
<td>
{php} echo VOCAPP::t('general', 'Department Users'){/php}
</td>
</tr>
<tr>
	<td>
		<select multiple="multiple" id="allUsers">
			{foreach from=$allUsers item="user"}
				{if $user.accesslevel_id == 2}
					<option value="{$user.user_id}">{$user.username}</option>
				{/if}
			{/foreach}
		</select>
	</td>
	<td colspan="2">
		{foreach from=$departments item="department"}
			<div id="departmentPermissions_{$department.id}" class="departmentPermissions" style="display:none;">
				<div style="vertical-align: center;float:left;margin-right: 70px;">
					<input type="button" class="button" value=">>" onclick="settings.managePermissions.moveFromAllToAssigned();"/><br/>
					<input type="button" class="button" value="<<" onclick="settings.managePermissions.moveFromAssignedToAll();"/>
				</div>
				<div>
					<select multiple="multiple" id="departmentUsers_{$department.id}">
						{assign var="departmentId" value=$department.id}
						{foreach from=$departmentUsers.$departmentId item="user"}
							<option value="{$user.user_id}">{$user.username}</option>
						{/foreach}
					</select>
				</div>
			</div>

		{/foreach}
	</td>
</tr>
</table>
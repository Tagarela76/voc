<table class="popup_table" align="left" cellspacing="0" cellpadding="0">
<tr>
	<td>
		<div id="woDepartmentsDiv" style="width: 230px;">
			<table align="left" cellspacing="0" cellpadding="0">
				<tr>
					<td class="control_list" colspan="3" style="border-bottom:0px solid #fff;padding-left:0px">
						Select: <a onclick="CheckAll(this)" name="allUsersList" class="id_company1">All</a>
						/<a onclick="unCheckAll(this)" name="allUsersList" class="id_company1">None</a>
					</td>
				</tr>
				<tr class="table_popup_rule">
					<td align="left" width="10%">
						Select
					</td>
					<td>
						Name
					</td>
				</tr>
				{foreach from=$departmentsDeafult key="depDefaulId" item="depDefaultName"}
					<tr>
						<td>
							<input type="checkbox" name="woDepartmentId" id="woDepartmentId" value="{$depDefaulId}" CHECKED/>
						</td>
						<td>
							{$depDefaultName}
						</td>
					</tr>
				{/foreach}
			</table>
		</div>
	</td>
</tr>
</table>
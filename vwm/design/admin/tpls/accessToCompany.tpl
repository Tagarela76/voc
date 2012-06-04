
	{if $color eq "green"}
		{include file="tpls:tpls/notify/greenNotify.tpl" text=$message}
	{/if}
	{if $color eq "orange"}
		{include file="tpls:tpls/notify/orangeNotify.tpl" text=$message}
	{/if}
	{if $color eq "blue"}
		{include file="tpls:tpls/notify/blueNotify.tpl" text=$message}
	{/if}
	{if $color eq "blue2" && $itemsCount == 0}
		{include file="tpls:tpls/notify/blueNotify.tpl" text=$message}
	{/if}
	
	
<div class="padd7">
<form method="POST" action="admin.php?action=accessToCompany&category=pfpLibrary&bookmark=pfps">
	<table  class="users" height="140"  cellspacing="0" cellpadding="0" align="center">
		<tr height="27" class="users_top_violet">
			<td width="35%" class="users_u_top_violet">Industry Type</td>
			<td width="55%">Company</td>
			<td width="10%" class="users_u_top_r_violet">&nbsp;</td>
		</tr>
		<tr height="100px" class="hov_company">
			<td  class="border_users_l border_users_r border_users_b">
				<select class="addInventory" name="industryType">
					{* TYPES AND SUB-TYPES 
					<optgroup label="All">
						<option value="0">All</option>
					</optgroup>

					{foreach from=$typesList item='type' key="name"}
						<optgroup label="{$name}">
							<option value="{$type.id}">{$name}</option>
							{foreach from=$type.subTypes item='subType' key="id"}
								<option value="{$id}">{$name} - {$subType}</option>
							{/foreach}
						</optgroup>
					{/foreach}
					*}
					
					{*ONLY TYPES*}
					
					{foreach from=$typesList item='type' key="name"}
						<option value="{$type.id}">{$name}</option>
					{/foreach}
				</select>
			</td>
			<td class="border_users_r border_users_b">
				<select class="addInventory" multiple="multiple" name="company[]" size="6">
					{foreach from=$companyList item='company'}
						<option value="{$company.id}">{$company.name}</option>
					{/foreach}
				</select>
			</td>
			<td class="border_users_r border_users_b" style="text-align: center;">
				<input type="submit" class="button" value="Assign" name="assign"/>
				<br/><br/>
				<input type="submit" class="button" value="Unassign" name="unassign"/>
			</td>
		</tr>
		<tr>
			<td class="border_users_l border_users_r" colspan="3">&nbsp;
				{*<br/>
				{$log}
				<br/>
				<input type="button" class="button" value="To PFP library" onclick="location.href='admin.php?action=browseCategory&category=pfps&bookmark=pfpLibrary'"/>*}
			</td>
		</tr>
		<tr>
			<td class="users_u_bottom">&nbsp;</td>
			<td colspan="2" class="users_u_bottom_r">&nbsp;</td>
		</tr>
	</table>
</form>
</div>
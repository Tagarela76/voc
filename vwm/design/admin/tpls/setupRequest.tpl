<div class="padd7" align="center">
<table class="users" width="100%" cellspacing="0" cellpadding="0" bgcolor="#EFEFEF">
	<tr class="users_u_top_size users_top_blue">
		<td class="users_u_top_blue" width="5%">
			<span style="display:inline-block; width:60px;">
			<a style="color:white" onclick="CheckAll(this)">All</a>
				/
			<a onclick="unCheckAll(this)" style="color:white">None</a>
			</span>
		</td>
		<td>Facility Name</td>
		<td>Company</td>
		<td>EPA ID Number</td>
		<td>VOC Monthly Limit</td>
		<td>VOC Annual Limit</td>
		<td>Additional Information</td>
		<td>Request Date</td>
		<td>Creator User</td>
        <td class="users_u_top_r_blue">Status</td>
	</tr>
	{if $setupRequest.facility|@count gt 0}
	{foreach from=$setupRequest.facility item=request}
	<tr>
		<td class="border_users_l border_users_b"><input type="checkbox" name="setupRequestFacilityID[]" value=""/></td>
		<td class="border_users_l border_users_b"><div style="width:100%;">{$request->name}</div></td>
		<td class="border_users_l border_users_b"><div style="width:100%;">{$request->parent_name}</div></td>
		<td class="border_users_l border_users_b"><div style="width:100%;">{$request->epa}</div></td>
		<td class="border_users_l border_users_b"><div style="width:100%;">{$request->voc_monthly_limit}</div></td>
		<td class="border_users_l border_users_b"><div style="width:100%;">{$request->voc_annual_limit}</div></td>
		<td class="border_users_l border_users_b"><div style="width:100%;"><a>View Information</a></div></td>
		<td class="border_users_l border_users_b"><div style="width:100%;">{$request->date}</div></td>
		<td class="border_users_l border_users_b"><div style="width:100%;">{$request->creator_name}</div></td>
        <td class="border_users_l border_users_b border_users_r"><div style="width:100%;">{$request->status}</div></td>
	</tr>
	{/foreach}
	{else}
	<tr>
		<td colspan="10" align="center">
			No requests to add new facility
		</td>
	</tr>	
	{/if}	
	<tr>
		<td colspan="5" height="15" class="users_u_bottom">
		</td>
		<td colspan="5" height="15" class="users_u_bottom_r">
		</td>
	</tr>
</table>
<br/>	
<table class="users" width="100%" cellspacing="0" cellpadding="0" bgcolor="#EFEFEF">
	<tr class="users_u_top_size users_top_blue">
		<td class="users_u_top_blue" width="5%">
			<span style="display:inline-block; width:60px;">
			<a style="color:white" onclick="CheckAll(this)">All</a>
				/
			<a onclick="unCheckAll(this)" style="color:white">None</a>
			</span>
		</td>
		<td>Department Name</td>
		<td>Facility</td>
		<td>VOC Monthly Limit</td>
		<td>VOC Annual Limit</td>
		<td>Request Date</td>
		<td>Creator User</td>
        <td class="users_u_top_r_blue">Status</td>
	</tr>
	{if $setupRequest.department|@count gt 0}
	{foreach from=$setupRequest.department item=request}
	<tr>
		<td class="border_users_l border_users_b"><input type="checkbox" name="setupRequestDepartmentID[]" value=""/></td>
		<td class="border_users_l border_users_b"><div style="width:100%;">{$request->name}</div></td>
		<td class="border_users_l border_users_b"><div style="width:100%;">{$request->parent_name}</div></td>
		<td class="border_users_l border_users_b"><div style="width:100%;">{$request->voc_monthly_limit}</div></td>
		<td class="border_users_l border_users_b"><div style="width:100%;">{$request->voc_annual_limit}</div></td>
		<td class="border_users_l border_users_b"><div style="width:100%;">{$request->date}</div></td>
		<td class="border_users_l border_users_b"><div style="width:100%;">{$request->creator_name}</div></td>
        <td class="border_users_l border_users_b border_users_r"><div style="width:100%;">{$request->status}</div></td>
	</tr>
	{/foreach}
	{else}
	<tr>
		<td colspan="8" align="center">
			No requests to add new department
		</td>
	</tr>	
	{/if}
	<tr>
		<td colspan="4" height="15" class="users_u_bottom">
		</td>
		<td colspan="4" height="15" class="users_u_bottom_r">
		</td>
	</tr>
</table>
</div>
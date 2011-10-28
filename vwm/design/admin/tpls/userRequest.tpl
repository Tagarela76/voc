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
		<td>Action</td>
		<td>User Name</td>
		<td>Access Level</td>
		<td>Title</td>
		<td>Request Date</td>
		<td>Creator User</td>
        <td class="users_u_top_r_blue">Status</td>
	</tr>
	{if $requests.add|@count gt 0}
	{foreach from=$requests.add item=request}
	<tr class="hov_company">
		<td class="border_users_l border_users_b"><input type="checkbox" name="userRequestAddID[]" value=""/></td>
		<td class="border_users_l border_users_b"><a href="{$request->url}"><div style="width:100%;">{$request->action}</div></a></td>
		<td class="border_users_l border_users_b"><a href="{$request->url}"><div style="width:100%;">{$request->new_username}</div></a></td>
		<td class="border_users_l border_users_b"><a href="{$request->url}"><div style="width:100%;">{$request->category_type}</div></a></td>
		<td class="border_users_l border_users_b"><a href="{$request->url}"><div style="width:100%;">{$request->title}</div></a></td>
		<td class="border_users_l border_users_b"><div style="width:100%;">{$request->date}</div></td>
		<td class="border_users_l border_users_b"><div style="width:100%;">{$request->creator_user}</div></td>
        <td class="border_users_l border_users_b border_users_r"><div style="width:100%;"><a href="{$request->url}">{$request->status}</a></div></td>
	</tr>
	{/foreach}
	{else}
	<tr>
		<td colspan="8" align="center" class="border_users_l border_users_r">
			No requests to add new user
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
		<td>Action</td>
		<td>User Name</td>
		<td>Request Date</td>
		<td>Creator User</td>
        <td class="users_u_top_r_blue">Status</td>
	</tr>
	{if $requests.delete|@count gt 0}
	{foreach from=$requests.delete item=request}
	<tr class="hov_company">
		<td class="border_users_l border_users_b"><input type="checkbox" name="userRequestDeleteID[]" value=""/></td>
		<td class="border_users_l border_users_b"><a href="{$request->url}"><div style="width:100%;">{$request->action}</div></a></td>
		<td class="border_users_l border_users_b"><a href="{$request->url}"><div style="width:100%;">{$request->username}</div></a></td>
		<td class="border_users_l border_users_b"><div style="width:100%;">{$request->date}</div></td>
		<td class="border_users_l border_users_b"><div style="width:100%;">{$request->creator_user}</div></td>
        <td class="border_users_l border_users_b border_users_r"><div style="width:100%;"><a href="{$request->url}">{$request->status}</a></div></td>
	</tr>
	{/foreach}
	{else}
	<tr>
		<td colspan="8" align="center" class="border_users_l border_users_r">
			No requests to delete user
		</td>
	</tr>	
	{/if}
	<tr>
		<td colspan="3" height="15" class="users_u_bottom">
		</td>
		<td colspan="3" height="15" class="users_u_bottom_r">
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
		<td>Action</td>
		<td>User Name</td>
		<td>New User Name</td>
		<td>Request Date</td>
		<td>Creator User</td>
        <td class="users_u_top_r_blue">Status</td>
	</tr>
	{if $requests.change|@count gt 0}
	{foreach from=$requests.change item=request}
	<tr class="hov_company">
		<td class="border_users_l border_users_b"><input type="checkbox" name="userRequestChangeID[]" value=""/></td>
		<td class="border_users_l border_users_b"><a href="{$request->url}"><div style="width:100%;">{$request->action}</div></a></td>
		<td class="border_users_l border_users_b"><a href="{$request->url}"><div style="width:100%;">{$request->username}</div></a></td>
		<td class="border_users_l border_users_b"><a href="{$request->url}"><div style="width:100%;">{$request->new_username}</div></a></td>
		<td class="border_users_l border_users_b"><div style="width:100%;">{$request->date}</div></td>
		<td class="border_users_l border_users_b"><div style="width:100%;">{$request->creator_user}</div></td>
        <td class="border_users_l border_users_b border_users_r"><div style="width:100%;"><a href="{$request->url}">{$request->status}</a></div></td>
	</tr>
	{/foreach}
	{else}
	<tr>
		<td colspan="8" align="center" class="border_users_l border_users_r">
			No requests to change user
		</td>
	</tr>	
	{/if}
	<tr>
		<td colspan="3" height="15" class="users_u_bottom">
		</td>
		<td colspan="4" height="15" class="users_u_bottom_r">
		</td>
	</tr>
</table>	
</div>
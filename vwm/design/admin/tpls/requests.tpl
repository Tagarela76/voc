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
		<td>Product ID</td>
		<td>Supplier</td>
		<td>Description</td>
		<td>Request Date</td>
		<td class="users_u_top_r_blue">User</td>
	</tr>
	{foreach from=$productRequests item=productRequest}
	<tr>
		{assign var='requestDate' value=$productRequest->getDate()}
		<td class="border_users_l border_users_b"><input type="checkbox" name="productRequestID[]" value=""/></td>
		<td class="border_users_l border_users_b"><div style="width:100%;">{$productRequest->getProductID()}</div></td>
		<td class="border_users_l border_users_b"><div style="width:100%;">{$productRequest->getSupplier()}</div></td>
		<td class="border_users_l border_users_b"><div style="width:100%;">{$productRequest->getDescription()}</div></td>
		<td class="border_users_l border_users_b"><div style="width:100%;">{$requestDate->format($smarty.const.DEFAULT_DATE_FORMAT)}</div></td>
		<td class="border_users_l border_users_b border_users_r"><div style="width:100%;">{$productRequest->getUserID()}</div></td>
	</tr>
	{/foreach}
</table>
</div>
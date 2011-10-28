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
		<td>User Name</td>
		<td>MSDS</td>
		<td class="users_u_top_r_blue">Status</td>
	</tr>
	{if $productRequests|@count gt 0}
	{foreach from=$productRequests item=productRequest}
	<tr class="hov_company">
		{assign var='requestDate' value=$productRequest->getDate()}
		<td class="border_users_l border_users_b"><input type="checkbox" value="{$productRequest->requestID}" name="item_{$productRequest->requestID}"/></td>
		<td class="border_users_l border_users_b"><a href="{$productRequest->getURL()}"><div style="width:100%;">{$productRequest->getProductID()}</div></a></td>
		<td class="border_users_l border_users_b"><a href="{$productRequest->getURL()}"><div style="width:100%;">{$productRequest->getSupplier()}</div></a></td>
		<td class="border_users_l border_users_b"><a href="{$productRequest->getURL()}"><div style="width:100%;">{$productRequest->getDescription()}</div></a></td>
		<td class="border_users_l border_users_b"><div style="width:100%;">{$requestDate->format($smarty.const.DEFAULT_DATE_FORMAT)}</div></td>
		<td class="border_users_l border_users_b"><div style="width:100%;">{$productRequest->getUserName()}</div></td>
        <td class="border_users_l border_users_b"><div style="width:100%;">{if $productRequest->getMsdsName() <> NULL}<a href="{$productRequest->getMsdsName()}">VIEW</a>{/if}</div></td>
		<td class="border_users_l border_users_b border_users_r"><div style="width:100%;"><a href="{$productRequest->getURL()}">{$productRequest->getStatus()}</a></div></td>
	</tr>
	{/foreach}
	{else}
	<tr>
		<td colspan="8" align="center" class="border_users_l border_users_r">
			No product requests
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
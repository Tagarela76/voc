<div style="padding:7px;">

{if $changeInvoiceStatus=="yes"}<form action="admin.php?action=vps&vpsAction=manageInvoice&invoiceAction=confirmEdit" method="post">
							{else}<form action="admin.php?action=vps&vpsAction=manageInvoice&invoiceAction=confirmAdd" method="post">
							{/if}

		<table class="users" align="center" cellpadding="0" cellspacing="0">
			<tr class="users_top_yellowgreen" >
				<td class="users_u_top_red" height="27">
					<span >
					{*--header-constructor--*}
						{if $changeInvoiceStatus=="yes"} Are you sure you want to change status to following invoices?
						{else} Are you sure you want to generete new {$invoiceType} invoice for customer <b>{$customerDetails.name}</b>?
						{/if}																				
					{*----------------------*}
					</span>
				</td>
				<td class="users_u_top_r_red">
				</td>
			</tr>
			
{if $changeInvoiceStatus == "yes"}
				{section name=i loop=$invoices}
					<tr height="20" class="hov_company">
						<td class="border_users_l border_users_b border_users_r" colspan="2">
							Change invoice {$invoices[i].invoiceID} status to <b>{$invoices[i].status}</b> {if $invoices[i].status == "DUE"} with due amount $ {$invoices[i].due}{/if}
							<input type="hidden" name="invoiceID_{$smarty.section.i.index}" value="{$invoices[i].invoiceID}">
							<input type="hidden" name="statusID_{$smarty.section.i.index}" value="{$invoices[i].statusID}">
							<input type="hidden" name="paymentMethodID_{$smarty.section.i.index}" value="{$invoices[i].paymentMethodID}">
							<input type="hidden" name="note_{$smarty.section.i.index}" value="{$invoices[i].note}">
							<input type="hidden" name="due_{$smarty.section.i.index}" value="{$invoices[i].due}"> 
						</td>												
					</tr>
				{/section}
					<tr>
						<td  height="25" class="users_u_bottom">							
						</td>
						<td height="25" class="users_u_bottom_r">							
						</td>
					</tr>															
		</table>
		<br>
		<table width="100%">
			<tr>
				<td width="80%">
				</td>
				<td>
					<input type="submit" class="button" value="Yes">					
					<input type="hidden" name="customerID" value="{$customerDetails.company_id}">					
				</td>
				<td>
					<input type="button" class="button" value="No" onClick="location.href='admin.php?action=vps&vpsAction=viewDetails&itemID=customer&customerID={$customerDetails.company_id}'">
				</td>
			</tr>
{else}
	{if $invoiceType == 'custom'}											
					<tr height="20" class="hov_company">
						<td class="border_users_l border_users_b border_users_r">
							Item (reason why you generate invoice)
						</td>						
						<td class="border_users_r border_users_b">
							{$customInfo}
							<input type="hidden" name="customInfo" value="{$customInfo}">							
						</td>
					</tr>
					<tr height="20" class="hov_company">
						<td class="border_users_l border_users_b border_users_r">
							Amount 
						</td>						
						<td class="border_users_r border_users_b">
							$ {$amount}
							<input type="hidden" name="amount" value="{$amount}">							
						</td>
					</tr>
					<tr height="20" class="hov_company">
						<td class="border_users_l border_users_b border_users_r">
							Suspansion Date 
						</td>						
						<td class="border_users_r border_users_b">
							{$suspensionDate}
							<input type="hidden" name="suspensionDate" value="{$suspensionDate}">							
						</td>
					</tr>
					<tr height="20" class="hov_company">
						<td class="border_users_l border_users_b border_users_r">
							Deactivate customer after suspension date 
						</td>						
						<td class="border_users_r border_users_b">
							{if $suspensionDisable == 0} no {elseif $suspensionDisable == 1} yes{/if}
							<input type="hidden" name="suspensionDisable" value="{$suspensionDisable}">							
						</td>
					</tr>
	{elseif $invoiceType == 'limit'}
					<tr height="20" class="hov_company">
						<td class="border_users_l border_users_b border_users_r">
							Limit
						</td>						
						<td class="border_users_r border_users_b">
							{$limitDetails.name} ({$limitDetails.unit_type})
							<input type="hidden" name="limitName" value="{$limitDetails.name}">							
						</td>
					</tr>
					<tr height="20" class="hov_company">
						<td class="border_users_l border_users_b border_users_r">
							Increase Limit Value 
						</td>						
						<td class="border_users_r border_users_b">
							+ {$plusToValue}
							<input type="hidden" name="plusToValue" value="{$plusToValue}">							
						</td>
					</tr>
	{elseif $invoiceType == 'module'}
					<tr height="20" class="hov_company">
						<td class="border_users_l border_users_b border_users_r">
							Module
						</td>						
						<td class="border_users_r border_users_b">
							{$module_name}
							<input type="hidden" name="module_name" value="{$module_name}">							
						</td>
					</tr>
					<tr height="20" class="hov_company">
						<td class="border_users_l border_users_b border_users_r">
							Type
						</td>						
						<td class="border_users_r border_users_b">
							{$bp_type}
							<input type="hidden" name="bp_type" value="{$bp_type}">											
						</td>
					</tr>
					<tr height="20" class="hov_company">
						<td class="border_users_l border_users_b border_users_r">
							Period
						</td>						
						<td class="border_users_r border_users_b">
							{$period}
							<input type="hidden" name="period" value="{$period}">							
						</td>
					</tr>
					<tr height="20" class="hov_company">
						<td class="border_users_l border_users_b border_users_r">
							Start Date
						</td>						
						<td class="border_users_r border_users_b">
							{$startDate}
							<input type="hidden" name="startDate" value="{$startDate}">							
						</td>
					</tr>
	{/if}
					
					<tr>
						<td  height="25" class="users_u_bottom">							
						</td>
						<td height="25" class="users_u_bottom_r">							
						</td>
					</tr>															
		</table>
		<br>
		<table width="100%">
			<tr>
				<td width="80%">
				</td>
				<td>
					<input type="submit" class="button" value="Yes">
					<input type="hidden" name="invoiceType" value="{$invoiceType}">
					<input type="hidden" name="customerID" value="{$customerDetails.company_id}">					
				</td>
				<td>
					<input type="button" class="button" value="No" onClick="location.href='admin.php?action=vps&vpsAction=viewDetails&itemID=customer&customerID={$customerDetails.company_id}'">
				</td>
			</tr>
{/if}
		</table>								
</form>		

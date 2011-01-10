{if $problem.conflict} {$problem.conflict} {/if}
{*----------------CUSTOMER_INVOICES---------------------------*}
<form name="invoice" action="admin.php?action=vps&vpsAction=manageInvoice&invoiceAction=areYouSureEdit" method="POST">
	
<table align="center" border="1" width="80%">	
	<tr>
		<td colspan="13">Edit Invoices</td>
	</tr>	
	<tr>		
		<td>InvNum</td>
		<td>Setup Charge</td>
		<td>Amount</td>
		<td>Discount</td>
		<td>Total</td>					
		<td>Total Paid</td>
		<td>Total Due</td>
		<td>Date Created</td>
		<td>Start Date</td>
		<td>Finish Date</td>
		<td>Suspension Date</td>
		<td>Status</td>
		<td>Payment Note</td>
	</tr>
	{section name=i loop=$invoices}
			<tr height="20" class="hov_company_vps">				
				<td class="border_users_l border_users_b border_users_r" >
					<div align="left">&nbsp;{$invoices[i].invoiceID}</div>
					<input type="hidden" name="invoiceID_{$smarty.section.i.index}" value="{$invoices[i].invoiceID}">
				</td>
				<td class="border_users_r border_users_b">
					<div align="left">&nbsp;${$invoices[i].oneTimeCharge}</div>
				</td>
				<td class="border_users_r border_users_b">
					<div align="left">&nbsp;${$invoices[i].amount}</div>
				</td>
				<td class="border_users_r border_users_b">
					<div align="left">&nbsp;${$invoices[i].discount}</div>
				</td>
				<td class="border_users_r border_users_b">
					<div align="left">&nbsp;${$invoices[i].total}</div>
				</td>
				<td class="border_users_r border_users_b">
					<div align="left">$ {$invoices[i].paid}</div>
				</td>
				<td class="border_users_r border_users_b">
					<div align="left">$ <input type="text" id="due_{$smarty.section.i.index}" name="due_{$smarty.section.i.index}" value="{$invoices[i].due}" {if $invoices[i].status != "DUE"} disabled {/if}>{if $problem[i].due}fail{/if}</div>
				</td>
				<td class="border_users_r border_users_b">
					<div align="left" >&nbsp;{$invoices[i].generationDate}</div>
				</td>
				{if $invoices[i].limitInfo != null}
					<td class="border_users_r border_users_b" colspan="2">				
						<div align="center" >[Invoice for extra limit]</div>
					</td>
				{elseif $invoices[i].customInfo != null}
					<td class="border_users_r border_users_b" colspan="2">				
						<div align="center" >{$invoices[i].customInfo}[Custom Invoice]</div>
					</td>
				{else}
					<td class="border_users_r border_users_b">				
						<div align="left" >&nbsp;{$invoices[i].periodStartDate}</div>
					</td>
					<td class="border_users_r border_users_b">
						<div align="left" >&nbsp;{$invoices[i].periodEndDate}</div>
					</td>
				{/if}
				<td class="border_users_r border_users_b">
					<div align="left" >&nbsp;{$invoices[i].suspensionDate}</div>
				</td>
				<td class="border_users_r border_users_b">
					<select name="status_{$smarty.section.i.index}" onchange="enableDueInput(this);">
						{*{section name=j loop=$invoiceStatusList}
							{if !($invoices[i].billingInfo != null && $invoiceStatusList[j].status == 'CANCELED')}								
								<option value="{$smarty.section.j.index}" style="{$invoiceStatusList[j].style}"
									{if $invoices[i].status == 'PAID'} 
										{if $invoices[i].lastPayment.paymentMethodID == $invoiceStatusList[j].paymentMethodID} selected{/if}
									{else}
										{if $invoices[i].status == $invoiceStatusList[j].status} selected{/if}
									{/if}	
								>{$invoiceStatusList[j].label}</option>
																	
							{/if}
						{/section}*}
						
						{section name=j loop=$invoices[i].invoiceStatusList}
							<option value="{$smarty.section.j.index}" style="{$invoices[i].invoiceStatusList[j].style}"
								{if $invoices[i].status == 'PAID'} 
									{if $invoices[i].invoiceStatusList[j].paymentMethodID == $invoices[i].invoiceStatusList[j].paymentMethodID} selected{/if}
									{else}
										{if $invoices[i].status == $invoices[i].invoiceStatusList[j].status} selected{/if}
								{/if}	
							>{$invoices[i].invoiceStatusList[j].label}</option>
						{/section}				
						
					</select>										
				</td>
				
				<td class="border_users_r border_users_b">
					<input type="text" name="note_{$smarty.section.i.index}"/>
				</td>
	{/section}
	<tr>
		<td colspan="13"><input type="submit" class="button" value="Save"><input type="button" class="button" value="Cancel" onClick="location.href='admin.php?action=vps&vpsAction=viewDetails&itemID=customer&customerID={$customerID}'"></td>		
	</tr>
</table>

<input type="hidden" name="customerID" value="{$customerID}">
</form>

{literal}
<script>
	function enableDueInput(selectElement) {
		var re = /_(\d+)$/; 
		var match = selectElement.name.match(re);
		if (selectElement.value == "0") {	//	0-DUE		
			document.getElementById("due_"+match[1]).disabled = false;
		} else {
			document.getElementById("due_"+match[1]).disabled = true;
		}		
	}
</script>
{/literal}
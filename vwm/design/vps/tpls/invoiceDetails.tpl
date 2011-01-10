{*notifications*}
	{if $success }
		{include file="tpls:../user/tpls/notify/greenNotify.tpl" text=$success}
	{elseif $canceled }
		{include file="tpls:../user/tpls/notify/orangeNotify.tpl" text=$canceled}		
	{/if}
{**}
<h1 class="logininfo" align="center">INVOICE</h1>

<!-- DEPRECATED
<table width="95%" align="center" cellpadding="0" cellspacing="0" class="invoiceDetals">
	<tr>
		<td colspan="3" >
			<div align="right" {if $invoiceDetails.status eq "DUE"} class="invoiceDetals_due" {else} class="invoiceDetals_paid"{/if}><span>{$invoiceDetails.status}</span></div> {* PAID=green DUE=red*}
		</td>
	<tr>
	<tr >
		<td width="70%">
			<div class="invoiceDetals_logo"><b>G</b>yant Compliance</div>
		</td>
		<td>
			Date:
		</td>
		<td>
			{$invoiceDetails.generationDate}
		</td>						
	</tr>
	<tr>
		<td>
			23974 Aliso Creek Road, Suite 280
		</td>
		<td>
			Invoice ID:
		</td>
		<td>
			{$invoiceDetails.invoiceID}
		</td>
	</tr>
	<tr>
		<td>
			Laguna Niguel, California, 92677
		</td>
		<td>
			Customer ID:
		</td>
		<td>
			{$invoiceDetails.customerID}
		</td>
	</tr>
	<tr>
		<td colspan="3">
			Phone: 949 495-0999
		</td>
	</tr>
	<tr>
		<td colspan="3">
			Fax: (714) 379-8894
		</td>
	</tr>
</table>
END DEPRECATED
-->

<table width="95%" align="center" cellpadding="0" cellspacing="0" class="invoiceDetals">
	<tr>
		<td colspan="2">
			<div align="right" {if $invoiceDetails.status eq "DUE"} class="invoiceDetals_due" {else} class="invoiceDetals_paid"{/if}><span>{$invoiceDetails.status}</span></div> {* PAID=green DUE=red*}
		</td>
	</tr>
	<tr>
		<td width="70%"> <!-- Left Block -->
			<div class="invoiceDetals_logo"><b>G</b>yant Compliance</div>
			23974 Aliso Creek Road, Suite 280<br/>
			Laguna Niguel, California, 92677<br/>
			Phone: 949 495-0999<br/>
			Fax: (714) 379-8894
		</td>
		<td> <!-- Right Block -->
			<table class="invoiceDetals" style='width:100%;'>
				<tr>
					<td style='width:50%;'>
						Date:
					</td>
					<td>
						{$invoiceDetails.generationDate}
					</td>
				</tr>
				<tr>	
					<td>
						Invoice ID:
					</td>
					<td>
						{$invoiceDetails.invoiceID}
					</td>
				</tr>
				<tr>	
					<td>
						Billing Period Date Start:
					</td>
					<td>
						{$invoiceDetails.periodStartDate}
					</td>
					<td>
				</tr>
				<tr>		
					<td>
						Billing Period Date Finish:
					</td>
					<td>
						{$invoiceDetails.periodEndDate}
					</td>
					
				</tr>
				<tr>
					<td>	
						Suspension date:	
					</td>
					<td>
						<b>{$invoiceDetails.suspensionDate}</b>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>

<br>
{* bill to *}
<table width="95%" align="center" cellpadding="0" cellspacing="0"  class="invoiceDetals_table">
	<tr>
		<td>
			<div>Bill To:</div>
		</td>
	</tr>
	<tr>
		<td>
			{$invoiceDetails.customerDetails.name}
		</td>
	</tr>
	<tr>
		<td>
			{$invoiceDetails.customerDetails.address}
		</td>
	</tr>
	<tr>
		<td>
			{$invoiceDetails.customerDetails.city}, {$invoiceDetails.customerDetails.state}, {$invoiceDetails.customerDetails.zip}  
		</td>
	</tr>
	<tr>
		<td>
			Phone: {$invoiceDetails.customerDetails.phone}  
		</td>
	</tr>
</table>

<br>
{* invoice body *}
<table width="95%" align="center" cellpadding="0" cellspacing="0"  class="invoiceDetals">
	<tr class="invoiceDetals_table">
		<td>#</td>
		<td>Setup Charge</td>
		<td>Amount</td>
		<td>Info</td>
		<td>Total</td>
	</tr>
	{foreach from=$invoiceDetails.invoice_items  item=invoiceItem}
	<tr>
		<td>{$invoiceItem.invoiceItemID}</td>
		
		<td>{$currentCurrency.sign} {$invoiceItem.oneTimeCharge}</td>
		<td>{$currentCurrency.sign} {$invoiceItem.amount}</td>
		<td>
			{if $invoiceItem.billingInfo != NULL}
				<b>Billing Plan</b> - {$invoiceItem.billingInfo}
			{elseif $invoiceItem.limitInfo != NULL}
				<b>Limit</b> - {$invoiceItem.limitInfo}
			{elseif $invoiceItem.customInfo != NULL}
				<b>Custom</b> - {$invoiceItem.customInfo}
			{elseif $invoiceItem.moduleID != NULL}
				<b>Module</b> - {$invoiceItem.module_name}
			{/if}	
		</td>
		<td>{$currentCurrency.sign} {$invoiceItem.total}</td>
	</tr>
	{/foreach}
</table>

<!-- DEPRECATED
<table width="95%" align="center" cellpadding="0" cellspacing="0"  class="invoiceDetals">
	<tr class="invoiceDetals_table">
		<td>
			Item
		</td>	
		<td>
			Billing Period Date Start
		</td>	
		<td>
			Billing Period Date Finish
		</td>
		<td>
			Suspension Date
		</td>		
		<td>
			Setup Charge  
		</td>	
		<td>
			Amount  
		</td>	
		<td>
			Discount  
		</td>
		<td>
			Total  
		</td>
	</tr>
	
	
	{foreach from=$invoiceDetails.invoice_items  item=invoiceItem}
	
	<tr class="invoiceDetals_table_body">
		<td>
			{if $invoiceDetails.billingInfo != NULL}
				<b>Billing Plan</b> - {$invoiceDetails.billingInfo}
			{elseif $invoiceDetails.limitInfo != NULL}
				<b>Limit</b> - {$invoiceDetails.limitInfo}
			{elseif $invoiceDetails.customInfo != NULL}
				<b>Custom</b> - {$invoiceDetails.customInfo}
			{elseif $invoiceDetails.moduleID != NULL}
				<b>Module</b> - {$invoiceDetails.module_name}
			{/if}						 
		</td>		
		<td>
			{$invoiceDetails.periodStartDate}
		</td>	
		<td>
			{$invoiceDetails.periodEndDate}
		</td>
		<td>
			<b>{$invoiceDetails.suspensionDate}</b>
		</td>
		<td>
			${$invoiceDetails.oneTimeCharge}
		</td>		
		<td>
			${$invoiceItem.amount}  
		</td>	
		<td>
			${$invoiceDetails.discount}   
		</td>
		<td>
			<b>${$invoiceDetails.total}</b>
		</td>
	</tr>
	{/foreach}
</table> -->
<br/>
<table width="95%" align="center" cellpadding="0" cellspacing="0"  class="invoiceDetals">
	
	<tr>
		<td colspan="2" rowspan="3" style="width:75%;"></td>
		<td>Subtotal:</td>
		<td>{$currentCurrency.sign} {$invoiceDetails.total}</td>
	</tr>
	<tr>
		<td>Discount:</td>
		<td>{$currentCurrency.sign} {$invoiceDetails.discountSum}</td>
	</tr>
	<tr>
		<td>TOTAL:</td>
		<td><b>{$currentCurrency.sign} {$invoiceDetails.totalSum}</b></td>
	</tr>
</table>

<br>
{* history *}
<table width="95%" align="center" cellpadding="0" cellspacing="0"  class="invoiceDetals">
	<tr class="invoiceDetals_table">
		<td colspan="7">
			<b>Register History for Invoice ID {$invoiceDetails.invoiceID}</b>
		</td>
	</tr>
	<tr class="invoiceDetals_table_head">
		<td>
			Date
		</td>	
		<td>
			Description
		</td>	
		<td>
			Invoice
		</td>
		<td>
			Status  
		</td>	
		<td>
			Due 
		</td>	
		<td>
			Paid  
		</td>
		<td>
			Balance  
		</td>		
	</tr>
	{section name=i loop=$paymentHistory}
		<tr class="invoiceDetals_table_body">
			<td>
				{$paymentHistory[i].date}
			</td>
			<td>
				{$paymentHistory[i].description}
			</td>
			<td>
				{$paymentHistory[i].invoiceID}
			</td>
			<td>
				{$paymentHistory[i].status}
			</td>
			<td>
				{$currentCurrency.sign} {$paymentHistory[i].due}
			</td>
			<td>
				{$currentCurrency.sign} {$paymentHistory[i].paid}
			</td>
			{if $paymentHistory[i].balance >= 0}
				<td style="color:green">				
					{$currentCurrency.sign} {$paymentHistory[i].balance}
				</td>
			{else}
				<td style="color:red">				
					{$currentCurrency.sign} {$paymentHistory[i].balance}
				</td>
			{/if}
			
		</tr>
	{/section}
</table>
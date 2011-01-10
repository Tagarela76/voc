<form action="admin.php?action=vps&vpsAction=editItem&itemID=customer" method="POST">
 {*shadow_table*}	
	             <table cellspacing="0" cellpadding="0" align="center" width="100%">
                         <tr>
                               <td valign="top" class="report_uploader_t_l_violet"></td>
                               <td valign="top" class="report_uploader_t_violet"></td>
                               <td valign="top" class="report_uploader_t_r_violet"></td>
						</tr>
						  <tr>
							   <td valign="top" class="report_uploader_l_violet"></td>
                               <td valign="top" class="report_uploader_c_violet">
{*shadow_table*}
<table align="center" border="0" width="100%">	
	<tr>
		<td colspan="2">Edit Customer {$customer.name}</td>
	</tr>	
	<tr>
		<td>ID</td>
		<td>{$customer.id}</td>
	</tr>
	<tr>
		<td>Name</td>
		<td>{$customer.name}</td>
	</tr>
	<tr>
		<td>Contact Person</td>
		<td>{$customer.contactPerson}</td>
	</tr>
	<tr>
		<td>Phone</td>
		<td>{$customer.phone}</td>
	</tr>
	<tr>
		<td>E-mail</td>
		<td>{$customer.email}</td>
	</tr>
	<tr>
		<td>Billing Plan</td>
		<td>
			{if $what2edit == "billing"}
			<div>
				<div style="float:left">
					<select name="newBillingPlan">
						{section name=i loop=$availableBillingPlans}
						 	<option value="{$availableBillingPlans[i].billingID}" {if $availableBillingPlans[i].billingID == $billingPlan.billingID}selected{/if}> {$availableBillingPlans[i].bplimit} sources {$availableBillingPlans[i].months_count} months {$availableBillingPlans[i].type} </option>
						{/section}					
					</select>
				</div>
				<div style="float:left;">
					<div><input type="radio" name="type" value="bpEnd" checked>Apply after current Billing Plan ends</div>
					<div><input type="radio" name="type" value="asap">Apply ASAP</div>
				</div>				
			</div>
			{else}
				{$billingPlan.bplimit} sources {$billingPlan.months_count} months {$billingPlan.type}
			{/if}		
		</td>
	</tr>
	<tr>
		<td>Trial Period End Date</td>
		<td>{$customer.trial_end_date}</td>
	</tr>
	<tr>
		<td>Balance</td>
		{if $what2edit == "balance"}
			<td>{$curentCurrency.sign} {$customer.balance} <select name="operation">
											<option value="+" {if $operation == "+"}selected{/if}>+</option>
											<option value="-" {if $operation == "-"}selected{/if}>-</option>
										</select>
										{$curentCurrency.sign}<input type="text" name="balance" value="{$addToBalance}">
										{if $problem.balance}Fail{/if}			
		{else}
			<td>${$customer.balance}</td>
		{/if}
	</tr>
	<tr>
		<td>Due Date</td>
		<td>{$customer.dueDate}</td>
	</tr>
	<tr>
		<td>Active</td>
		{if $what2edit == "status"}		
			<td><input type="checkbox" name="active" {if $customer.status == "on"} checked {/if}>
				{if $customer.status == "off"}					
						Restore and shift last billing period for <input type="text" name="dayShift" value="{$dayShift}"> days. {if $dayShift > 30}<i>(Customer didn't use it before deactivation)</i>{/if} {if $problem.dayShift}Fail{/if}					
				{/if}
			</td>
		{else}
			{if $customer.status == "on"}<td style="color:green;">yes</td>{elseif $customer.status == "off"}<td style="color:grey;">no</td>{/if}
		{/if}		
	</tr>
	<tr>
		<td colspan="2"><input type="submit" value="Save"><input type="button" value="Cancel" onClick="location.href='admin.php?action=vps&vpsAction=viewDetails&itemID=customer&customerID={$customer.id}'"></td>				
	</tr>					
</table>

<input type="hidden" name="what2edit" value="{$what2edit}">
<input type="hidden" name="customerID" value="{$customer.id}">
</form>
   {*/shadow_table*}	
					         </td>
					          <td valign="top" class="report_uploader_r_violet"></td>
			           </tr>
				        <tr>          
                             <td valign="top" class="report_uploader_b_l_violet"></td>
                             <td valign="top" class="report_uploader_b_violet"></td>
                             <td valign="top" class="report_uploader_b_r_violet"></td>                           				
				       </tr>
		      </table>
		
		      {*/shadow_table*}	
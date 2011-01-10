<script type="text/javascript" src="modules/js/jquery-ui-1.8.2.custom/js/jquery-1.4.2.min.js"></script>
<script type="text/javascript" src="modules/js/jquery-calendar.js"></script>
<link rel="stylesheet" type="text/css" href="style/jquery-calendar.css" />

<form action="admin.php?action=vps&vpsAction=manageInvoice&invoiceAction=areYouSureAdd" method="POST">
<table align="center" border="1" width="80%">	
	<tr>
		<td colspan="13">Add new {$invoiceType} invoice to customer <b>{$customerDetails.name}</b></td>
	</tr>
{if $invoiceType == 'custom'}	
	<tr>
		<td>Item</td>
		<td><input type="text" name="customInfo" value="{$customInfo}"></td>
	</tr>
	<tr>
		<td>Amount, $</td>
		<td>
			<input type="text" name="amount" value="{$defaultAmount}">
			{if $problem.amount}fail{/if}
		</td>
	</tr>
	<tr>
		<td>
			Suspension Date (yyyy-mm-dd)			
		</td>
		<td>
			<input type="text" name="suspensionDate" id="calendar1" class="calendarFocus" value='{$defaultSuspensionDate}'/>
			{if $problem.suspensionDate}fail{/if}
		</td>
	</tr>
	<tr>
		<td>Deactivate customer after suspension date</td>
		<td><input type="checkbox" name="suspensionDisable" {if $suspensionDisable}checked{/if}></td>
	</tr>
{elseif $invoiceType == 'limit'}
	<tr>
		<td>Limit</td>
		<td>
			<select name="limitID">
				{section name=i loop=$limitList}
					<option value="{$limitList[i].id}" {if $limitList[i].id == $limitID} selected {/if}>{$limitList[i].name} ({$limitList[i].unit_type})</option>
				{/section}
			</select>
		</td>		
	</tr>
	<tr>
		<td>Increase Limit Value</td>
		<td>+<input type="text" name="plusToValue" value="{$plusToValue}">{if $problem.plusToValue}fail{/if}</td>
	</tr>
{elseif $invoiceType == 'module'}
	
	<tr>
		<td>Module</td>
		<td>
			<select name="module_name">
				{section name=i loop=$modules}
					<option value="{$modules[i].name}" {if $modules[i].name == $module_name} selected {/if}>{$modules[i].name}</option>
				{/section}
			</select>			
		</td>		
	</tr>
	<tr>
		<td>BP Type</td>
		<td>
			{$bp_type}&nbsp;
			<input type='hidden' name='bp_type' value='{$bp_type}'>
		</td>
	</tr>
	<tr>
		<td>Period</td>
		<td>
			<select name="period">
				{section name=i loop=$periodList}
					<option value="{$periodList[i]}" {if $modules[i].id == $module_id} selected {/if}>{$periodList[i]}</option>
				{/section}
			</select>
		</td>
	</tr>
	<tr>
		<td>Start Date</td>
		<td><input type='text' name='startDate' value={$startDate}></td>
	</tr>
{/if}
	<tr>
		<td colspan="2">
			<input type="submit" class="button" value="Save"/>
			<input type="button" class="button" onclick="location.href='admin.php?action=vps&vpsAction=viewDetails&itemID=customer&customerID={$customerDetails.company_id}'" value="Cancel"/>
			<input type="hidden" name="customerID" value="{$customerDetails.company_id}">
			<input type="hidden" name="invoiceType" value="{$invoiceType}">
		</td>				
	</tr>
		
		
	{*	<td>InvNum</td>
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
	</tr>*}
	
</table>
</form>

{literal}
<script>

	  $(document).ready(function (){
	  	popUpCal.dateFormat = 'YMD-';         
        $("#calendar1").calendar();        
      });                
</script>
{/literal}
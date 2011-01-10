{if !$newUserRegistration}
	{include file="tpls:bookmarks_vps.tpl" }
{else}
	<center><h1>Are you sure you want to apply following Billing Plan?</h1></center>
	<center><h1>Attention! Invoice for setup and first billing period will be generated now.</h1></center>
	<form action="vps.php?action=addUser&step=third" method="post">
{/if}

{*notifications*}
	{if $message }
		{include file="tpls:../user/tpls/notify/greenNotify.tpl" text=$message}		
	{/if}
{**}	
<input type="hidden" name="currencyID" value="{$currentCurrency.id}" />
<table class="report_issue_green" cellspacing="0" cellpadding="0" align="center">
	<tr>
		<td valign="top" class="report_issue_top_green"></td>
	</tr>
	<tr>
		<td valign="top" class="report_issue_center" align="center">
{if $pleaseWait}
	<table border="0" align="center" width="440px" class="dashboard" cellspacing="0" >
		<tr>
			<td class="dashboard">
				PLEASE WAIT
			</td>			
		</tr>
		<tr>
			<td align="center">
				{$pleaseWait}
			</td>
		</tr>
	</table>
{else}
			<table border="0" align="center" width="440px" class="dashboard" cellspacing="0" cellpadding="3" >					
				<tr>
					<td colspan="2"  class="dashboard">
						{$billingPlan.name}
					</td>
				</tr>
				<tr>
					<td colspan="2">
						{$billingPlan.description}
					</td>
				</tr>
				<tr>
					<td colspan="2" align="center" bgcolor="#ffffff" style="color:#4BA26A">
						{if $billingPlan.type == "self"} SELF COMPLIANCE & REPORTING SOLUTIONS {elseif $billingPlan.type == "gyant"} GYANT COMPLIANCE & REPORTING SOLUTIONS {/if}
					</td>
				</tr>
				<tr>
					<td class="pcenter">
						Emission Sources:
					</td>
					<td class="pcenter">					
						{$billingPlan.bplimit}					
					</td>
				</tr>
				<tr>
					<td class="pcenter">
						Payment period (months):
					</td>
					<td class="pcenter">
						{$billingPlan.months_count}
					</td>
				</tr>
				<tr>
					<td class="pcenter">
						One Time Setup Charge:
					</td>
					<td class="pcenter">
						{$totalCurrency.sign} {$billingPlan.one_time_charge}
					</td>
				</tr>
				<tr>
					<td class="pcenter">
						Cost:
					</td>
					<td class="pcenter">
						{$totalCurrency.sign} {$billingPlan.price}
					</td>
				</tr>
				<tr>
					<td class="pcenter">
						{if $isRegistration}
						<b>Billing Total Amount:</b>
						{else}
						<b>Invoice Total Amount:</b>
						{/if}
					</td>
					<td class="pcenter">
						<b>{$totalCurrency.sign} {$totalInvoice}</b>
					</td>
				</tr>									
			</table>
			<br/>
			{if $isRegistration} <!-- If registration -->
Selected Modules:
			<table border="0" align="center" width="455px" class="dashboard" cellspacing="0" cellpadding="3">
				<tr align="center" bgcolor="#ffffff" style="color:#4BA26A">
					<td>MODULE</td>
					<td>PERIOD</td>
					<td>PRICE</td>
				</tr>
				{if $appliedModules}
				
				
				{foreach from=$appliedModules item=module}
				<tr>
					<td class="pcenter" style="padding-left:15px;">{$module.module_name}</td>
					<td class="pcenter" style="padding-left:15px;">{$module.month_count} month</td>
					<td class="pcenter" style="padding-left:15px;">{$currentCurrency.sign} {$module.price}</td>
				</tr>	
				{/foreach}
				
				
				<tr>
					<td class="pcenter" style="padding-left:15px;" colspan="2">
						<b>Total selected modules price:</b>
					</td>
					<td class="pcenter"  >
						<b>{$currentCurrency.sign} {$totalModulesPriceFormat}</b>
					</td>
				</tr>	
				
				{else}
				<tr>
					<td colspan="3" style="text-align:center;font-size:15px;" >
					no selected modules
					</td>
				</tr>
				
				
				{/if}		
			</table>
			<br/>
			{else}
Active Modules:
			<table border="0" align="center" width="455px" class="dashboard" cellspacing="0">
				<tr align="center" bgcolor="#ffffff" style="color:#4BA26A">
					<td>
					 MODULE
					</td>
					<td>
					PERIOD
					</td>
					<td>
					START DATE
					</td>
					<td>
					END DATE
					</td>
					<td>
					PRICE
					</td>
					<td>
					STATUS
					</td>
					<td > &nbsp;</td>
				</tr>
				{if !$appliedModules}
					<tr>
						<td class="pcenter" colspan="6">No active modules</td>
					</tr>
				{else}
					{foreach from=$appliedModules item=module}
					<tr>
						<td class="pcenter" style="font-style:italic;font-weight:bold;" colspan="1">{$module.module_name}&nbsp;</td>
						<td class="pcenter" colspan="4"><input type="button" value="Delete All Plans(Deactivate module)" onClick="location.href='vps.php?category=billing&action=editCategory&subCategory=modules&module={$module.module_id}&total=delete&status=delete_all'" title="Delete all plans for module(deactivate module)">&nbsp;{*to remove all plans for module*}</td>
						<td class="pcenter">{$module.status}&nbsp;</td>
						<td class="pcenter">&nbsp;</td>
						
					</tr>
						{foreach from=$module.plans item=module_plan}
						
							{assign var=currencyID value=$module_plan.currency_id}
							
							<tr>
								<td class="pcenter" style="padding-left:15px;">{$module.module_name}&nbsp;</td>
								<td class="pcenter">{$module_plan.period}&nbsp;</td>
								<td class="pcenter">{$module_plan.start}&nbsp;</td>
								<td class="pcenter">{$module_plan.end}&nbsp;</td>
								<td class="pcenter">{$groupedCurrencies.$currencyID.sign} {$module_plan.price}&nbsp;</td>
								<td class="pcenter">{$module_plan.status}&nbsp;</td>
								<td class="pcenter"><input type="button" value="X" onClick="location.href='vps.php?category=billing&action=editCategory&subCategory=modules&module={$module.module_id}&plan={$module_plan.id}&total=delete&status=delete_all'" style="width:5px;" title="Delete module plan">&nbsp;</td>
							</tr>
						{/foreach}
					{/foreach}
				{/if}	
			</table>
			<br/>
			{/if}
			{if !$isRegistration}
Bonus Modules:
			<table border="0" align="center" width="440px" class="dashboard" cellspacing="0">
				<tr bgcolor="#ffffff" style="color:#4BA26A">
					<td>
					 MODULE
					</td>
					<td> &nbsp;</td>
				</tr>
				{if !$bonusModules}
					<tr>
						<td class="pcenter" colspan="2">No bonus modules</td>
					</tr>
				{else}
					{foreach from=$bonusModules item=module}
					{if $module}
					<tr style="height:10px;">
						<td class="pcenter" style="font-style:italic;font-weight:bold;" colspan="1">{$module}&nbsp;</td>
						<td class="pcenter">&nbsp;</td>
					</tr>
					{/if}
					{/foreach}
				{/if}	
			</table>
			<br/>
			{/if}
			
Also you have:
			<table border="0" align="center" width="440px" class="dashboard" cellspacing="0">
				<tr>
					<td class="pcenter">
						MSDS Count Limit:
					</td>
					<td class="pcenter">
						{$billingPlan.limits.MSDS.max_value}
					</td>
				</tr>
				<tr>
					<td class="pcenter">
						Memory Limit:
					</td>
					<td class="pcenter">
						{$billingPlan.limits.memory.max_value}
					</td>
				</tr>
			</table>
			
			{if $isRegistration}
			<br/>
			<br/>
			<table border="0" align="center" width="455px" class="dashboard" cellspacing="0" cellpadding="3"> <!-- Total Invoice Amount -->
					<td class="pcenter" style="padding-left:15px;" colspan="2">
						<b>Total Invoice Amount:</b>
					</td>
					<td class="pcenter"  >
					
						<b>{$currentCurrency.sign} {$totalInvoiceForAllFormat}</b>
					</td>
			</table>
			{/if}
			
			{if !($discountPercent == "0.00" || !$discountPercent)}			
			<div><b>Congratilations!</b> You have a discount <b>-{$discountPercent}%</b></div>
			{/if}
			
						
			{if $futureBillingPlan}
			<table border="0" align="center" width="440px" class="dashboard" cellspacing="0">
				<tr>
					<td colspan="2" height="40">
						&nbsp;
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<img src='design/user/img/alert1.gif' height=16  style="float:left;">{$futurePlanLabel} 						
					</td>
				</tr>
				<tr>
					<td colspan="2">
						&nbsp;
					</td>
				</tr>
				<tr>
					<td colspan="2"  class="dashboard">
						{$futureBillingPlan.name}
					</td>
				</tr>
				<tr>
					<td colspan="2">
						{$futureBillingPlan.description}
					</td>
				</tr>
				<tr>
					<td colspan="2" align="center" bgcolor="#ffffff" style="color:#4BA26A">
						{if $futureBillingPlan.type == "self"} SELF COMPLIANCE & REPORTING SOLUTIONS {elseif $futureBillingPlan.type == "gyant"} GYANT COMPLIANCE & REPORTING SOLUTIONS {/if}
					</td>
				</tr>
				<tr>
					<td class="pcenter">
						Emission Sources:
					</td>
					<td class="pcenter">					
						{$futureBillingPlan.bplimit}					
					</td>
				</tr>
				<tr>
					<td class="pcenter">
						Payment period (months):
					</td>
					<td class="pcenter">
						{$futureBillingPlan.months_count}
					</td>
				</tr>
				<tr>
					<td class="pcenter">
						One Time Setup Charge:
					</td>
					<td class="pcenter">
						{$currentCurrency.sign} {$futureBillingPlan.one_time_charge}
					</td>
				</tr>
				<tr>
					<td class="pcenter">
						Cost:
					</td>
					<td class="pcenter">
						{$currentCurrency.sign} {$futureBillingPlan.price}
					</td>
				</tr>		
			</table>
				{/if}
			
			
			
{/if}
			{*shadow*}	
		</td>
	</tr>
	<tr>
		<td valign="top" class="report_issue_bottom_green">		
		</td>					
	</tr>
</table>

{if $newUserRegistration}
<div align="center">
	<input type="submit" class="button" name="registrationAction" value="Save">
	<input type="submit" class="button" name="registrationAction" value="Cancel">
</div>
	</form>
{/if}		
		{**}	
	
	
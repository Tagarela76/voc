
<div style="padding:7px;">

{if $currentBookmark eq "payInvoice"}
<form action="vps.php?action=confirmEdit&category={$currentBookmark}&invoiceID={$invoice.invoiceID}" method="post">
{else}
<form action="vps.php?action=confirmEdit&category={$category}" method="post">
{/if}

		<table class="users" align="center" cellpadding="0" cellspacing="0">
			<tr class="users_top_red" >
				<td class="users_u_top_red" height="27" colspan="{if $areYouSureAction eq 'apply module plan'}6{else}{if $areYouSureAction eq 'remove module plan'}5{else}2{/if}{/if}"> {*ALLA!!!!!!!!!!! fix me!*}
					<span >
					{* header constructor *}
					Are you sure you want to {$areYouSureAction}						
						{if $areYouSureAction eq "change billing plan"}
							from <b>{$from.name}</b> to <b>{$to.name}</b>
						{/if} ?								
					{**}
					</span>
				</td>
				<td class="users_u_top_r_red">
				</td>
			</tr>
			
{if $areYouSureAction eq "pay for invoice"}
<tr>
	<td colspan="3" style="width:100%;padding:0px;">
		{include file="tpls:tpls/invoiceShortDescription.tpl"}
	</td>
</tr>
{/if}

{if $areYouSureAction eq "change billing plan"}

			<tr  bgcolor="#e3e3e3">
				<td class="border_users_l border_users_b" width="20%">&nbsp;</td>										
				<td class="border_users_l border_users_b border_users_r" width="40%">From</td>
				<td class="border_users_r border_users_b">To</td>								
			</tr>
					<tr height="20" class="hov_company">
						<td class="border_users_l border_users_b border_users_r">
							ID
						</td>
						<td class="border_users_r border_users_b">
							{$from.billingID}
						</td>
						<td class="border_users_r border_users_b">
							{$to.billingID}
						</td>
					</tr>
					<tr height="20" class="hov_company">
						<td class="border_users_l border_users_b border_users_r">
							Name
						</td>
						<td class="border_users_r border_users_b">
							{$from.name}
						</td>
						<td class="border_users_r border_users_b">
							{$to.name}
						</td>
					</tr>
					<tr height="20" class="hov_company">
						<td class="border_users_l border_users_b border_users_r">
							Description
						</td>
						<td class="border_users_r border_users_b">
							{$from.description}&nbsp;
						</td>
						<td class="border_users_r border_users_b">
							{$to.description}&nbsp;
						</td>
					</tr>
					<tr height="20" class="hov_company">
						<td class="border_users_l border_users_b border_users_r">
							Emission Sources
						</td>
						<td class="border_users_r border_users_b">							
							{$from.bplimit}							
						</td>
						<td class="border_users_r border_users_b">
							{$to.bplimit}
						</td>
					</tr>
					<tr height="20" class="hov_company">
						<td class="border_users_l border_users_b border_users_r">
							Months Count
						</td>
						<td class="border_users_r border_users_b">
							{$from.months_count}
						</td>
						<td class="border_users_r border_users_b">
							{$to.months_count}
						</td>
					</tr>
					<tr height="20" class="hov_company">
						<td class="border_users_l border_users_b border_users_r">
							One Time Setup Charge
						</td>
						<td class="border_users_r border_users_b">
							{$currentCurrency.sign} {$from.one_time_charge}
						</td>
						<td class="border_users_r border_users_b">
							{$currentCurrency.sign} {$to.one_time_charge}
						</td>
					</tr>
					<tr height="20" class="hov_company">
						<td class="border_users_l border_users_b border_users_r">
							Price
						</td>
						<td class="border_users_r border_users_b">
							{$currentCurrency.sign} {$from.price}
						</td>
						<td class="border_users_r border_users_b">
							{$currentCurrency.sign} {$to.price}
						</td>
					</tr>
					
										
					<tr>
						<td  height="25" class="users_u_bottom">							
						</td>
						<td height="25" colspan="2" class="users_u_bottom_r" align="right">
							<b>Note:</b> Changes will be applied <b>{$dateWhenNewPlanWillBeImplemented}</b>.
							{if $notification}
							<br/><b>Note:</b> {$notification}
							{/if}
						</td>
					</tr>
					
					
					
					<input type="hidden" name="changeTo" value="{$to.billingID}">
					<input type="hidden" name="applyWhen" value="{$applyWhen}">
								
{elseif $areYouSureAction eq "change MSDS limit"}
			<tr  bgcolor="#e3e3e3">
				<td class="border_users_l border_users_b" width="20%">&nbsp;</td>										
				<td class="border_users_l border_users_b border_users_r" width="40%">From</td>
				<td class="border_users_r border_users_b">To</td>								
			</tr>
					<tr height="20" class="hov_company">
						<td class="border_users_l border_users_b border_users_r">
							MSDS Count Limit
						</td>
						<td class="border_users_r border_users_b">
							 {$from.limits.MSDS.max_value} {$from.limits.MSDS.unit_type}
						</td>
						<td class="border_users_r border_users_b">
							 {$to.limits.MSDS.max_value} {$to.limits.MSDS.unit_type}
						</td>
					</tr>
					<tr>
						<td  height="25" class="users_u_bottom">							
						</td>
						<td height="25" colspan="2" class="users_u_bottom_r" align="right">
							<b>Note:</b> Limit increase cost is <b>{$curentCurrency.sign} {$increaseCost}</b>. New invoice will be generated. Please pay for it in <b>30</b> days.
						</td>
					</tr>
					<input type="hidden" name="subCategory" value="{$subCategory}">
					<input type="hidden" name="plusTo" value="{$plusTo}">
					
{elseif  $areYouSureAction eq "change memory limit"}

			<tr  bgcolor="#e3e3e3">
				<td class="border_users_l border_users_b" width="20%">&nbsp;</td>										
				<td class="border_users_l border_users_b border_users_r" width="40%">From</td>
				<td class="border_users_r border_users_b">To</td>								
			</tr>
					<tr height="20" class="hov_company">
						<td class="border_users_l border_users_b border_users_r">
							Memory Storage Limit
						</td>
						<td class="border_users_r border_users_b">
							{$from.limits.memory.max_value} {$from.limits.memory.unit_type}
						</td>
						<td class="border_users_r border_users_b">
							{$to.limits.memory.max_value} {$to.limits.memory.unit_type}
						</td>
					</tr>
					<tr>
						<td  height="25" class="users_u_bottom">							
						</td>
						<td height="25" colspan="2" class="users_u_bottom_r" align="right">
							<b>Note:</b> Limit increase cost is <b>{$currentCurrency.sign} {$increaseCost}</b>. New invoice will be generated. Please pay for it in <b>30</b> days.
						</td>
					</tr>
					<input type="hidden" name="subCategory" value="{$subCategory}">
					<input type="hidden" name="plusTo" value="{$plusTo}">
{elseif $areYouSureAction eq "apply module plan"}
{if $plans}
			<tr  bgcolor="#e3e3e3">
				<td class="border_users_l border_users_b">&nbsp;</td>
				<td class="border_users_l border_users_b">Module</td>										
				<td class="border_users_l border_users_b border_users_r">BP Type</td>
				<td class="border_users_l border_users_b border_users_r">Period</td>
				<td class="border_users_l border_users_b border_users_r">Start Date</td>
				<td class="border_users_l border_users_b border_users_r">End Date</td>
				<td class="border_users_r border_users_b">Price</td>						
			</tr>
{foreach from=$plans item=plan key=module_id} 
	{if $oldPlans[$module_id]}
		{foreach from=$oldPlans[$module_id] item=oldPlan}
					<tr height="20" class="hov_company">
						<td class="border_users_l border_users_b border_users_r">
							From&nbsp;
						</td>
						<td class="border_users_l border_users_b border_users_r">
							{$oldPlan.module_name}&nbsp;
						</td>
						<td class="border_users_r border_users_b">
							{$oldPlan.type}&nbsp;
						</td>
						<td class="border_users_r border_users_b">
							{$oldPlan.month_count} {if $plan.month_count >1}Months{else}Month{/if}
						</td>
						<td class="border_users_r border_users_b">
							{$oldPlan.currentInvoice.periodStartDate}&nbsp;
						</td>
						<td class="border_users_r border_users_b">
							{$oldPlan.currentInvoice.periodEndDate}&nbsp;
						</td>
						<td class="border_users_r border_users_b">
							{$currentCurrency.sign} {$oldPlan.price}&nbsp;
						</td>
					</tr>
		{/foreach}			
					<tr height="20" class="hov_company">
						<td class="border_users_l border_users_b border_users_r" style="border-bottom-width:5px;">
							To&nbsp;
						</td>
						<td class="border_users_l border_users_b border_users_r" style="border-bottom-width:5px;">
							{$plan.module_name}&nbsp;
						</td>
						<td class="border_users_r border_users_b" style="border-bottom-width:5px;">
							{$plan.type}&nbsp;
						</td>
						<td class="border_users_r border_users_b" style="border-bottom-width:5px;">
							{$plan.month_count} {if $plan.month_count >1}Months{else}Month{/if}
						</td>
						<td class="border_users_r border_users_b" style="border-bottom-width:5px;">
							{$plan.start}&nbsp;
						</td>
						<td class="border_users_r border_users_b" style="border-bottom-width:5px;">
							{$plan.end}&nbsp;
						</td>
						<td class="border_users_r border_users_b" style="border-bottom-width:5px;">
							{$currentCurrency.sign} {$plan.price}&nbsp;
						</td>
					</tr>
				
	{else}
					<tr height="20" class="hov_company">
						<td class="border_users_l border_users_b border_users_r" style="border-bottom-width:5px;"> &nbsp; <i>New</i></td>
						<td class="border_users_r border_users_b" style="border-bottom-width:5px;">
							{$plan.module_name}&nbsp;
						</td>
						<td class="border_users_r border_users_b" style="border-bottom-width:5px;">
							{$plan.type}&nbsp;
						</td>
						<td class="border_users_r border_users_b" style="border-bottom-width:5px;">
							{$plan.month_count} {if $plan.month_count >1}Months{else}Month{/if}
						</td>
						<td class="border_users_r border_users_b" style="border-bottom-width:5px;">
							{$plan.start}&nbsp;
						</td>
						<td class="border_users_r border_users_b" style="border-bottom-width:5px;">
							{$plan.end}&nbsp;
						</td>
						<td class="border_users_r border_users_b" style="border-bottom-width:5px;">
							{$currentCurrency.sign} {$plan.price}&nbsp;
						</td>
					</tr>
				
	{/if}
{/foreach}
<tr>
						<td  height="25" class="users_u_bottom">							
						</td>
						<td height="25" colspan="6" class="users_u_bottom_r" align="right">
							&nbsp;
						</td>
					</tr>
					
					<input type="hidden" name="subCategory" value="{$subCategory}">
					<input type="hidden" name="changeTo" value='{$plan_ids}'>
					<input type="hidden" name="startDate" value="{$start}">
{else}
<tr><td>No modules was selected to activate!</td></tr>
{/if}
{elseif $areYouSureAction eq "remove module plan"}
		<tr  bgcolor="#e3e3e3">
				<td class="border_users_l border_users_b" width="30%">Module</td>										
				<td class="border_users_l border_users_b border_users_r">BP Type</td>
				<td class="border_users_l border_users_b border_users_r">Period</td>
				<td class="border_users_l border_users_b border_users_r">Start Date</td>
				<td class="border_users_l border_users_b border_users_r">End Date</td>
				<td class="border_users_r border_users_b">Price</td>								
			</tr>
			{if $status eq "delete_plan"}
					<tr height="20" class="hov_company">
						<td class="border_users_l border_users_b border_users_r">
							<input type="hidden" name="plan_id" value="{$plan.id}">
							{$plan.module_name}&nbsp;
						</td>
						<td class="border_users_r border_users_b">
							{$plan.type}&nbsp;
						</td>
						<td class="border_users_r border_users_b">
							{$plan.month_count} {if $plan.month_count >1}Months{else}Month{/if}
						</td>
						<td class="border_users_r border_users_b">
							{$plan.currentInvoice.periodStartDate}&nbsp;
						</td>
						<td class="border_users_r border_users_b">
							{$plan.currentInvoice.periodEndDate}&nbsp;
						</td>
						<td class="border_users_r border_users_b">
							{$currentCurrency.sign} {$plan.price}&nbsp;
						</td>
					</tr>
			{else}
				{foreach from=$plans item=plan}
					<tr height="20" class="hov_company">
						<td class="border_users_l border_users_b border_users_r">
							{$plan.module_name}&nbsp;
						</td>
						<td class="border_users_r border_users_b">
							{$plan.type}&nbsp;
						</td>
						<td class="border_users_r border_users_b">
							{$plan.month_count} {if $plan.month_count >1}Months{else}Month{/if}
						</td>
						<td class="border_users_r border_users_b">
							{$plan.currentInvoice.periodStartDate}&nbsp;
						</td>
						<td class="border_users_r border_users_b">
							{$plan.currentInvoice.periodEndDate}&nbsp;
						</td>
						<td class="border_users_r border_users_b">
							{$currentCurrency.sign} {$plan.price}&nbsp;
						</td>
					</tr>
				{/foreach}
			{/if}
					<tr>
						<td  height="25" class="users_u_bottom">							
						</td>
						<td height="25" colspan="5" class="users_u_bottom_r" align="right">
							&nbsp;
						</td>
					</tr>
					<input type="hidden" name="subCategory" value="{$subCategory}">
					<input type="hidden" name="module_id" value="{$moduleID}">
					<input type="hidden" name="status" value="{$status}">
					<input type="hidden" name="total" value="delete">
{/if}		
		</table>
		<br>
		<table width="100%">
			<tr>
				<td width="80%">
				</td>
				<td>
				
					<input class="button" type="submit" value="Yes">
				</td>
				<td>
				{if $areYouSureAction eq "pay for invoice"}
					<input class="button" type="button" value="No" onClick="location.href='vps.php?action=viewList&category=invoices&subCategory=Due'">
				{else}
					<input class="button" type="button" value="No" onClick="location.href='vps.php?action=viewDetails&category=billing&subCategory=AvailableBillingPlans'">
				{/if}
				</td>
			</tr>
		</table>
	
		{*<input type="hidden" name="action" value="confirmEdit">		
		<input type="hidden" name="category" value="{$category}">*}
		
</form>		

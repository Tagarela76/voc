<div style="padding:7px;">

<form action="admin.php?action=vps&vpsAction=confirmEdit&itemID={$itemID}" method="post">

		<table class="users" align="center" cellpadding="0" cellspacing="0">
			<tr class="users_top_red" >
				<td class="users_u_top_red users_u_top_r_red" height="27" colspan="{if $moduleAction eq 'remove_all'}6{else}3{/if}">
					<span >
					{*--header-constructor--*}
					{if $itemID eq 'availableBillingPlans'}						
						Are you sure you want to change following billing plans ?
					{elseif $itemID eq 'limits'}						
						Are you sure you want to change following extra limits ?
					{elseif $itemID eq 'definedBillingPlans'}						
						Are you sure you want to change following defined Billing Plan ?
					{elseif $itemID eq "customer"}
						Are you sure you want to change following customer settings ?
					{elseif $itemID eq "notRegisteredCustomer"}
						Are you sure you want to register customer <b>{$customer.name}</b>?
					{elseif $itemID eq "modules"}
						Are you sure you want to deactivate module 
						{if $moduleAction eq 'remove_all'}<b>{$module[0].module_name}</b> for customer <b>{$customer.name}</b> from now on?
						{else} plan {$module.module_name} {$module.month_count}</b> for customer <b>{$customer.name}</b> for choosen period?
						{/if}
					{else}
						Are you sure you want to make changes ?					
					{/if}																
					{*----------------------*}
					</span>
				</td>
				{*<td class="users_u_top_r_red">
				</td>*}
			</tr>
			
			
{if $itemID eq 'availableBillingPlans'}		
<input type='hidden' name='currency' value='{$currentCurrency.id}' />
<input type='hidden' name='b2cID' value='{$b2cID}' />
<input type='hidden' name='currencyID' value='{$currencyID}' />
<input type='hidden' name='bpLimit' value='{$bpLimit}' />
<input type='hidden' name='newPrice' value='{$newPrice}' />			
			{section name=i loop=$itemCount}			
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
							{$from[i].id}&nbsp;
						</td>
						<td class="border_users_r border_users_b">
							{$to[i].id}&nbsp;
							<input type="hidden" name="billingID_{$smarty.section.i.index}" value="{$to[i].billingID}">
						</td>
					</tr>
					<tr height="20" class="hov_company">
						<td class="border_users_l border_users_b border_users_r">
							Name
						</td>
						<td class="border_users_r border_users_b">
							{$from[i].name}&nbsp;
						</td>
						<td class="border_users_r border_users_b">
							{$to[i].name}&nbsp;
							<input type="hidden" name="name_{$smarty.section.i.index}" value="{$to[i].name}">
						</td>
					</tr>
					<tr height="20" class="hov_company">
						<td class="border_users_l border_users_b border_users_r">
							Description
						</td>
						<td class="border_users_r border_users_b">
							{$from[i].description}&nbsp;
						</td>
						<td class="border_users_r border_users_b">
							{$to[i].description}&nbsp;
							<input type="hidden" name="description_{$smarty.section.i.index}" value="{$to[i].description}">
						</td>
					</tr>
					<tr height="20" class="hov_company">
						<td class="border_users_l border_users_b border_users_r">
							One Time Setup Charge
						</td>
						<td class="border_users_r border_users_b">							
							{$currentCurrency.sign} {$from[i].one_time_charge}							
						</td>
						<td class="border_users_r border_users_b">
							{$currentCurrency.sign} {$to[i].one_time_charge}
							<input type="hidden" name="one_time_charge_{$smarty.section.i.index}" value="{$to[i].one_time_charge}">							
						</td>
					</tr>
					<tr height="20" class="hov_company">
						<td class="border_users_l border_users_b border_users_r">
							Emission Sources
						</td>
						<td class="border_users_r border_users_b">							
							{$from[i].bplimit}							
						</td>
						<td class="border_users_r border_users_b">
							{$to[i].bplimit}
							<input type="hidden" name="bplimit_{$smarty.section.i.index}" value="{$to[i].bplimit}">
						</td>
					</tr>
					<tr height="20" class="hov_company">
						<td class="border_users_l border_users_b border_users_r">
							Months Count
						</td>
						<td class="border_users_r border_users_b">
							{$from[i].months_count}
						</td>
						<td class="border_users_r border_users_b">
							{$to[i].months_count}
							<input type="hidden" name="monthsCount_{$smarty.section.i.index}" value="{$to[i].months_count}">
						</td>
					</tr>
					<tr height="20" class="hov_company">
						<td class="border_users_l border_users_b border_users_r">
							Price
						</td>
						<td class="border_users_r border_users_b">
							{$currentCurrency.sign} {$from[i].price}
						</td>
						<td class="border_users_r border_users_b">
							{$currentCurrency.sign} {$to[i].price}
							<input type="hidden" name="price_{$smarty.section.i.index}" value="{$to[i].price}">
						</td>
					</tr>
					<tr height="20" class="hov_company">
						<td class="border_users_l border_users_b border_users_r">
							Type
						</td>
						<td class="border_users_r border_users_b">
							{$from[i].type}
						</td>
						<td class="border_users_r border_users_b">
							{$to[i].type}							
						</td>
					</tr>
															
					{/section}
					
{elseif $itemID eq 'limits'}
<input type="hidden" name="currencyID" value="{$curentCurrency.id}" />
			{section name=i loop=$itemCount}			
			<tr  bgcolor="#e3e3e3">
				<td class="border_users_l border_users_b" width="20%">&nbsp;</td>										
				<td class="border_users_l border_users_b border_users_r" width="40%">From</td>
				<td class="border_users_r border_users_b">To</td>								
			</tr>				
					<tr height="20" class="hov_company">
						<td class="border_users_l border_users_b border_users_r">
							Limit Price ID
						</td>
						<td class="border_users_r border_users_b">
							{$from[i].limit_price_id}&nbsp;
						</td>
						<td class="border_users_r border_users_b">
							{$to[i].limit_price_id}&nbsp;
							<input type="hidden" name="limit_price_id_{$smarty.section.i.index}" value="{$to[i].limit_price_id}">
						</td>
					</tr>
					<tr height="20" class="hov_company">
						<td class="border_users_l border_users_b border_users_r">
							Limit Name
						</td>
						<td class="border_users_r border_users_b">
							{$from[i].name}&nbsp;
						</td>
						<td class="border_users_r border_users_b">
							{$to[i].name}&nbsp;							
						</td>
					</tr>
					<tr height="20" class="hov_company">
						<td class="border_users_l border_users_b border_users_r">
							Emission Sources
						</td>
						<td class="border_users_r border_users_b">							
							{$from[i].bplimit}							
						</td>
						<td class="border_users_r border_users_b">
							{$to[i].bplimit}							
						</td>
					</tr>
					<tr height="20" class="hov_company">
						<td class="border_users_l border_users_b border_users_r">
							Default Limit
						</td>
						<td class="border_users_r border_users_b">
							{$from[i].default_limit}
						</td>
						<td class="border_users_r border_users_b">
							{$to[i].default_limit}
							<input type="hidden" name="default_limit_{$smarty.section.i.index}" value="{$to[i].default_limit}">
						</td>
					</tr>
					<tr height="20" class="hov_company">
						<td class="border_users_l border_users_b border_users_r">
							Increase Price (+{$to[i].increase_step} {$to[i].unit_type})
						</td>
						<td class="border_users_r border_users_b">
							{$curentCurrency.sign} {$from[i].increase_cost}
						</td>
						<td class="border_users_r border_users_b">
							{$curentCurrency.sign} {$to[i].increase_cost}
							<input type="hidden" name="increase_cost_{$smarty.section.i.index}" value="{$to[i].increase_cost}">
						</td>
					</tr>
					<tr height="20" class="hov_company">
						<td class="border_users_l border_users_b border_users_r">
							Type
						</td>
						<td class="border_users_r border_users_b">
							{$from[i].type}
						</td>
						<td class="border_users_r border_users_b">
							{$to[i].type}							
						</td>
					</tr>															
					{/section}
					
{elseif $itemID eq 'definedBillingPlans'}
<input type='hidden' name='curentCurrency' value='{$curentCurrency.id}' />
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
							{$from[0].billingID}&nbsp;
						</td>
						<td class="border_users_r border_users_b">
							{$to[0].billingID}&nbsp;
							<input type="hidden" name="billingID" value="{$to[0].billingID}">
						</td>
					</tr>
					<tr height="20" class="hov_company">
						<td class="border_users_l border_users_b border_users_r">
							Company (ID)
						</td>
						<td class="border_users_r border_users_b">
							{$from[0].customer_id}&nbsp;
						</td>
						<td class="border_users_r border_users_b">
							{$to[0].customer_id}&nbsp;
							<input type="hidden" name="customerID" value="{$to[0].customer_id}">							
						</td>
					</tr>
					<tr height="20" class="hov_company">
						<td class="border_users_l border_users_b border_users_r">
							Emission Sources
						</td>
						<td class="border_users_r border_users_b">
							{$from[0].bplimit}&nbsp;
						</td>
						<td class="border_users_r border_users_b">
							{$to[0].bplimit}&nbsp;
							<input type="hidden" name="bplimit" value="{$to[0].bplimit}">
						</td>
					</tr>
					<tr height="20" class="hov_company">
						<td class="border_users_l border_users_b border_users_r">
							Months
						</td>
						<td class="border_users_r border_users_b">
							{$from[0].months_count}&nbsp;
						</td>
						<td class="border_users_r border_users_b">
							{$to[0].months_count}&nbsp;						
							<input type="hidden" name="monthsCount" value="{$to[0].months_count}">	
						</td>
					</tr>
					<tr height="20" class="hov_company">
						<td class="border_users_l border_users_b border_users_r">
							One Time Setup Charge
						</td>
						<td class="border_users_r border_users_b">
							{$curentCurrency.sign} {$from[0].one_time_charge}&nbsp;
						</td>
						<td class="border_users_r border_users_b">
							{$curentCurrency.sign} {$to[0].one_time_charge}&nbsp;
							<input type="hidden" name="oneTimeCharge" value="{$to[0].one_time_charge}">							
						</td>
					</tr>
					<tr height="20" class="hov_company">
						<td class="border_users_l border_users_b border_users_r">
							Cost
						</td>
						<td class="border_users_r border_users_b">
							{$curentCurrency.sign} {$from[0].price}&nbsp;
						</td>
						<td class="border_users_r border_users_b">
							{$curentCurrency.sign} {$to[0].price}&nbsp;
							<input type="hidden" name="price" value="{$to[0].price}">							
						</td>
					</tr>
					<tr height="20" class="hov_company">
						<td class="border_users_l border_users_b border_users_r">
							Type
						</td>
						<td class="border_users_r border_users_b">
							{$from[0].type}
						</td>
						<td class="border_users_r border_users_b">
							{$to[0].type}
							<input type="hidden" name="type" value="{$to[0].type}">							
						</td>
					</tr>
					<tr height="20" class="hov_company">
						<td class="border_users_l border_users_b border_users_r">
							MSDS Default Limit 
						</td>
						<td class="border_users_r border_users_b">
							{$from[0].limits.MSDS.default_limit}
						</td>
						<td class="border_users_r border_users_b">
							{$to[0].limits.MSDS.default_limit}
							<input type="hidden" name="MSDSDefaultLimit" value="{$to[0].limits.MSDS.default_limit}">							
						</td>
					</tr>															
					<tr height="20" class="hov_company">
						<td class="border_users_l border_users_b border_users_r">
							Extra MSDS Cost 
						</td>
						<td class="border_users_r border_users_b">
							{$curentCurrency.sign} {$from[0].limits.MSDS.increase_cost}
						</td>
						<td class="border_users_r border_users_b">
							{$curentCurrency.sign} {$to[0].limits.MSDS.increase_cost}
							<input type="hidden" name="MSDSIncreaseCost" value="{$to[0].limits.MSDS.increase_cost}">							
						</td>
					</tr>
					<tr height="20" class="hov_company">
						<td class="border_users_l border_users_b border_users_r">
							Memory Default Limit 
						</td>
						<td class="border_users_r border_users_b">
							{$from[0].limits.memory.default_limit}
						</td>
						<td class="border_users_r border_users_b">
							{$to[0].limits.memory.default_limit}
							<input type="hidden" name="memoryDefaultLimit" value="{$to[0].limits.memory.default_limit}">							
						</td>
					</tr>
					<tr height="20" class="hov_company">
						<td class="border_users_l border_users_b border_users_r">
							Extra Memory Cost 
						</td>
						<td class="border_users_r border_users_b">
							{$curentCurrency.sign} {$from[0].limits.memory.increase_cost}
						</td>
						<td class="border_users_r border_users_b">
							{$curentCurrency.sign} {$to[0].limits.memory.increase_cost}
							<input type="hidden" name="memoryIncreaseCost" value="{$to[0].limits.memory.increase_cost}">							
						</td>
					</tr>
{elseif $itemID eq 'customer'}
	
		{if $what2edit == 'billing'}
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
							{$from.billingID}&nbsp;
						</td>
						<td class="border_users_r border_users_b">
							{$to.billingID}&nbsp;
							<input type="hidden" name="billingID" value="{$to.billingID}">
						</td>
					</tr>					
					<tr height="20" class="hov_company">
						<td class="border_users_l border_users_b border_users_r">
							Emission Sources
						</td>
						<td class="border_users_r border_users_b">
							{$from.bplimit}&nbsp;
						</td>
						<td class="border_users_r border_users_b">
							{$to.bplimit}&nbsp;							
						</td>
					</tr>
					<tr height="20" class="hov_company">
						<td class="border_users_l border_users_b border_users_r">
							Months
						</td>
						<td class="border_users_r border_users_b">
							{$from.months_count}&nbsp;
						</td>
						<td class="border_users_r border_users_b">
							{$to.months_count}&nbsp;														
						</td>
					</tr>
					<tr height="20" class="hov_company">
						<td class="border_users_l border_users_b border_users_r">
							One Time Setup Charge
						</td>
						<td class="border_users_r border_users_b">
							$ {$from.one_time_charge}&nbsp;
						</td>
						<td class="border_users_r border_users_b">
							$ {$to.one_time_charge}&nbsp;														
						</td>
					</tr>
					<tr height="20" class="hov_company">
						<td class="border_users_l border_users_b border_users_r">
							Cost
						</td>
						<td class="border_users_r border_users_b">
							$ {$from.price}&nbsp;
						</td>
						<td class="border_users_r border_users_b">
							$ {$to.price}&nbsp;														
						</td>
					</tr>
					<tr height="20" class="hov_company">
						<td class="border_users_l border_users_b border_users_r">
							Type
						</td>
						<td class="border_users_r border_users_b">
							{$from.type}
						</td>
						<td class="border_users_r border_users_b">
							{$to.type}														
						</td>
					</tr>
					<tr height="20" class="hov_company">
						<td class="border_users_l border_users_b border_users_r">
							MSDS Default Limit 
						</td>
						<td class="border_users_r border_users_b">
							{$from.limits.MSDS.default_limit}
						</td>
						<td class="border_users_r border_users_b">
							{$to.limits.MSDS.default_limit}														
						</td>
					</tr>															
					<tr height="20" class="hov_company">
						<td class="border_users_l border_users_b border_users_r">
							Extra MSDS Cost 
						</td>
						<td class="border_users_r border_users_b">
							$ {$from.limits.MSDS.increase_cost}
						</td>
						<td class="border_users_r border_users_b">
							$ {$to.limits.MSDS.increase_cost}														
						</td>
					</tr>
					<tr height="20" class="hov_company">
						<td class="border_users_l border_users_b border_users_r">
							Memory Default Limit 
						</td>
						<td class="border_users_r border_users_b">
							{$from.limits.memory.default_limit}
						</td>
						<td class="border_users_r border_users_b">
							{$to.limits.memory.default_limit}														
						</td>
					</tr>
					<tr height="20" class="hov_company">
						<td class="border_users_l border_users_b border_users_r">
							Extra Memory Cost 
						</td>
						<td class="border_users_r border_users_b">
							$ {$from.limits.memory.increase_cost}
						</td>
						<td class="border_users_r border_users_b">
							$ {$to.limits.memory.increase_cost}														
						</td>						
					</tr>
		{elseif $what2edit == 'balance'}
				<tr height="20" class="hov_company">
						<td class="border_users_l border_users_b border_users_r" colspan="3" align="center">
							{if $operation == "+"} Increase {elseif $operation == "-"} Decrease {/if}balance for $ {$balance} for customer <b>{$customerID}</b>?  
							<input type="hidden" name="operation" value="{$operation}">
							<input type="hidden" name="balance" value="{$balance}"> 
						</td>					
					</tr>
		{elseif $what2edit == 'status'}
					<tr height="20" class="hov_company">
						<td class="border_users_l border_users_b border_users_r" colspan="3" align="center">
							{$activate} Customer <b>{$customerID}</b>?
							<input type="hidden" name="activate" value="{$activate}">
							<input type="hidden" name="dayShift" value="{$dayShift}"> 
						</td>					
					</tr>
		{/if}
		
		
{elseif $itemID eq 'moduleBillingPlans'}
<input type='hidden' name='currencyID' value='{$currencyID}' />
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
					{$from.id}&nbsp;
				</td>
				<td class="border_users_r border_users_b">
					{$to.id}&nbsp;
					<input type="hidden" name="billingID" value="{$to.id}">
				</td>
			</tr>		
			<tr height="20" class="hov_company">
				<td class="border_users_l border_users_b border_users_r">
					Months
				</td>
				<td class="border_users_r border_users_b">
					{$from.month_count}&nbsp;
				</td>
				<td class="border_users_r border_users_b">
					{$to.month_count}&nbsp;														
				</td>
			</tr>			
			<tr height="20" class="hov_company">
				<td class="border_users_l border_users_b border_users_r">
					Cost
				</td>
				<td class="border_users_r border_users_b">
					{$curentCurrency.sign} {$from.price}&nbsp;
				</td>
				<td class="border_users_r border_users_b">
					{$curentCurrency.sign} {$to.price}&nbsp;	
					<input type="hidden" name="price" value="{$to.price}">														
				</td>
			</tr>
			<tr height="20" class="hov_company">
				<td class="border_users_l border_users_b border_users_r">
					Type
				</td>
				<td class="border_users_r border_users_b">
					{$from.type}
				</td>
				<td class="border_users_r border_users_b">
					{$to.type}														
				</td>
			</tr>
			<tr height="20" class="hov_company">
				<td class="border_users_l border_users_b border_users_r">
					Module Name 
				</td>
				<td class="border_users_r border_users_b">
					{$from.module_name}
				</td>
				<td class="border_users_r border_users_b">
					{$to.module_name}														
				</td>
			</tr>		
					
		
		
{elseif $itemID eq 'notRegisteredCustomer'}	
					<tr height="20" class="hov_company">
						<td class="border_users_l border_users_b border_users_r">
							ID
						</td>
						<td class="border_users_r border_users_b" colspan="2">
							{$customer.company_id}&nbsp;
						</td>
					</tr>				
					<tr height="20" class="hov_company">
						<td class="border_users_l border_users_b border_users_r">
							Name
						</td>
						<td class="border_users_r border_users_b" colspan="2">
							{$customer.name}&nbsp;
						</td>
					</tr>
					<tr height="20" class="hov_company">
						<td class="border_users_l border_users_b border_users_r">
							Address
						</td>
						<td class="border_users_r border_users_b" colspan="2">
							{$customer.address}&nbsp;
						</td>
					</tr>	
					<tr height="20" class="hov_company">
						<td class="border_users_l border_users_b border_users_r">
							City
						</td>
						<td class="border_users_r border_users_b" colspan="2">
							{$customer.city}&nbsp;
						</td>
					</tr>
					<tr height="20" class="hov_company">
						<td class="border_users_l border_users_b border_users_r">
							Zip
						</td>
						<td class="border_users_r border_users_b" colspan="2">
							{$customer.zip}&nbsp;
						</td>
					</tr>
					<tr height="20" class="hov_company">
						<td class="border_users_l border_users_b border_users_r">
							Phone
						</td>
						<td class="border_users_r border_users_b" colspan="2">
							{$customer.phone}&nbsp;
						</td>
					</tr>
					<tr height="20" class="hov_company">
						<td class="border_users_l border_users_b border_users_r">
							Fax
						</td>
						<td class="border_users_r border_users_b" colspan="2">
							{$customer.fax}&nbsp;
						</td>
					</tr>
					<tr height="20" class="hov_company">
						<td class="border_users_l border_users_b border_users_r">
							Email
						</td>
						<td class="border_users_r border_users_b" colspan="2">
							{$customer.email}&nbsp;
						</td>
					</tr>	
					<tr height="20" class="hov_company">
						<td class="border_users_l border_users_b border_users_r">
							Contact Person
						</td>
						<td class="border_users_r border_users_b" colspan="2">
							{$customer.contact}&nbsp;
						</td>
					</tr>	
					<tr height="20" class="hov_company">
						<td class="border_users_l border_users_b border_users_r">
							Title
						</td>
						<td class="border_users_r border_users_b" colspan="2">
							{$customer.title}&nbsp;
						</td>
					</tr>	
					<tr height="20" class="hov_company">
						<td class="border_users_l border_users_b border_users_r">
							Trial Period End
						</td>
						<td class="border_users_r border_users_b" colspan="2">
							<b>{$customer.trial_end_date}</b>&nbsp;
						</td>
					</tr>			
{elseif $itemID eq "modules"}
		{if $moduleAction eq 'remove_all'}
					<tr height="20" class="hov_company">
						<td class="border_users_l border_users_b border_users_r">
							Module
							<input type="hidden" name="moduleID" value="{$moduleID}">
							<input type="hidden" name="customerID" value="{$customerID}">
							<input type="hidden" name="moduleAction" value="{$moduleAction}">
						</td>
						<td class="border_users_r border_users_b">
							Period
						</td>
						<td class="border_users_r border_users_b">
							Type
						</td>
						<td class="border_users_r border_users_b">
							Start
						</td>
						<td class="border_users_r border_users_b">
							End
						</td>
						<td class="border_users_r border_users_b">
							Price
						</td>
					</tr>
			{foreach from=$module item=plan}
				<tr height="20" class="hov_company">
						<td class="border_users_l border_users_b border_users_r">
							{$plan.module_name}&nbsp;
						</td>
						<td class="border_users_r border_users_b">
							{$plan.month_count} {if $plan.month_count eq 1}Month{else}Months{/if}&nbsp;
						</td>
						<td class="border_users_r border_users_b">
							{$plan.type}&nbsp;
						</td>
						<td class="border_users_r border_users_b">
							{$plan.currentInvoice.periodStartDate}&nbsp;
						</td>
						<td class="border_users_r border_users_b">
							{$plan.currentInvoice.periodEndDate}&nbsp;
						</td>
						<td class="border_users_r border_users_b">
							{$plan.price}&nbsp;
						</td>
					</tr>
			{/foreach}
		{else}
					<tr height="20" class="hov_company">
						<td class="border_users_l border_users_b border_users_r" weight="20%">
							Module
						</td>
						<td class="border_users_r border_users_b" colspan="2">
							<b>{$module.module_name}</b>&nbsp;
							<input type="hidden" name="modulePlanID" value="{$planID}">
							<input type="hidden" name="customerID" value="{$customerID}">
							<input type="hidden" name="moduleAction" value="{$moduleAction}">
						</td>
					</tr>
					<tr height="20" class="hov_company">
						<td class="border_users_l border_users_b border_users_r">
							Billing Plan Type
						</td>
						<td class="border_users_r border_users_b" colspan="2">
							{$module.type}&nbsp;
						</td>
					</tr>
					<tr height="20" class="hov_company">
						<td class="border_users_l border_users_b border_users_r">
							Period
						</td>
						<td class="border_users_r border_users_b" colspan="2">
							{$module.month_count} {if $module.month_count = 1}Month{elseif $module.month_count >1}Months{else}&nbsp;{/if}
						</td>
					</tr>
					<tr height="20" class="hov_company">
						<td class="border_users_l border_users_b border_users_r">
							Start Date
						</td>
						<td class="border_users_r border_users_b" colspan="2">
							{$module.currentInvoice.periodStartDate}&nbsp;
						</td>
					</tr>
					<tr height="20" class="hov_company">
						<td class="border_users_l border_users_b border_users_r">
							End Date
						</td>
						<td class="border_users_r border_users_b" colspan="2">
							{$module.currentInvoice.periodEndDate}&nbsp;
						</td>
					</tr>
					<tr height="20" class="hov_company">
						<td class="border_users_l border_users_b border_users_r">
							Price
						</td>
						<td class="border_users_r border_users_b" colspan="2">
							{$module.price}&nbsp;
						</td>
					</tr>		
		{/if}		
{/if}					
					
					<tr>
						<td  height="25" class="users_u_bottom">						
						</td>
						<td height="25" colspan="{if $moduleAction eq 'remove_all'}5{else}2{/if}" class="users_u_bottom_r" align="right">						
						{if $dateWhenNewPlanWillBeImplemented}<input type="hidden" name="applyWhen" value="{$applyWhen}"><b>Note!</b> Following Billing Plan will be applied <b>{$dateWhenNewPlanWillBeImplemented}</b>.{/if}							
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
				</td>
				<td>
					{if $itemID eq 'notRegisteredCustomer'}
						<input type="button" class="button" value="No" onClick="location.href='admin.php?action=vps&vpsAction=viewDetails&itemID=notRegisteredCustomer&customerID={$customer.company_id}'">
						<input type="hidden" name="what2edit" value="{$what2edit}">
						<input type="hidden" name="customerID" value="{$customer.company_id}">
					{elseif $itemID eq 'customer'}
						<input type="button" class="button" value="No" onClick="location.href='admin.php?action=vps&vpsAction=viewDetails&itemID=customer&customerID={$customerID}'">
						<input type="hidden" name="what2edit" value="{$what2edit}">						
						<input type="hidden" name="customerID" value="{$customerID}">
					{else}
						<input type="button" class="button" value="No" onClick="location.href='admin.php?action=vps&vpsAction=browseCategory&itemID=billing'">
						<input type="hidden" name="itemCount" value="{$itemCount}">
					{/if}					
				</td>
			</tr>
		</table>											
</form>		

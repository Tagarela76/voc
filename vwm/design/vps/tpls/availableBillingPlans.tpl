{if !$newUserRegistration}
	{include file="tpls:bookmarks_vps.tpl" }

 {*shadow_table*}	
	             <table cellspacing="0" cellpadding="0" align="center" >
                         <tr>
                               <td valign="top" class="billing_t_l_yellowgreen"></td>
                               <td valign="top" class="billing_t_yellowgreen"></td>
                               <td valign="top" class="billing_t_r_yellowgreen"></td>
						</tr>
						  <tr>
							   <td valign="top" class="billing_l_yellowgreen"></td>
                               <td valign="top" class="billing_c_yellowgreen">
	           {*shadow_table*}

	<form action="vps.php" method="get">
		<table border="0"  width="100%"  cellspacing="0" cellpadding="5" >
			<tr class="billingPlans_hd">
				<td>EMISSION SOURCES</td>
				<td>ONE TIME SETUP CHARGE</td>
				<td colspan="3">SELF COMPLIANCE TRACKING & REPORTING SOLUTIONS</td>
				<td colspan="3" style="border-right:0px solid #fff;">GYANT COMPLIANCE TRACKING & REPORTING SOLUTIONS</td>
			</tr>		
			<tr class="billingPlansMonths">
				<td></td><td></td>
				{section name=i loop=$months}
					<td style="color:#fff">											
						{$months[i]}
						{if $months[i] eq "1"} month {else} months {/if}						
					</td>
				{/section}
				{section name=i loop=$months}
					<td style="color:#fff;">											
						{$months[i]}
						{if $months[i] eq "1"} month {else} months {/if}						
					</td>
				{/section}	
			</tr>
			{section name=i loop=$sources}			
				<tr class="billingPlans">
				
					<td class="billingPlans_bot" style="border-left:1px solid #fff;">						
						{$sources[i].bplimit}
					</td>
					
					<td class="billingPlans_bot">						
						{$currentCurrency.sign} {$sources[i].one_time_charge}						
					</td>
					
					{section name=j loop=$availablePlans}											
						{if $sources[i].bplimit eq $availablePlans[j].bplimit}
							<td class="billingPlans_bot">
								<input type="radio" name="selectedBillingPlan"  value="{$availablePlans[j].billingID}" {if $availablePlans[j].billingID eq $billingPlan.billingID} checked {/if}>{$currentCurrency.sign} {$availablePlans[j].price}																														
							</td>
						{/if}
					{/section}									
				</tr>
			{/section}
			<tr class="billingPlans">
				<td style="border-left:1px solid #fff;" class="billingPlans_bot">4</td>
				<td  class="billingPlans_bot" colspan="{$monthsCount*2+1}" align="center">If you want to use 4 and more emission sources, you should 
				<a href="vps.php?action=contactAdmin" style="color:black;">contact VOC-WEB-MANAGER's Administrator</a>.</td>				
			</tr>
			<tr>
				<td colspan="{$monthsCount*2+1}" >
					<table>
						<tr>
							<td>
								<input type="radio" name="applyWhen"  value="bpEnd" checked> Apply after current billing period ends
							</td>
							<td>
								<input type="radio" name="applyWhen"  value="asap"> Apply ASAP
							</td>
						</tr>
					</table>
				</td>
				<td style="padding:15px 0 0 0">
					<input type="submit" value="Change My Plan" class="button">
				</td>
			</tr>	
		</table>
		
		<input type="hidden" name="category" value="billing">
		<input type="hidden" name="action" value="editCategory">
		{*if !$newUserRegistration}
			<input type="hidden" name="category" value="billing">
			<input type="hidden" name="action" value="editCategory">
		{*else}			
			<input type="hidden" name="action" value="addUser">
			<input type="hidden" name="step" value="second">
		{/if*}		
		
	</form>
{*if !$newUserRegistration*}
	{*=========================================================*}
		<form action="vps.php" method="get">
		<table border="0"  width="100%"  cellspacing="0" cellpadding="5" >
			<tr class="billingPlans_hd">
				<td>MODULES</td>
				<td>ONE TIME SETUP CHARGE</td>
				<td colspan="3">SELF COMPLIANCE TRACKING & REPORTING SOLUTIONS</td>
				<td colspan="3" style="border-right:0px solid #fff;">GYANT COMPLIANCE TRACKING & REPORTING SOLUTIONS</td>
				<td> &nbsp;</td>
			</tr>		
			<tr class="billingPlansMonths">
				<td></td><td></td>
				{section name=i loop=$months}
					<td style="color:#fff">											
						{$months[i]}
						{if $months[i] eq "1"} month {else} months {/if}						
					</td>
				{/section}
				{section name=i loop=$months}
					<td style="color:#fff;">											
						{$months[i]}
						{if $months[i] eq "1"} month {else} months {/if}						
					</td>
				{/section}	
				<td> &nbsp;</td>
			</tr>
			
			{foreach from=$allModules item=plan key=module_id}			
				<tr class="billingPlans">
				
					<td class="billingPlans_bot" style="border-left:1px solid #fff;">						
						{*$module_id*} {$plan.name}{*тут должно быть имя модуля, а не id*}
					</td>
					
					<td class="billingPlans_bot">						
						{$currentCurrency.sign} 0.00					
					</td>
					
					{section name=j loop=$months}											
							<td class="billingPlans_bot">
							{assign var=curMonth value=$months[j]}
								<input type="radio" name="selectedModulePlan_{$module_id}"  value="{$plan.self.$curMonth.id}" {if 'self' neq $billingPlan.type} disabled {/if} onclick="setPrice('{$plan.self.$curMonth.price}', '{$module_id}', {$ids})">{$currentCurrency.sign} {$plan.self.$curMonth.price}																														
							</td>
					{/section}		
					{section name=j loop=$months}											
							<td class="billingPlans_bot">
							{assign var=curMonth value=$months[j]}
								<input type="radio" name="selectedModulePlan_{$module_id}"  value="{$plan.gyant.$curMonth.id}" {if 'gyant' neq $billingPlan.type} disabled {/if} onclick="setPrice('{$plan.gyant.$curMonth.price}', '{$module_id}', {$ids})">{$currentCurrency.sign} {$plan.gyant.$curMonth.price}																														
							</td>
					{/section}	
					<td class="billingPlans_bot">
                        <input type="button" value="X" onClick="deselectModule({$module_id},{$ids})" style="width:5px;" title="Clear selection">
					&nbsp;{if $plan.applied}/<input type="button" value="Delete" onclick="location.href='vps.php?category=billing&action=editCategory&subCategory=modules&module={$module_id}&total=delete&status=delete_all'" title="Delete all plans for module(deactivate module)">{/if}
					<input type="hidden" id="price_{$module_id}" value="0">
					</td>							
				</tr>
			{/foreach}
			
			<tr class="billingPlans">
				<td style="border-left:1px solid #fff;" class="billingPlans_bot" colspan="{$monthsCount*2+3}" align="center">You can choose modules only for your Billing Plan type(Self, Gyant) 
				</td>				
			</tr>
			<tr>
				<td colspan="{$monthsCount*2+2}" >
					<table>
						<tr>
							<td colspan="2">
								<input type="checkbox" name="bindToBP"  value="yes" disabled> Bind to the current Billing Plan
							</td>
							<td>&nbsp;</td>
							<td>Begin Date of module use<input type="text" name="startDate" value="{$date}"></td><td>&nbsp;</td>
				<td>Price<input id="price" type="text" value="" disabled></td>
						</tr>
					</table>
				</td>
				
				<td style="padding:15px 0 0 0">
					<input type="submit" value="Apply Modules" class="button">
				</td>
			</tr>	
		</table>
		
			<input type="hidden" name="category" value="billing">
			<input type="hidden" name="action" value="editCategory">
			<input type="hidden" name="subCategory" value="modules">
		
		
	</form>
	{*===========================================================================*}
{*/if*}
	
{*if !$newUserRegistration*}
	<br>
	<table border="0" width="100%" cellspacing="0" cellpadding="5" >
		<tr bgcolor="#ffffff">
			<td align="center"><b>Note!</b> {$billingPlan.limits.MSDS.increase_step} {$billingPlan.limits.MSDS.unit_type} Extra SDS price: {$currentCurrency.sign} {$billingPlan.limits.MSDS.increase_cost}</td>
			<td align="center"><b>Note!</b> {$billingPlan.limits.memory.increase_step} {$billingPlan.limits.memory.unit_type} Extra Memory Storage price: {$currentCurrency.sign} {$billingPlan.limits.memory.increase_cost}</td>
		</tr>		
		<tr class="billingPlans">
			<td width="50%" align="center" class="billingPlans_bot" style="border-left:1px solid #fff;">				
				<form action="vps.php" method="get">
					Extra SDS input 
					<select name="plusTo">						
						{section name=i loop=$list.MSDS}
							<option value="{$list.MSDS[i]}">+{$list.MSDS[i]} {$billingPlan.limits.MSDS.unit_type}</option>
						{/section}
					</select>
					
					<input type="hidden" name="action" value="editCategory">
					<input type="hidden" name="category" value="billing">
					<input type="hidden" name="subCategory" value="MSDSLimit">
					
					<input type="submit" value="Order">
				</form>				
			</td>
			<td align="center" class="billingPlans_bot">				
				<form action="vps.php" method="get">
					Extra Memory Storage
					<select name="plusTo">
						{section name=i loop=$list.memory}
							<option value="{$list.memory[i]}">+{$list.memory[i]} {$billingPlan.limits.memory.unit_type}</option>
						{/section}
					</select>
					
					<input type="hidden" name="action" value="editCategory">
					<input type="hidden" name="category" value="billing">
					<input type="hidden" name="subCategory" value="memoryLimit">
					
					<input type="submit" value="Order">				
			</td>
		</tr>	 
	</table>
	{*/if*}			
	       {*/shadow_table*}	
					         </td>
					          <td valign="top" class="billing_r_yellowgreen"></td>
			           </tr>
				        <tr>          
                             <td valign="top" class="billing_b_l_yellowgreen"></td>
                             <td valign="top" class="billing_b_yellowgreen"></td>
                             <td valign="top" class="billing_b_r_yellowgreen"></td>                           				
				       </tr>
		      </table>
		
		      {*/shadow_table*}	
<script type="text/javascript" src="modules/js/jquery-ui-1.8.2.custom/js/jquery-ui-1.8.2.custom.min.js"></script>
<link href="modules/js/jquery-ui-1.8.2.custom/css/smoothness/jquery-ui-1.8.2.custom.css" rel="stylesheet" type="text/css"/>

<script type="text/javascript">
    dateFormatJS = '{$dateFormatJS}';
    {literal}
    $(document).ready(function(){	
         $("input[name='startDate']").datepicker({ dateFormat: dateFormatJS }); 
    });
    {/literal}
</script>
		      <script type="text/javascript" src="modules/js/vps_modules.js"></script>
 
{else} <!-- NEW USER IS CHOISING NEW BILLING AND MODULES -->
	<center><h1>Choose your billing plan</h1></center>
	{include file='tpls:tpls/availableBillingPlansAndModules.tpl'}
		
		
{/if}
    
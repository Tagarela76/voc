<form action="vps.php" method="get">
<input type='hidden' name='currencyID' value="{$currentCurrency.id}"/>
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
						{$c.sign} {$sources[i].one_time_charge}					
					</td>
					
					{section name=j loop=$availablePlans}											
						{if $sources[i].bplimit eq $availablePlans[j].bplimit}
							<td class="billingPlans_bot">
								<input type="radio" name="selectedBillingPlan" onClick="billingRadioButtonClick('{$availablePlans[j].type}');"  value="{$availablePlans[j].billingID}" {if $availablePlans[j].billingID eq $billingPlan.billingID} checked {/if}>{$currentCurrency.sign} {$availablePlans[j].price}																														
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
				{if !$newUserRegistration}
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
				{/if}
				</td>
				<td style="padding:15px 0 0 0">
				{if !$newUserRegistration}
					<input type="submit" value="Change My Plan" class="button">
				{else}
					
				{/if}</td>
			</tr>	
		</table>
		
		{if !$newUserRegistration}
			<input type="hidden" name="category" value="billing">
			<input type="hidden" name="action" value="editCategory">
		{else}			
			<input type="hidden" name="action" value="second">
			<input type="hidden" name="category" value="myInfo">
		{/if}		
		
	
<table border="0"  width="100%"  cellspacing="0" cellpadding="5" >
			<tr class="billingPlans_hd">
				<td>MODULES</td>
				<td>ONE TIME SETUP CHARGE</td>
				<td id='tdSelf' colspan="3">SELF COMPLIANCE TRACKING & REPORTING SOLUTIONS</td>
				<td id='tdGyant' colspan="3" style="border-right:0px solid #fff;">GYANT COMPLIANCE TRACKING & REPORTING SOLUTIONS</td>
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
			<script type="text/javascript">
					var rbuttonsSelf = new Array();
					var rbuttonsGyant = new Array();
			</script>
			{assign var=counter value=0} 
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
							{assign var=counter value=$counter+1} 
							<script type="text/javascript">
								var rarr = new Array("radio"+{$counter},'{$plan.self.$curMonth.price}', '{$module_id}', {$ids});<!-- Collect button's ids and data for recount price -->
								
								rbuttonsSelf.push(rarr);
							</script>
								<input type="radio" id='radio{$counter}'  name="selectedModulePlan_{$module_id}"  value="{$plan.self.$curMonth.id}" onclick="setPrice('{$plan.self.$curMonth.price}', '{$module_id}', {$ids})" disabled=true>{$currentCurrency.sign} {$plan.self.$curMonth.price}																														
							</td>
					
					{/section}		
					{section name=j loop=$months}											
							<td class="billingPlans_bot">
							{assign var=curMonth value=$months[j]}
							{assign var=counter value=$counter+1}
							<script type="text/javascript">
								var rarr = new Array("radio"+{$counter},'{$plan.gyant.$curMonth.price}', '{$module_id}', {$ids});
								rbuttonsGyant.push(rarr);
							</script>
							<!-- name="selectedModulePlan_{$module_id}" -->
								<input type="radio" id='radio{$counter}' name="selectedModulePlan_{$module_id}"  value="{$plan.gyant.$curMonth.id}" onclick="setPrice('{$plan.gyant.$curMonth.price}', '{$module_id}', {$ids})" disabled=true>{$currentCurrency.sign} {$plan.gyant.$curMonth.price}																														
							</td>
					{/section}	
					<td class="billingPlans_bot"><input type="button" value="X" onClick="deselectModule({$module_id},{$ids})" style="width:5px;" title="Clear selection">
					&nbsp;{if $plan.applied}/<input type="button" value="Delete" onclick="location.href='vps.php?category=billing&action=editCategory&subCategory=modules&module={$module_id}&total=delete&status=delete_all'" title="Delete all plans for module(deactivate module)">{/if}
					<input type="hidden" id="price_{$module_id}" value="0">
					
					</td>							
				</tr>
			{/foreach}
			<script type="text/javascript">
					
			</script>
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
							<td>Begin Date of module use<input type="text" name="startDate" id="startDate" value="{$date}"></td><td>&nbsp;</td>
				<td><!--Price<input id="price" type="text" value="" disabled> -->
				 
				<span style='font-size:15px;'>Total modules price: {$currentCurrency.sign} <span id="price" style='font-weight:bold;'>0</span></span></td>
						</tr>
					</table>
				</td>
				
				<td style="padding:15px 0 0 0">
					
				</td>
			</tr>	
		</table>
		<div style='text-align:right;width:100%;'>
			<input type="submit" value="Apply Plan" class="button" id='btnSubmit' disabled=true>
		</div>
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

<script type="text/javascript" src="modules/js/jquery-ui-1.8.2.custom/js/jquery-ui-1.8.2.custom.min.js"></script>
<link href="modules/js/jquery-ui-1.8.2.custom/css/smoothness/jquery-ui-1.8.2.custom.css" rel="stylesheet" type="text/css"/>

<script type="text/javascript">
    dateFormatJS = '{$jsdateformat}';
    {literal}
    $(document).ready(function(){	
         $("#startDate").datepicker({ dateFormat: dateFormatJS }); 
    });
    {/literal}
</script>

		<script type="text/javascript" src="modules/js/vps_modules.js"></script>
		      {*/shadow_table*}	
</form>
{*fromRequest*}
{if $definedPlans.0.request_id}
<div><b>Clients performatives:</b></div>
<div>{$definedPlans.0.description}</div>
<div><b>Requst was send:</b></div>
<div>{$definedPlans.0.date}</div>
{/if}
 {*shadow_table*}	
	             <table cellspacing="0" cellpadding="0" align="center" width="100%">
                         <tr>
                               <td valign="top" class="report_uploader_t_l_orange"></td>
                               <td valign="top" class="report_uploader_t_orange"></td>
                               <td valign="top" class="report_uploader_t_r_orange"></td>
						</tr>
						  <tr>
							   <td valign="top" class="report_uploader_l_orange"></td>
                               <td valign="top" class="report_uploader_c_orange">
	           {*shadow_table*}
<form action="admin.php" method="GET">
<input type='hidden' name='currencyID' value={$curentCurrency.id} />
	<table border="0" cellspacing="0" cellpadding="0" width="100%" class="DefinedBillingPlanDetails_vps">
		<tr class="DefinedBillingPlanDetails_vps_tr" >
			<td colspan="2">
				Defined Billing Plan Details
			</td>
		</tr>
		
		{*edit*}
		{if $definedPlans.0.billingID}
		<tr >
			<td class="DefinedBillingPlanDetails_vps_tb">
				ID
			</td>
			<td>
				{$definedPlans.0.billingID}				
			</td>
		</tr>
		{/if}
		
		<tr >
			<td class=DefinedBillingPlanDetails_vps_td>
				Customer
			</td>
			<td>
			
			{*edit*}
			{if $definedPlans.0.customer_id}
				{$definedPlans.0.customerName}
				<input type="hidden" name="customerID" value="{$definedPlans.0.customer_id}">
			{*add*}			
			{else}
				<select name="customerID">
					{section name=i loop=$customersList}
						<option value="{$customersList[i].company_id}">{$customersList[i].name} {if $customersList[i].status == "exist" }({$customersList[i].status}){/if}</option>
					{/section}					
				</select>
			{/if}
			{if $problems.customerID}
				{*ERROR*}					
				<div style="width:80px;margin:2px 0px 0px 5px;" align="left"><img src='design/user/img/alert1.gif' height=16  style="float:left;">
				<font style="float:left;vertical-align:bottom;color:red;margin:1px 0px 0px 5px;">Error!</font></div>
				{*/ERROR*}
			{/if}
			</td>
		</tr>
		<tr  >
			<td class="DefinedBillingPlanDetails_vps_td">
				Emission sources
			</td>
			<td>
				<select name="bplimit">
					{section name=i loop=50}
					<option value="{$smarty.section.i.index+1}" {if $definedPlans.0.bplimit eq $smarty.section.i.index+1} selected {/if}>{$smarty.section.i.index+1}</option>
					{/section}
				</select>
				{if $problems.bplimit}
				{*ERROR*}					
				<div style="width:80px;margin:2px 0px 0px 5px;" align="left"><img src='design/user/img/alert1.gif' height=16  style="float:left;">
				<font style="float:left;vertical-align:bottom;color:red;margin:1px 0px 0px 5px;">Error!</font></div>
				{*/ERROR*}
				{/if}
			</td>			
		</tr>
		<tr >
			<td class="DefinedBillingPlanDetails_vps_td">
				Months
			</td>
			<td>				
				<select name="monthsCount">				
					{section name=i loop=36}
					<option value="{$smarty.section.i.index+1}" {if $definedPlans.0.months_count eq $smarty.section.i.index+1} selected {/if}>{$smarty.section.i.index+1}</option>
					{/section}
				</select>
				{if $problems.monthsCount}
				{*ERROR*}					
				<div style="width:80px;margin:2px 0px 0px 5px;" align="left"><img src='design/user/img/alert1.gif' height=16  style="float:left;">
				<font style="float:left;vertical-align:bottom;color:red;margin:1px 0px 0px 5px;">Error!</font></div>
				{*/ERROR*}
				{/if}
			</td>
		</tr>
		<tr>
			<td class="DefinedBillingPlanDetails_vps_td">
				One Time Setup Charge ({$curentCurrency.sign})
			</td>
			<td>
				<input type="text" name="oneTimeCharge" value="{$definedPlans.0.one_time_charge}">
				{if $problems.oneTimeCharge}
				{*ERROR*}					
				<div style="width:80px;margin:2px 0px 0px 5px;" align="left"><img src='design/user/img/alert1.gif' height=16  style="float:left;">
				<font style="float:left;vertical-align:bottom;color:red;margin:1px 0px 0px 5px;">Error!</font></div>
				{*/ERROR*}
				{/if}
			</td>
		</tr>
		<tr>
			<td class="DefinedBillingPlanDetails_vps_td">
				Cost ({$curentCurrency.sign})
			</td>
			<td>				
				<input type="text" name="price" value="{$definedPlans.0.price}">
				{if $problems.price}
				{*ERROR*}					
				<div style="width:80px;margin:2px 0px 0px 5px;" align="left"><img src='design/user/img/alert1.gif' height=16  style="float:left;">
				<font style="float:left;vertical-align:bottom;color:red;margin:1px 0px 0px 5px;">Error!</font></div>
				{*/ERROR*}
				{/if}
			</td>
		</tr>
		<tr>
			<td class="DefinedBillingPlanDetails_vps_td">
				Type
			</td>
			<td>
				<select name="type">					
					<option value="self" {if $definedPlans.0.type eq "self"} selected {/if}>Self Compliance & Reporting</option>
					<option value="gyant" {if $definedPlans.0.type eq "gyant"} selected {/if}>Gyant Compliance & Reporting</option>					
				</select>						
			</td>
		</tr>
		<tr>
			<td class="DefinedBillingPlanDetails_vps_td">
				SDS Default Limit
			</td>
			<td>				
				<input type="text" name="MSDSDefaultLimit" value="{$definedPlans.0.limits.MSDS.default_limit}"> {$definedPlans.0.MSDS.memory.unit_type}
				{if $problems.MSDSDefaultLimit}
				{*ERROR*}					
				<div style="width:80px;margin:2px 0px 0px 5px;" align="left"><img src='design/user/img/alert1.gif' height=16  style="float:left;">
				<font style="float:left;vertical-align:bottom;color:red;margin:1px 0px 0px 5px;">Error!</font></div>
				{*/ERROR*}
				{/if}		
			</td>
		</tr>
		<tr>
			<td class="DefinedBillingPlanDetails_vps_td">
				Extra SDS Cost ({$curentCurrency.sign})
			</td>
			<td>				
				<input type="text" name="MSDSIncreaseCost" value="{$definedPlans.0.limits.MSDS.increase_cost}">
				{if $problems.MSDSIncreaseCost}
				{*ERROR*}					
				<div style="width:80px;margin:2px 0px 0px 5px;" align="left"><img src='design/user/img/alert1.gif' height=16  style="float:left;">
				<font style="float:left;vertical-align:bottom;color:red;margin:1px 0px 0px 5px;">Error!</font></div>
				{*/ERROR*}
				{/if}		
			</td>
		</tr>
		<tr>
			<td class="DefinedBillingPlanDetails_vps_td">
				Memory Default Limit
			</td>
			<td>				
				<input type="text" name="memoryDefaultLimit" value="{$definedPlans.0.limits.memory.default_limit}"> {$definedPlans.0.limits.memory.unit_type}
				{if $problems.memoryDefaultLimit}
				{*ERROR*}					
				<div style="width:80px;margin:2px 0px 0px 5px;" align="left"><img src='design/user/img/alert1.gif' height=16  style="float:left;">
				<font style="float:left;vertical-align:bottom;color:red;margin:1px 0px 0px 5px;">Error!</font></div>
				{*/ERROR*}
				{/if}		
			</td>
		</tr>
		<tr>
			<td class="DefinedBillingPlanDetails_vps_td">
				Extra Memory Cost ({$curentCurrency.sign})
			</td>
			<td>				
				<input type="text" name="memoryIncreaseCost" value="{$definedPlans.0.limits.memory.increase_cost}">
				{if $problems.memoryIncreaseCost}
				{*ERROR*}					
				<div style="width:80px;margin:2px 0px 0px 5px;" align="left"><img src='design/user/img/alert1.gif' height=16  style="float:left;">
				<font style="float:left;vertical-align:bottom;color:red;margin:1px 0px 0px 5px;">Error!</font></div>
				{*/ERROR*}
				{/if}		
			</td>
		</tr>
	</table>
	
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
	
	<input type="submit" class="button" value="Save">
	<input type="button" class="button" value="Cancel" onClick="location.href='admin.php?action=vps&vpsAction=browseCategory&itemID=billing'">
	
	<input type="hidden" name="action" value="vps">
	<input type="hidden" name="vpsAction" value="{$vpsAction}">
	<input type="hidden" name="itemID" value="definedBillingPlans">
	<input type="hidden" name="requestID" value="{$definedPlans.0.request_id}">
</form>

	       {*/shadow_table*}	
					         </td>
					          <td valign="top" class="report_uploader_r_orange"></td>
			           </tr>
				        <tr>          
                             <td valign="top" class="report_uploader_b_l_orange"></td>
                             <td valign="top" class="report_uploader_b_orange"></td>
                             <td valign="top" class="report_uploader_b_r_orange"></td>                           				
				       </tr>
		      </table>
		
		      {*/shadow_table*}	

<script type="text/javascript" src="modules/js/checkBoxes.js"></script>


 <!--{*shadow_table*}	
	             <table cellspacing="0" cellpadding="0" align="center" width="100%">
                         <tr>
                               <td valign="top" class="report_uploader_t_l_violet"></td>
                               <td valign="top" class="report_uploader_t_violet"></td>
                               <td valign="top" class="report_uploader_t_r_violet"></td>
						</tr>
						  <tr>
							   <td valign="top" class="report_uploader_l_violet"></td>
                               <td valign="top" class="report_uploader_c_violet">
{*shadow_table*}-->
{*shadow_table*}	
	             <table cellspacing="0" cellpadding="0" align="center" width="100%">
                         <tr>
                               <td valign="top" class="report_uploader_t_l"></td>
                               <td valign="top" class="report_uploader_t"></td>
                               <td valign="top" class="report_uploader_t_r"></td>
						</tr>
						  <tr>
							   <td valign="top" class="report_uploader_l"></td>
                               <td valign="top" class="report_uploader_c">
	           {*shadow_table*}

{*----------------CUSTOMER_INFO---------------------------*}
<table align="center" border="0" width="100%" class="others_vps">	
	<tr class="other_vps_tr">
		<td colspan="2"><b>View Customer {$customer.name}</b></td>
	</tr>	
	<tr>
		<td class="other_vps_td">ID</td>
		<td>{$customer.id}</td>
	</tr>
	<tr>
		<td class="other_vps_td">Name</td>
		<td>{$customer.name}</td>
	</tr>
	<tr>
		<td class="other_vps_td">Contact Person</td>
		<td>{$customer.contactPerson}</td>
	</tr>
	<tr>
		<td class="other_vps_td">Phone</td>
		<td>{$customer.phone}</td>
	</tr>
	<tr>
		<td class="other_vps_td">E-mail</td>
		<td>{$customer.email}</td>
	</tr>
	<tr>
		<td class="other_vps_td">Billing Plan</td>
		<td>{$billingPlan.bplimit} sources {$billingPlan.months_count} months {$billingPlan.type}<input type="button" class="button" onclick="location.href='admin.php?action=vps&vpsAction=showEdit&itemID=customer&what2edit=billing&customerID={$customer.id}'" value="Edit"/></td>
	</tr>
	<tr>
		<td class="other_vps_td">Trial Period End Date</td>
		<td>{$customer.trial_end_date}</td>
	</tr>
	<tr>
		<td class="other_vps_td">Balance</td>
		<td>{$currencySign} {$customer.balance} <input type="button" class="button" onclick="location.href='admin.php?action=vps&vpsAction=showEdit&itemID=customer&what2edit=balance&customerID={$customer.id}'" value="Edit"/></td>
		
	</tr>
	<tr>
		<td class="other_vps_td">Due Date</td>
		<td>{$customer.period_end_date}</td>
	</tr>
	<tr>
		<td class="other_vps_td">Currency</td>
		<td>
		<form method="POST" action = "admin.php?action=vps&vpsAction=viewDetails&itemID=customer&customerID={$customer.id}">
			<select name='currencySelect'>
			{foreach from=$currencies item=c}
				{if $curentCurrency != $c.id}
					<option value='{$c.id}'>{$c.iso} &mdash; {$c.description}</option>
				{else}
					<option value='{$c.id}' selected>{$c.iso} &mdash; {$c.description}</option>
				{/if}
			{/foreach}
			</select>
			
			<input type='submit' value='Change Currency' name='btnChangeCurrency' class='button'/>
			{if $changeCurrencyStatus}
			<span style='color:Green;'>{$changeCurrencyStatus}</span>
			{/if}
		</form>
		</td>
	</tr>
	<tr>
		<td class="other_vps_td">Active</td>
		{if $customer.status == "on"}
			<td style="color:green;">yes<input type="button" class="button" onclick="location.href='admin.php?action=vps&vpsAction=showEdit&itemID=customer&what2edit=status&customerID={$customer.id}'" value="Edit"/></td>
		{elseif $customer.status == "off"}
			<td style="color:grey;">no<input type="button" class="button" onclick="location.href='admin.php?action=vps&vpsAction=showEdit&itemID=customer&what2edit=status&customerID={$customer.id}'" value="Edit"/></td>
		{/if}
	</tr>				
</table>

<br>

{*----------------CUSTOMER_MODULES---------------------------*}
<table align="center" border="0" width="100%"  class="others_vps">	
	<tr class="other_vps_tr">
		<td colspan="6"><b>Activated modules:</b></td>
	</tr>	
	<tr>
		<td width="20%">Module</td>
		<td width="10%">Period</td>
		<td width="10%">Start Date</td>
		<td width="10%">End Date</td>
		<td width="10%">Status</td>
		<td> &nbsp; </td>
	</tr>
	{if !$modules}
	<tr>
		<td colspan="6">No active modules</td>
	</tr>
	{else}
	{foreach from=$modules item=module}
	<tr style="height:10px;">
		<td style="font-style:italic;font-weight:bold;" colspan="4">{$module.module_name}&nbsp;</td>
		<td>{$module.status}&nbsp;</td>
		<td><input type="button" value="Delete All Plans" onClick="location.href='admin.php?action=vps&vpsAction=showEdit&itemID=modules&module={$module.module_id}&customerID={$customer.id}&status=remove_all'">&nbsp;{*to remove all plans for module*}</td>
		
	</tr>
		{foreach from=$module.plans item=module_plan}
			<tr style="height:5px;">
				<td style="padding-left:15px;">{$module.module_name}&nbsp;</td>
				<td>{$module_plan.period}&nbsp;</td>
				<td>{$module_plan.start}&nbsp;</td>
				<td>{$module_plan.end}&nbsp;</td>
				<td>{$module_plan.status}&nbsp;</td>
				<td><input type="button" value="Delete" onClick="location.href='admin.php?action=vps&vpsAction=showEdit&itemID=modules&module={$module.module_id}&plan={$module_plan.id}&customerID={$customer.id}&status=remove_plan'">&nbsp;</td>
			</tr>
		{/foreach}
	{/foreach}
	{/if}		
</table>

<table align="center" border="0" width="100%" class="others_vps">	
	<tr class="other_vps_tr">
		<td colspan="2"><b>Trial/Bonus modules(not purchased, switched on):</b></td>
	</tr>	
	{if $bonusModules}
		{foreach from=$bonusModules item=module}
			{if $module}
			<tr>
				<td style="width:20%;font-style:italic;">
				{$module}
				</td>
				<td>&nbsp;</td>
			</tr>
			{/if}
		{/foreach}
	{else}
	<tr>
		<td colspan="2">
		No bonus/trial modules
		</td>
	</tr>
	{/if}
	<tr>
	<td>&nbsp</td>
	<td><input type="button" class='button' value="Go to Modules Admin" onClick="location.href='admin.php?action=browseCategory&category=modulars'"></td>
	</tr>
</table>

<br>

{*----------------CUSTOMER_INVOICES---------------------------*}
<form name="invoice" action="">
<table align="center" border="0" width="100%">	
	<tr>
		<td>
			<H3 STYLE="FONT-SIZE:14PX;margin:5px 0 3px 0" >
				Select: 
				<a onclick="CheckAll(this)" class="id_company1" >All</a>									
	 			/
				<a onclick="unCheckAll(this)" class="id_company1">None</a>
				<input type="submit" class="button" name="invoiceAction" value="Edit"/>				
			</H3>					
		</td>	
		<td align="right">			
			<input type="radio" name="invoiceType" value="custom" checked> Custom
			<input type="radio" name="invoiceType" value="limit"> For Limit Increase
			<input type="radio" name="invoiceType" value="module"> Manage Modules			
			<input type="submit" class="button" name="invoiceAction" value="Add"/>	
		</td>
	</tr>					
</table>			
		
<table align="center" border="0" width="100%" class="others_vps">	
	<tr class="other_vps_tr">
		<td colspan="13"><b>Invoices</b></td>
	</tr>	
	<tr>
		<td>&nbsp;</td>
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
	</tr>
	{section name=i loop=$invoices}
			<tr height="20" class="hov_company_vps">
				<td class="border_users_l border_users_b border_users_r" >				
					<div align="left">{if $invoices[i].editable == "yes"}
											<input type="checkbox" name="invoice_{$smarty.section.i.index}" value="{$invoices[i].invoiceID}"></div>
									  {else}&nbsp;
									  {/if}
				</td>
				<td class="border_users_l border_users_b border_users_r" >
				<a href="admin.php?action=vps&vpsAction=viewDetails&itemID=invoice&invoiceID={$invoices[i].invoiceID}"><div align="left">&nbsp;{$invoices[i].invoiceID}</div></a>
				</td>
				<td class="border_users_r border_users_b">
				<a href="admin.php?action=vps&vpsAction=viewDetails&itemID=invoice&invoiceID={$invoices[i].invoiceID}"><div align="left">&nbsp;{$invoices[i].sign}{$invoices[i].oneTimeCharge}</div></a>
				</td>
				<td class="border_users_r border_users_b">
				<a href="admin.php?action=vps&vpsAction=viewDetails&itemID=invoice&invoiceID={$invoices[i].invoiceID}"><div align="left">&nbsp;{$invoices[i].sign}{$invoices[i].amount}</div></a>
				</td>
				<td class="border_users_r border_users_b">
				<a href="admin.php?action=vps&vpsAction=viewDetails&itemID=invoice&invoiceID={$invoices[i].invoiceID}"><div align="left">&nbsp;{$invoices[i].sign}{$invoices[i].discount}</div></a>
				</td>
				<td class="border_users_r border_users_b">
				<a href="admin.php?action=vps&vpsAction=viewDetails&itemID=invoice&invoiceID={$invoices[i].invoiceID}"><div align="left">&nbsp;{$invoices[i].sign}{$invoices[i].total}</div></a>
				</td>
				<td class="border_users_r border_users_b">
				<a href="admin.php?action=vps&vpsAction=viewDetails&itemID=invoice&invoiceID={$invoices[i].invoiceID}"><div align="left">&nbsp;{$invoices[i].sign}{$invoices[i].paid}</div></a>
				</td>
				<td class="border_users_r border_users_b">
				<a href="admin.php?action=vps&vpsAction=viewDetails&itemID=invoice&invoiceID={$invoices[i].invoiceID}"><div align="left">&nbsp;{$invoices[i].sign}{$invoices[i].due}</div></a>
				</td>
				<td class="border_users_r border_users_b">
				<a href="admin.php?action=vps&vpsAction=viewDetails&itemID=invoice&invoiceID={$invoices[i].invoiceID}"><div align="left" >&nbsp;{$invoices[i].generationDate}</div></a>
				</td>
				{if $invoices[i].limitInfo != null}
					<td class="border_users_r border_users_b" colspan="2">				
						<a href="admin.php?action=vps&vpsAction=viewDetails&itemID=invoice&invoiceID={$invoices[i].invoiceID}"><div align="center" >[Invoice for extra limit]</div></a>
					</td>
				{elseif $invoices[i].customInfo != null}
					<td class="border_users_r border_users_b" colspan="2">				
						<div align="center" >[Custom Invoice]</div>
					</td>
				{elseif $invoices[i].moduleID != null}
					<td class="border_users_r border_users_b" colspan="2">				
						<div align="center" >[Invoice for Module]</div>
					</td>								
				{elseif $invoices[i].billingInfo == null && $invoices[i].limitInfo == null && $invoices[i].customInfo == null && $invoices[i].moduleID == null}
					<td class="border_users_r border_users_b" colspan="2">				
						<div align="center" >[Manual balance change]</div>
					</td>				
				{else}
					<td class="border_users_r border_users_b">				
					<a href="admin.php?action=vps&vpsAction=viewDetails&itemID=invoice&invoiceID={$invoices[i].invoiceID}"><div align="left" >&nbsp;{$invoices[i].periodStartDate}</div></a>
					</td>
					<td class="border_users_r border_users_b">
					<a href="admin.php?action=vps&vpsAction=viewDetails&itemID=invoice&invoiceID={$invoices[i].invoiceID}"><div align="left" >&nbsp;{$invoices[i].periodEndDate}</div></a>
					</td>
				{/if}
				<td class="border_users_r border_users_b">
				<a href="admin.php?action=vps&vpsAction=viewDetails&itemID=invoice&invoiceID={$invoices[i].invoiceID}"><div align="left" >&nbsp;{$invoices[i].suspensionDate}</div></a>
				</td>
				<td class="border_users_r border_users_b">
				<a href="admin.php?action=vps&vpsAction=viewDetails&itemID=invoice&invoiceID={$invoices[i].invoiceID}"><div align="left" {if $invoices[i].status == "DUE"}style="color:red"{elseif $invoices[i].status == "PAID"}style="color:green"{else}style="color:blue"{/if}>&nbsp;{$invoices[i].status}</div></a>
				</td>
	{/section}
</table>

<input type="hidden" name="action" value="vps">
<input type="hidden" name="vpsAction" value="manageInvoice">
<input type="hidden" name="customerID" value="{$customer.id}">
{*<input type="hidden" name="invoiceCount" value="{$invoiceCount}">*}

</form>


	<!--        {*/shadow_table*}	
					         </td>
					          <td valign="top" class="report_uploader_r_violet"></td>
			           </tr>
				        <tr>          
                             <td valign="top" class="report_uploader_b_l_violet"></td>
                             <td valign="top" class="report_uploader_b_violet"></td>
                             <td valign="top" class="report_uploader_b_r_violet"></td>                           				
				       </tr>
		      </table>
		
		      {*/shadow_table*}-->
		      {*/shadow_table*}	
					         </td>
					          <td valign="top" class="report_uploader_r"></td>
			           </tr>
				        <tr>          
                             <td valign="top" class="report_uploader_b_l"></td>
                             <td valign="top" class="report_uploader_b"></td>
                             <td valign="top" class="report_uploader_b_r"></td>                           				
				       </tr>
		      </table>
		
		      {*/shadow_table*}		
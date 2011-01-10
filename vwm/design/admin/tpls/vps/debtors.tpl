{if $action == "viewDetails"}
	{include file="tpls:tpls/vps/viewCustomer.tpl"}
{elseif $action == "showEdit"}
	{include file="tpls:tpls/vps/editCustomer.tpl"}
{elseif $action == "viewInvoice"}
	{include file="tpls:tpls/vps/invoiceDetails.tpl"}
{elseif $action == "showAddInvoice"}
	{include file="tpls:tpls/vps/showAddInvoice.tpl"}
{elseif $action == "showEditInvoice"}
	{include file="tpls:tpls/vps/editInvoice.tpl"}
{elseif $action == "notRegisteredCustomer"}
	{include file="tpls:tpls/vps/editNotRegisteredCustomer.tpl"}
{else}
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
<table align="center" border="0" width="100%" celpadding="0" cellspacing=0>
	<tr>
		<td colspan="6">Customers</td>
	</tr>
	{if $customers}
	<tr>
		<td>ID</td>
		<td>Name</td>
		<td>Phone</td>
		<td>Current Billing Plan</td>
		<td>Balance</td>
		<td>Active</td>
	</tr>
		{section name=i loop=$customers}
    	<tr class="hov_company_discounts" onClick="location.href='admin.php?action=vps&vpsAction=viewDetails&itemID=customer&customerID={$customers[i].id}';">
	        <td>{$customers[i].id}</td>
        	<td>{$customers[i].name}</td>
        	<td>{$customers[i].phone}</td>
        	<td>{$billingPlans[i].bplimit} sources {$billingPlans[i].months_count} months {$billingPlans[i].type}</td>
        	<td>$ {$customers[i].balance}</td>
        	<td>{if $customers[i].status == "on"}yes{elseif $customers[i].status == "off"}no{/if}</td>        	 
    	</tr>
    	{/section}
	{else}
	<tr>
        <td colspan="6">No Customers Registered in VPS</td>
    </tr>
	{/if}
</table>
<br>
<table align="center" border="0" width="100%" celpadding="0" cellspacing=0>
	<tr>
		<td colspan="5">Not Registered Customers</td>
	</tr>
	{if $notRegisteredCustomers}
	<tr>
		<td>ID</td>
		<td>Name</td>
		<td>Phone</td>
		<td>Email</td>
		<td>Trial Period Ends</td>
	</tr>
		{section name=i loop=$notRegisteredCustomers}
    	<tr class="hov_company_biling" onClick="location.href='admin.php?action=vps&vpsAction=viewDetails&itemID=notRegisteredCustomer&customerID={$notRegisteredCustomers[i].company_id}';">
        	<td>{$notRegisteredCustomers[i].company_id}</td>
        	<td>{$notRegisteredCustomers[i].name}</td>
        	<td>{$notRegisteredCustomers[i].phone}</td>
        	<td>{$notRegisteredCustomers[i].email}</td>     	 
        	<td>{$notRegisteredCustomers[i].trial_end_date}</td>
    	</tr>
    	{/section}
	{else}
	<tr>
        <td colspan="4">No Customers</td>
    </tr>
	{/if}
</table>
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
{/if}
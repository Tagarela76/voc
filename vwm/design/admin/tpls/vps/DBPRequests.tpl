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
<table border="0" align="center" width="100%"cellspacing="1" cellpadding="5">
	<tr class="billingPlans_hd_orange" >
		<td colspan="6">Requests for defined Billing Plans</td>
	</tr>
	<tbody>
	<tr height="27" bgcolor="#ecb57f">
		<td >ID</td>
		<td>Customer</td>
		<td>Emission Source</td>
		<td>Months</td>
		<td>Type</td>
		<td>Date</td>		
	</tr>
	{section name=i loop=$requestList}		
	<tr {if $requestList[i].status == "unprocessed"} style="color:red;"{/if} onclick="location.href='admin.php?action=vps&vpsAction=showAddItem&itemID=definedBillingPlans&requestID={$requestList[i].id}';" bgcolor="#F2E5D3" class="hov_company_biling">				
		<td class="billingPlans_bot" style="border-left:1px solid #fff;">{$requestList[i].id}</td>
		<td class="billingPlans_bot">{$requestList[i].customerName}</td>
		<td class="billingPlans_bot">{$requestList[i].bplimit}</td>
		<td class="billingPlans_bot">{$requestList[i].monthsCount}</td>
		<td class="billingPlans_bot">{$requestList[i].type}</td>
		<td class="billingPlans_bot">{$requestList[i].date}</td>
	</tr>
	{/section}	
	</tbody>		
</table>
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
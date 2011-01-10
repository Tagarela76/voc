<table class="users"  align="center" cellpadding="0" cellspacing="0" style="width:100%;">
		<tr>
			<td class="border_users_l border_users_b border_users_r" >InvNum</td>
			<td class="border_users_r border_users_b">Total</td>					
			<td class="border_users_r border_users_b">Suspension Date</td>
		</tr>	

		<tr height="20" class="hov_company_vps">
			<td class="border_users_l border_users_b border_users_r" >
				<div align="left">#{$invoice.invoiceID}</div>
			</td>
			<td class="border_users_r border_users_b border_users_r">
				<div align="left"><b>{$invoice.total}</b>$</div>
			</td>
			<td class="border_users_r border_users_b">
				<div align="left">{$invoice.suspensionDate}</div>
			</td>
			</td>
		</tr>
											
		<tr>
           <td  height="15" class="users_u_bottom">
		   </td>
		   <td class="border_users">
		   </td>
		     <td height="15" class="users_u_bottom_r">
		   </td>
		</tr>
</table>
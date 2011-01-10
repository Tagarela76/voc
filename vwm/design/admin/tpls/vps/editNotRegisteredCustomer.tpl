
<table align="center" border="1" width="80%">	
	<tr>
		<td colspan="2">View Not Registered Customer {$customer.company_id}</td>
	</tr>	
	<tr>
		<td>ID</td>
		<td>{$customer.company_id}</td>
	</tr>
	<tr>
		<td>Name</td>
		<td>{$customer.name}</td>
	</tr>
	<tr>
		<td>Contact Person</td>
		<td>{$customer.contact}</td>
	</tr>
	<tr>
		<td>Phone</td>
		<td>{$customer.phone}</td>
	</tr>
	<tr>
		<td>E-mail</td>
		<td>{$customer.email}</td>
	</tr>
	<tr>
		<td>Trial Period End Date</td>
		<td>{$customer.trial_end_date}{*<input type="button" class="button" onclick="location.href='admin.php?action=vps&vpsAction=showEdit&itemID=notRegisteredCustomer&what2edit=trialPeriodEnd&customerID={$customer.company_id}'" value="Edit"/>*}</td>
	</tr>
	<tr align="right">
		<td colspan="2"><input type="button" class="button" onclick="location.href='admin.php?action=vps&vpsAction=editItem&itemID=notRegisteredCustomer&what2edit=register&customerID={$customer.company_id}'" value="Register"/></td>
	</tr>
</table>

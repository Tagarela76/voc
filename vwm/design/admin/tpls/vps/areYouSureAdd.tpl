
<div style="padding:7px;">

<form action="admin.php?action=vps&vpsAction=confirmAdd&itemID=definedBillingPlans" method="post">

		<table class="users" align="center" cellpadding="0" cellspacing="0">
			<tr class="users_top_yellowgreen" >
				<td class="users_u_top_red" height="27">
					<span >
					{*--header-constructor--*}					
						Are you sure you want to add new Billing Plan ?																				
					{*----------------------*}
					</span>
				</td>
				<td class="users_u_top_r_red">
				</td>
			</tr>
			
			
							
					<tr height="20" class="hov_company">
						<td class="border_users_l border_users_b border_users_r" width="25%">
							Company (ID)
						</td>						
						<td class="border_users_r border_users_b">
							{$newPlan.customerID}&nbsp;
							<input type="hidden" name="customerID" value="{$newPlan.customerID}">							
						</td>
					</tr>
					<tr height="20" class="hov_company">
						<td class="border_users_l border_users_b border_users_r">
							Emission Sources
						</td>						
						<td class="border_users_r border_users_b">
							{$newPlan.bplimit}&nbsp;
							<input type="hidden" name="bplimit" value="{$newPlan.bplimit}">
						</td>
					</tr>
					<tr height="20" class="hov_company">
						<td class="border_users_l border_users_b border_users_r">
							Months
						</td>						
						<td class="border_users_r border_users_b">
							{$newPlan.monthsCount}&nbsp;						
							<input type="hidden" name="monthsCount" value="{$newPlan.monthsCount}">	
						</td>
					</tr>
					<tr height="20" class="hov_company">
						<td class="border_users_l border_users_b border_users_r">
							One Time Setup Charge
						</td>						
						<td class="border_users_r border_users_b">
							$ {$newPlan.oneTimeCharge}&nbsp;
							<input type="hidden" name="oneTimeCharge" value="{$newPlan.oneTimeCharge}">							
						</td>
					</tr>
					<tr height="20" class="hov_company">
						<td class="border_users_l border_users_b border_users_r">
							Cost
						</td>						
						<td class="border_users_r border_users_b">
							$ {$newPlan.price}&nbsp;
							<input type="hidden" name="price" value="{$newPlan.price}">							
						</td>
					</tr>
					<tr height="20" class="hov_company">
						<td class="border_users_l border_users_b border_users_r">
							Type
						</td>						
						<td class="border_users_r border_users_b">
							{$newPlan.type}&nbsp;
							<input type="hidden" name="type" value="{$newPlan.type}">							
						</td>
					</tr>
					<tr height="20" class="hov_company">
						<td class="border_users_l border_users_b border_users_r">
							MSDS Default Limit 
						</td>						
						<td class="border_users_r border_users_b">
							{$newPlan.MSDSDefaultLimit}
							<input type="hidden" name="MSDSDefaultLimit" value="{$newPlan.MSDSDefaultLimit}">							
						</td>
					</tr>															
					<tr height="20" class="hov_company">
						<td class="border_users_l border_users_b border_users_r">
							Extra MSDS Cost 
						</td>						
						<td class="border_users_r border_users_b">
							$ {$newPlan.MSDSIncreaseCost}
							<input type="hidden" name="MSDSIncreaseCost" value="{$newPlan.MSDSIncreaseCost}">							
						</td>
					</tr>
					<tr height="20" class="hov_company">
						<td class="border_users_l border_users_b border_users_r">
							Memory Default Limit 
						</td>						
						<td class="border_users_r border_users_b">
							{$newPlan.memoryDefaultLimit}
							<input type="hidden" name="memoryDefaultLimit" value="{$newPlan.memoryDefaultLimit}">							
						</td>
					</tr>
					<tr height="20" class="hov_company">
						<td class="border_users_l border_users_b border_users_r">
							Extra Memory Cost 
						</td>						
						<td class="border_users_r border_users_b">
							$ {$newPlan.memoryIncreaseCost}
							<input type="hidden" name="memoryIncreaseCost" value="{$newPlan.memoryIncreaseCost}">							
						</td>
					</tr>
				
					
					<tr>
						<td  height="25" class="users_u_bottom">							
						</td>
						<td height="25" class="users_u_bottom_r">							
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
					<input type="button" class="button" value="No" onClick="location.href='admin.php?action=vps&vpsAction=browseCategory&itemID=billing'">
				</td>
			</tr>
		</table>
						
		<input type="hidden" name="itemCount" value="{$itemCount}">		
		<input type="hidden" name="requestID" value="{$requestID}">
		<input type="hidden" name="applyWhen" value="{$newPlan.applyWhen}">
</form>		

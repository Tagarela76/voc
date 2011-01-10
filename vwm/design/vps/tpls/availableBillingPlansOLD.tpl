{if !$newUserRegistration}
	{include file="tpls:bookmarks_vps.tpl" }
{else}
	<center><h1>Choose your billing plan</h1></center>
{/if}

 {*shadow_table*}	
	             <table cellspacing="0" cellpadding="0" align="center" >
                         <tr>
                               <td valign="top" class="report_uploader_t_l_yellowgreen"></td>
                               <td valign="top" class="report_uploader_t_yellowgreen"></td>
                               <td valign="top" class="report_uploader_t_r_yellowgreen"></td>
						</tr>
						  <tr>
							   <td valign="top" class="report_uploader_l_yellowgreen"></td>
                               <td valign="top" class="report_uploader_c_yellowgreen">
	           {*shadow_table*}

	<form action="vps.php" method="get">
		<table border="0"  width="100%"  cellspacing="0" cellpadding="0" >
			<tr>
				<td>
					&nbsp;
				</td>
				{section name=i loop=$months}
					<td  class="dashboard" style="padding:0 10px 0 0">
						{$months[i]} months
					</td>
				{/section}				
			</tr>
			{section name=i loop=$users}
				<tr>
					<td  class="dashboard_min pcenter">
						{if $users[i] eq 0}
							Unlimited users count
						{elseif $users[i] eq 1}
							{$users[i]} user
						{else}
							{$users[i]} users
						{/if}
					</td>
					{section name=j loop=$availablePlans}
						{if $users[i] eq $availablePlans[j].userCount}
							<td  class="pcenter" >
								<input type="radio" name="selectedBillingPlan"  value="{$availablePlans[j].billingID}" {if $availablePlans[j].billingID eq $billingPlan.billingID} checked {/if}>${$availablePlans[j].price}
							</td>
						{/if}
					{/section}
				</tr>
			{/section}
			<tr>
				<td colspan="{$monthsCount}"></td>
				<td style="padding:15px 0 0 0">
				
				{if !$newUserRegistration}
					<input type="submit" value="Change My Plan" class="button_120">
				{else}
					<input type="submit" value="Apply Plan" class="button_120">
				{/if}
									
				</td>
			</tr>
		</table>
		
		{if !$newUserRegistration}
			<input type="hidden" name="category" value="billing">
			<input type="hidden" name="action" value="editCategory">
		{else}			
			<input type="hidden" name="action" value="addUser">
			<input type="hidden" name="step" value="second">
		{/if}		
		
	</form>
	       {*/shadow_table*}	
					         </td>
					          <td valign="top" class="report_uploader_r_yellowgreen"></td>
			           </tr>
				        <tr>          
                             <td valign="top" class="report_uploader_b_l_yellowgreen"></td>
                             <td valign="top" class="report_uploader_b_yellowgreen"></td>
                             <td valign="top" class="report_uploader_b_r_yellowgreen"></td>                           				
				       </tr>
		      </table>
		
		      {*/shadow_table*}	
	
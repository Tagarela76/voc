	{if $color eq "green"}
		{include file="tpls:tpls/notify/greenNotify.tpl" text=$message}
	{/if}
	
	{if $color eq "orange"}
		{include file="tpls:tpls/notify/orangeNotify.tpl" text=$message}
	{/if}
	
	{if $color eq "blue"}
		{include file="tpls:tpls/notify/blueNotify.tpl" text=$message}
	{/if}
<div style="padding:7px;">
		<table class="users" align="center" cellpadding="0" cellspacing="0">
			 <tr class="users_top_yellowgreen users_u_top_size">
				<td class="users_u_top_yellowgreen" width="27%">
					<span >View details</span>
				</td>
				<td class="users_u_top_r_yellowgreen" width="300">
					&nbsp;
				</td>				
			</tr>					
						
						<tr>
							<td class="border_users_l border_users_b" height="20">
								Agency ID:
							</td>
							<td class="border_users_l border_users_b border_users_r">
								<div align="left" >&nbsp;{$agency.agency_id}</div>							
							</td>
						</tr>
						
						<tr>
							<td class="border_users_l border_users_b" height="20">
								Agency name US:
							</td>
							<td class="border_users_l border_users_b border_users_r">
							<div align="left" >	&nbsp;{$agency.name_us}
							</div>						
							</td>
						</tr>
						
						<tr>
							<td class="border_users_l border_users_b" height="20">
								Agency name EU:
							</td>
							<td class="border_users_l border_users_b border_users_r">
							<div align="left" >	&nbsp;{$agency.name_eu}
							</div>						
							</td>
						</tr>
						
						<tr>
							<td class="border_users_l border_users_b" height="20">
								Agency name China:
							</td>
							<td class="border_users_l border_users_b border_users_r">
							<div align="left" >	&nbsp;{$agency.name_cn}
							</div>						
							</td>
						</tr>
						<tr>
							<td class="border_users_l border_users_b" height="20">
								Description:
							</td>
							<td class="border_users_l border_users_b border_users_r">
							<div align="left" >	&nbsp;{$agency.description}
							</div>						
							</td>
						</tr>	
						
						<tr>
							<td class="border_users_l border_users_b" height="20">
								Country:
							</td>
							<td class="border_users_l border_users_b border_users_r">
							<div align="left" >	&nbsp;{$agency.country}
							</div>						
							</td>
						</tr>	
						
						<tr>
							<td class="border_users_l border_users_b" height="20">
								Location:
							</td>
							<td class="border_users_l border_users_b border_users_r">
							<div align="left" >	&nbsp;{$agency.location}
							</div>						
							</td>
						</tr>	
						
						<tr>
							<td class="border_users_l border_users_b" height="20">
								Contact Information:
							</td>
							<td class="border_users_l border_users_b border_users_r">
							<div align="left" >	&nbsp;{$agency.contact_info}
							</div>						
							</td>
						</tr>						
						
						<tr>
             				 <td height="20" class="users_u_bottom">
             	 				&nbsp;
                			 </td>
                			 <td height="20" class="users_u_bottom_r">
                 				&nbsp;
                 			</td>
           				</tr>
			</table>
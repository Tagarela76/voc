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
								1.	Substrate ID :
							</td>
							<td class="border_users_l border_users_b border_users_r">
								<div align="left" >
									&nbsp;{$substrate.substrate_id}
								</div>							
							</td>
						</tr>
						
						<tr>
							<td class="border_users_l border_users_b" height="20">
								2.	Description :
							</td>
							<td class="border_users_l border_users_b border_users_r">
								<div align="left" >	&nbsp;{$substrate.substrate_desc}</div>							
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
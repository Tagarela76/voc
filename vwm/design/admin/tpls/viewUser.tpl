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
								User ID
							</td>
							<td class="border_users_l border_users_b border_users_r">
								<div align="left" >&nbsp;{$user.user_id}</div>
							
								
							</td>
						</tr>														
						
						<tr>
							<td class="border_users_l border_users_b" height="20">
								Accessname
							</td>
							<td class="border_users_l border_users_b border_users_r">
								<div align="left" >	&nbsp;{$user.accessname}</div>						
							</td>
						</tr>												
						
						<tr>
							<td class="border_users_l border_users_b" height="20">
								Username
							</td>
							<td class="border_users_l border_users_b border_users_r">
								<div align="left" >	&nbsp;{$user.username}</div>						
							</td>
						</tr>
							
						<tr>
							<td class="border_users_l border_users_b" height="20">
								Phone
							</td>
							<td class="border_users_l border_users_b border_users_r">
								<div align="left" >	&nbsp;{$user.phone}</div>						
							</td>
						</tr>
						
						<tr>
							<td class="border_users_l border_users_b" height="20">
								Mobile
							</td>
							<td class="border_users_l border_users_b border_users_r">
								<div align="left" >	&nbsp;{$user.mobile}</div>						
							</td>
						</tr>					
						
						<tr>
							<td class="border_users_l border_users_b" height="20">
								E-mail
							</td>
							<td class="border_users_l border_users_b border_users_r">
								<div align="left" >	&nbsp;{$user.email}</div>						
							</td>
						</tr>
						
						<tr>
							<td class="border_users_l border_users_b" height="20">
								Access level
							</td>
							<td class="border_users_l border_users_b border_users_r">
								<div align="left" >	&nbsp;{$user.accesslevel_id}</div>						
							</td>
						</tr>																														
						
						<tr>
							<td class="border_users_l border_users_b" height="20">
								Start point
							</td>
							<td class="border_users_l border_users_b border_users_r">
								<div align="left" >	&nbsp;{$user.startPoint}</div>						
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
	</div>
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
								<div align="left" >&nbsp;{$user.user_id|escape}</div>
							
								
							</td>
						</tr>														
						
						<tr>
							<td class="border_users_l border_users_b" height="20">
								Accessname*
							</td>
							<td class="border_users_l border_users_b border_users_r">
								<div align="left" >	&nbsp;{$user.accessname|escape}</div>						
							</td>
						</tr>												
						
						<tr>
							<td class="border_users_l border_users_b" height="20">
								Username**
							</td>
							<td class="border_users_l border_users_b border_users_r">
								<div align="left" >	&nbsp;{$user.username|escape}</div>						
							</td>
						</tr>
							
						<tr>
							<td class="border_users_l border_users_b" height="20">
								Phone
							</td>
							<td class="border_users_l border_users_b border_users_r">
								<div align="left" >	&nbsp;{$user.phone|phone_format|escape}</div>						
							</td>
						</tr>
						
						<tr>
							<td class="border_users_l border_users_b" height="20">
								Mobile
							</td>
							<td class="border_users_l border_users_b border_users_r">
								<div align="left" >	&nbsp;{$user.mobile|phone_format|escape}</div>						
							</td>
						</tr>					
						
						<tr>
							<td class="border_users_l border_users_b" height="20">
								E-mail
							</td>
							<td class="border_users_l border_users_b border_users_r">
								<div align="left" >	&nbsp;{$user.email|escape}</div>						
							</td>
						</tr>
						
						<tr>
							<td class="border_users_l border_users_b" height="20">
								Access level
							</td>
							<td class="border_users_l border_users_b border_users_r">
								<div align="left" >	&nbsp;{$user.accesslevel_id|escape}</div>						
							</td>
						</tr>																														
						
						{if $user.accesslevel_id neq "Superuser level (Admin)" and $user.accesslevel_id neq "Sales level"}
						<tr>
							<td class="border_users_l border_users_b" height="20">
								Start point
							</td>
							<td class="border_users_l border_users_b border_users_r">
								<div align="left" >	&nbsp;{$user.startPoint|escape}</div>
							</td>
						</tr>																									
						{/if}
						
						<tr>
							<td height="20" class="users_u_bottom">
             	 				&nbsp;
                			 </td>
                			 <td height="20" class="users_u_bottom_r">
                 				&nbsp;
                 			</td>
						</tr>
			</table>
<br />* You'll use this to log in to the voc web manager. Other user will no see your access name	
<br />** This is your real name. We'll can print it on some reports or show it to other users						
	</div>

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
								Case Number:
							</td>
							<td class="border_users_l border_users_b border_users_r">
								<div align="left" >&nbsp;{$components.cas}</div>								
							</td>
						</tr>
						
						<tr>
							<td class="border_users_l border_users_b" height="20">
								EC Number:
							</td>
							<td class="border_users_l border_users_b border_users_r">
								<div align="left" >&nbsp;{$components.EINECS}</div>								
							</td>
						</tr>
						
						<tr>
							<td class="border_users_l border_users_b" height="20">
								Description :
							</td>
							<td class="border_users_l border_users_b border_users_r">
							<div align="left" >	&nbsp;{$components.description}</div>
						
								
							
							</td>
						</tr>
						
						
						<tr>
							<td class="border_users_l border_users_r" colspan='2' height="20">
								<br>
								<table width="100%" cellpadding="0" cellspacing="0" >
								 	<tr class="users_top_lightgray">
										<td class="users_u_top_lightgray" height="25px" width="27%">
											Agencies:
										</td>
										<td class="users_u_top_r_lightgray" >
											&nbsp;
										</td>												
									</tr>											 
										 
										{section name=agency loop=$components.agencies}
										<tr>
											<td class="border_users_l border_users_b" height="20">
												{if $components.agencies[agency].control=="yes"}
													<b>{$components.agencies[agency].name}</b>
												{else}
													{$components.agencies[agency].name}
												{/if}
											</td>
											<td class="border_users_l border_users_b border_users_r">
											<div align="left"> &nbsp;
												{if $components.agencies[agency].control=="yes"}
													<b>yes</b>
												{else}
													no
												{/if}
											</div>
											</td>
										</tr>
										{/section}				 
									
									{if $smarty.section.agency.total == 0}								
									{*BEGIN	EMPTY LIST*}
									<tr>
										<td class="border_users_l border_users_b border_users_r" colspan='2' height="20" align='center'>
											<b>No agencies</b>
										</td>
									</tr>
									{*END	EMPTY LIST*}
									{/if}								
								</table>
							<!--</td>
							<td class="border_users_l border_users_b border_users_r">
							<div align="left" ></div>
							</td>-->
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
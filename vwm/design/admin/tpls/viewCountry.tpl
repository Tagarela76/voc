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
								Country ID :
							</td>
							<td class="border_users_l border_users_b border_users_r">
								<div align="left" >&nbsp;{$country.country_id}</div>							
							</td>
						</tr>
						
						<tr>
							<td class="border_users_l border_users_b" height="20">
								Name :
							</td>
							<td class="border_users_l border_users_b border_users_r">
							<div align="left" >	&nbsp;{$country.country_name}</div>
						
								
							
							</td>
						</tr>
						
						<tr>
							<td class="border_users_l border_users_b" height="20">
								Format of date :
							</td>
							<td class="border_users_l border_users_b border_users_r">
							<div align="left" >	&nbsp;{if $country.date_type=='d-m-Y g:iA'}dd-mm-yyyy{else}mm/dd/yyyy{/if}</div>
							</td>
							
						</tr>
						<tr>
							<td class="border_users_l border_users_b" height="20">
								Number of states :
							</td>
							<td class="border_users_l border_users_b border_users_r">
								<div align="left" >	&nbsp;{$statesCount}</div>						
							</td>
						</tr>
						<tr >							
							<td colspan='2' class="border_users_l border_users_r">
								<br>
								<table width="100%" cellpadding="0" cellspacing="0" >
								 <tr class="users_top_lightgray">
										<td class="users_u_top_lightgray" height="25px">
											State
										</td>
										<td class="users_u_top_r_lightgray" >
											&nbsp;
										</td>												
									</tr>
											 
								{if $statesCount > 0}						 
														 
								{*BEGIN LIST*}						 
								
								{section name=i loop=$states}						
									<tr>
										{*
										<td class="bgtdleft">
										
										 <input type="checkbox" checked="checked" value="{$states[i].state_id}" name="item_{$smarty.section.i.index}" onclick="return CheckCB(this);"></td>
										*} 
										<td class="border_users_l border_users_b border_users_r" colspan='2' height="20" align='center'>				            
								             <div style="width:100%;">
											         {$states[i].name}	
											 </div >			 
										</td>				
										</tr>
								{/section}		
							
							
										{*	<tr class="cell">
											<td  class="bgtdleft" ></td><td colspan="3" class="border_td"></td>
											</tr>*}
					
								{*END LIST*}
								
								{else}
								
								{*BEGIN	EMPTY LIST*}
								<tr>
									<td class="border_users_l border_users_b border_users_r" colspan='2' height="20" align='center'>
										<b>No states</b>
									</td>
								</tr>
								{*END	EMPTY LIST*}
								{/if}								
							</table>
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
					
			
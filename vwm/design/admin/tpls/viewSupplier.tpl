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
								Supplier ID :
							</td>
							<td class="border_users_l border_users_b border_users_r">
								<div align="left" >&nbsp;{$supplier.supplier_id}</div>
							
								
							</td>
						</tr>
						
						<tr>
							<td class="border_users_l border_users_b" height="20">
								Description :
							</td>
							<td class="border_users_l border_users_b border_users_r">
							<div align="left" >	&nbsp;{$supplier.supplier_desc}</div>						
							</td>
						</tr>
						
						<tr>
							<td class="border_users_l border_users_b" height="20">
								Contact Person :
							</td>
							<td class="border_users_l border_users_b border_users_r">
							<div align="left" >	&nbsp;{$supplier.contact}</div>
							</td>
						</tr>
						
						<tr>
							<td class="border_users_l border_users_b" height="20">
								Phone :
							</td>
							<td class="border_users_l border_users_b border_users_r">
							<div align="left" >	&nbsp;{$supplier.phone}</div>
							</td>
						</tr>
						
						<tr>
							<td class="border_users_l border_users_b" height="20">
								Address :
							</td>
							<td class="border_users_l border_users_b border_users_r">
							<div align="left" >	&nbsp;{$supplier.address}</div>
							</td>
						</tr>
						
						<tr>
							<td class="border_users_l border_users_b" height="20">
								Country :
							</td>
							<td class="border_users_l border_users_b border_users_r">
							<div align="left" >	&nbsp;{$supplier.country}</div>
							</td>
						</tr>
						{*Similar suppliers*}
						<tr>
							<td class="border_users_l border_users_b" height="20">
								Similar Suppliers:
							</td>
							<td class="border_users_l border_users_b border_users_r">
								<div align="left">
									{if $SuppliersByOrigin|@count !== 0}
									{foreach from=$SuppliersByOrigin item=supplier name=fooList}
										{if $smarty.foreach.fooList.index < $SuppliersByOrigin|@count-1}
											&nbsp;{$supplier.supplier},
										{else}
											&nbsp;{$supplier.supplier}
										{/if}	
									{/foreach}
									{else}
										&nbsp;&nbsp;&mdash;
									{/if}	
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
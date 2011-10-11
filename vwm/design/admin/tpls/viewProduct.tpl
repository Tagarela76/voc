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
		<table class="users"  align="center" cellpadding="0" cellspacing="0">
			<tr class="users_top_yellowgreen" >
				<td class="users_u_top_yellowgreen" width="27%" height="30" >
					<span >View details</span>
				</td>
				<td class="users_u_top_r_yellowgreen" width="300">
				</td>				
			</tr>

						
						
						<tr>
							<td class="border_users_l border_users_b" height="20">
									Product Nr :
							</td>
							<td class="border_users_l border_users_r border_users_b">
								<div align="left" >&nbsp;{$product.product_nr}</div>
							
								
							</td>
						</tr>
						
						<tr>
							<td class="border_users_l border_users_b" height="20">
									Name :
							</td>
							<td class="border_users_l border_users_r border_users_b">
							<div align="left" >	&nbsp;{$product.name}</div>
						
								
							
							</td>
						</tr>
						
						<!--<tr>
							<td class="border_users_l border_users_b" height="20">
								3.	Inventory :
							</td>
							<td class="border_users_l border_users_r border_users_b">
							<div align="left" >	&nbsp;{$product.inventory_id}</div>
						
								
							
							</td>
						</tr>-->						
						
						<tr>
							<td class="border_users_l border_users_b" height="20">
									VOCLX :
							</td>
							<td class="border_users_l border_users_r border_users_b">
							<div align="" >	&nbsp;{$product.voclx}</div>
							
							
						
							</td>
						</tr>
						
						
						
						<tr>
							<td class="border_users_l border_users_b" height="20">
									VOCWX :
							</td>
							<td class="border_users_l border_users_r border_users_b">
							<div align="left" >	&nbsp;{$product.vocwx}</div>
							
							
						
							</td>
						</tr>
						
						
						<tr>
							<td class="border_users_l border_users_b" height="20">
								Percent Volatile by Weight :
							</td>
							<td class="border_users_l border_users_b border_users_r">
								<div align="left" >&nbsp;{$product.percent_volatile_weight}</div>
							</td>
						</tr>
						<tr>
							<td class="border_users_l border_users_b" height="20">
								Percent Volatile by Volume :
							</td>
							<td class="border_users_l border_users_b border_users_rpcente_r">
								<div align="left" >&nbsp;{$product.percent_volatile_volume}</div>
							</td>
						</tr>
						
						
						<tr>
							<td class="border_users_l border_users_b" height="20">
									Density:
							</td>
							<td class="border_users_l border_users_r border_users_b">
							<div align="left" >	&nbsp;{$product.density}&nbsp;{$densityDetails.numerator}/{$densityDetails.denominator}</div>
							</td>
						</tr>
						
						<tr>
							<td class="border_users_l border_users_b" height="20">
									Coating :
							</td>
							<td class="border_users_l border_users_r border_users_b">
							<div align="left" >	&nbsp;{$product.coating_id}</div>
							
							
						
							</td>
						</tr>
						
						<tr>
							<td class="border_users_l border_users_b" height="20">
									Specialty coating :
							</td>
							<td class="border_users_l border_users_r border_users_b">
							<div align="left" >	&nbsp;{$product.specialty_coating}</div>
							
							
						
							</td>
						</tr>
						
						
						

						
						<tr>
							<td class="border_users_l border_users_b" height="20">
									Aerosol :
							</td>
							<td class="border_users_l border_users_r border_users_b">
							<div align="left" >	&nbsp;{$product.aerosol}</div>
							
							
						
							</td>
						</tr>
						
						<tr>
							<td class="border_users_l border_users_b" height="20">
									Specific gravity :
							</td>
							<td class="border_users_l border_users_r border_users_b">
							<div align="left" >	&nbsp;{$product.specific_gravity}</div>
							
							
						
							</td>
						</tr>
						
						
						<tr>
							<td class="border_users_l border_users_b" height="20">
									Hazardous class:
							</td>
							<td class="border_users_l border_users_r border_users_b">
							<div align="left">&nbsp;{section name=i loop=$product.chemicalClasses}{$product.chemicalClasses[i].name};&nbsp;{/section}</div>																						
							</td>
						</tr>
						
						
						
						<tr>
							<td class="border_users_l border_users_b" height="20">
									Boiling range:
							</td>
							<td class="border_users_l border_users_r border_users_b">
							<div align="left" >	&nbsp;from {$product.boiling_range_from} to {$product.boiling_range_to}</div>
							</td>
						</tr>
						
						
						<tr>
							<td class="border_users_l border_users_b" height="20">
									Supplier:
							</td>
							<td class="border_users_l border_users_r border_users_b">
							<div align="left" >	&nbsp;{$product.supplier_id}</div>
							
							
						
							</td>
						</tr>
						
						
						<tr>
							<td class="border_users_l border_users_b" height="20">
									Industry Type / Industry Sub-Category:
							</td>
							<td class="border_users_l border_users_r border_users_b">
							<div align="left" > 
								{if $productTypes|@count > 0}
									{foreach from=$productTypes item=category key=k}
										{if $category.industrySubType neq ''}
											{if $k < $productTypes|@count-1}
												&nbsp;{$category.industryType} / {$category.industrySubType},
											{else}
												{$category.industryType} / {$category.industrySubType}
											{/if}
										{else}	
											{$category.industryType},
										{/if}	
									{/foreach}
								{/if}
							</div>
							
							
						
							</td>
						</tr>
						
						
							<tr>
							<td class="border_users_l border_users_r" colspan="2" style="padding:5px 3px 0 3px">
							
							
							
							
							<table width="100%" cellpadding="0" cellspacing="0" >
							
							<tr class="users_top_lightgray">
							<td class="users_u_top_lightgray" height="25px">Compounds</td>
							<td class="users_u_top_r_lightgray" >&nbsp;</td></tr>
							{if $product.components>0}
							<tr bgcolor="#e3e3e3">
							<td  class="border_users_l border_users_b" height="20" width="50%">
								Case Number
							</td>
							<td class="border_users_l border_users_r border_users_b">
							<div align="left">Description</div>
							
							</td>
							</tr>
							
						{section name=i loop=$product.components}
					
							<tr class="">
							<td  class="border_users_l border_users_b" height="20" width="50%">
								<div align="left">&nbsp;{$product.components[i].cas}</div>
							</td>
							<td class="border_users_l border_users_r border_users_b">
							<div align="left">&nbsp;{$product.components[i].description}</div>
							
							</td>
							</tr>
							
						{/section}
					{else}
						<tr>
							<td colspan=2 class="border_users_l border_users_r border_users_b" height="40">
								<div style="text-align: center; font-weight: bold;">No compounds in product!</div>
							</td>
						</tr>
					{/if}
						</table>
							</td>
						</tr>
											</tr>
						
						
						
						<tr>
						                       <td  height="15" class="users_u_bottom">
											   </td>
											     <td height="15" class="users_u_bottom_r">
											   </td>
						</tr>
			</table>

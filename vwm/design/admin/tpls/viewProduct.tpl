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
								{if $productIndustryTypes|@count > 0}
									{foreach from=$productIndustryTypes item=category key=k name=foo}
										{if $category.industrySubType neq ''}
											{if $smarty.foreach.foo.index < $productIndustryTypes|@count-1}
												&nbsp;{$category.industryType} / {$category.industrySubType},
											{else}
												&nbsp;{$category.industryType} / {$category.industrySubType}
											{/if}
										{else}
											&nbsp;{$category.industryType},
										{/if}
									{/foreach}
								{/if}
							</div>



							</td>
						</tr>
                        <tr>
                           <td class="border_users_l border_users_b" height="20">
									Product Library Type:
							</td>
                            
                            <td class="border_users_l border_users_r border_users_b">
							<div align="left" >
								{$productLibraryTypeName}
							</div>
							</td>
                        </tr>


						<tr>
							<td class="border_users_l border_users_b" height="20">
									MSDS:
							</td>
							<td class="border_users_l border_users_r border_users_b">
								{if $msdsLink}
									<a href='{$msdsLink}'>view</a> or
									<a href='{$unlinkMsdsUrl}'>unlink</a>
								{else}
									<a href="{$uploadMsdsUrl}"><div style="width:100%;">upload</div></a>
								{/if}
							</td>
						</tr>

						<tr>
							<td class="border_users_l border_users_b" height="20">
									Tech Sheet:
							</td>
							<td class="border_users_l border_users_r border_users_b">
								{if $techSheetLink}
									<a href='{$techSheetLink}'>view</a> or
									<a href='?action=unlinkTechSheet&category=product&productID={$product.product_id}&letterpage={$letterpage}&page={$page}'>unlink</a>
								{else}
									<a href="?action=uploadOneMsds&category=product&productID={$product.product_id}&letterpage={$letterpage}&page={$page}"><div style="width:100%;">upload</div></a>
								{/if}
							</td>
						</tr>
						{if $product.discontinued}
							<tr>
								<td class="border_users_l border_users_b" height="20">
										Discontinued:
								</td>
								<td class="border_users_l border_users_r border_users_b">
									<div align="left" style="font-weight: bold; color: #F00">	&nbsp; YES </div>
								</td>
							</tr>
						{/if}
						<tr>
							<td class="border_users_l border_users_b" height="20">
							Price:
							</td>
							<td class="border_users_l border_users_r border_users_b">
								<div align="left">
									&nbsp;$ {$product.product_pricing} &nbsp;  per &nbsp;{$product.unit_type}
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

<!-- STOCK VALUES -->
							<tr>
							<td class="border_users_l border_users_r" colspan="2" style="padding:5px 3px 0 3px">




							<table width="100%" cellpadding="0" cellspacing="0" >

							<tr class="users_top_lightgray">
							<td class="users_u_top_lightgray" height="25px" width="27%">Initial stock values</td>
							<td class="users_u_top_r_lightgray" >&nbsp;</td></tr>

							<tr bgcolor="#e3e3e3">
							<td  class="border_users_l border_users_b" height="20" width="27%">
								In stock :
							</td>
							<td class="border_users_l border_users_r border_users_b">
							<div align="left">&nbsp;{$product.product_instock}{if $stock.name}, {$stock.name}{/if}</div>

							</td>
							</tr>

							<tr bgcolor="#e3e3e3">
							<td  class="border_users_l border_users_b" height="20" width="27%">
								Limit :
							</td>
							<td class="border_users_l border_users_r border_users_b">
							<div align="left">&nbsp;{$product.product_limit}{if $stock.name}, {$stock.name}{/if}</div>

							</td>
							</tr>

							<tr bgcolor="#e3e3e3">
							<td  class="border_users_l border_users_b" height="20" width="27%">
								Amount :
							</td>
							<td class="border_users_l border_users_r border_users_b">
							<div align="left">&nbsp;{$product.product_amount}{if $stock.name}, {$stock.name}{/if}</div>

							</td>
							</tr>



						</table>
							</td>
						</tr>



						<tr>
						                       <td  height="15" class="users_u_bottom">
											   </td>
											     <td height="15" class="users_u_bottom_r">
											   </td>
						</tr>
			</table>
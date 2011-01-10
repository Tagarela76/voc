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
        <tr class="users_top_yellowgreen">
            <td class="users_u_top_yellowgreen" width="18%" height="30">
                <span>View details</span>
            </td>
            <td class="users_u_top_r_yellowgreen" width="100">
            </td>
        </tr>
        <tr>
            <td colspan="2" valign="top" class="border_users_l border_users_r">
                <table class="mix_id" style="border-top:0px;" width="100%" cellpadding="0" cellspacing="0">
                    <tr>
                        <td class="border_users_l border_users_b" height="20">
                           Product Nr :
                        </td>
                        <td class="border_users_l border_users_r border_users_b">
                            <div align="left">
                                &nbsp;{$product.product_nr}
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="border_users_l border_users_b" height="20">
                           Name :
                        </td>
                        <td class="border_users_l border_users_r border_users_b">
                            <div align="left">
                                &nbsp;{$product.name}
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="border_users_l border_users_b" height="20">
                           Inventory :
                        </td>
                        <td class="border_users_l border_users_r border_users_b">
                            <div align="left">
                                &nbsp;{$product.inventory_id}
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="border_users_l border_users_b" height="20">
                            VOCLX :
                        </td>
                        <td class="border_users_l border_users_r border_users_b">
                            <div align="">
                                &nbsp;{$product.voclx}
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="border_users_l border_users_b" height="20">
                            VOCWX :
                        </td>
                        <td class="border_users_l border_users_r border_users_b">
                            <div align="left">
                                &nbsp;{$product.vocwx}
                            </div>
                        </td>
                    </tr>
                                        					
					<tr>
						<td class="border_users_l border_users_b" height="20">
									Percent of Volatile by Weight:
						</td>
						<td class="border_users_l border_users_r border_users_b">
							<div align="left" >	&nbsp;{$product.percent_volatile_weight}</div>
						</td>
					</tr>
					<tr>
						<td class="border_users_l border_users_b" height="20">
									Percent of Volatile by Volume:
						</td>
						<td class="border_users_l border_users_r border_users_b">
							<div align="left" >	&nbsp;{$product.percent_volatile_volume}</div>
						</td>
					</tr>
                    <tr>
                        <td class="border_users_l border_users_b" height="20">
                           Density:
                        </td>
                        <td class="border_users_l border_users_r border_users_b">
                            <div align="left">
                                &nbsp;{$product.density}
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="border_users_l border_users_b" height="20">
                            Coating :
                        </td>
                        <td class="border_users_l border_users_r border_users_b">
                            <div align="left">
                                &nbsp;{$product.coating_id}
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="border_users_l border_users_b" height="20">
                            Specialty coating :
                        </td>
                        <td class="border_users_l border_users_r border_users_b">
                            <div align="left">
                                &nbsp;{$product.specialty_coating}
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="border_users_l border_users_b" height="20">
                            Aerosol :
                        </td>
                        <td class="border_users_l border_users_r border_users_b">
                            <div align="left">
                                &nbsp;{$product.aerosol}
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="border_users_l border_users_b" height="20">
                            Specific gravity :
                        </td>
                        <td class="border_users_l border_users_r border_users_b">
                            <div align="left">
                                &nbsp;{$product.specific_gravity}
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="border_users_l border_users_b" height="20">
                            Hazardous class:
                        </td>
                        <td class="border_users_l border_users_r border_users_b">
                            <div align="left">
                                &nbsp;{section name=i loop=$product.chemicalClasses}{$product.chemicalClasses[i].name};&nbsp;{/section}
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="border_users_l border_users_b" height="20">
                            Boiling range:
                        </td>
                        <td class="border_users_l border_users_r border_users_b">
                            <div align="left">
                                &nbsp;from {$product.boiling_range_from} to {$product.boiling_range_to}
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="border_users_l border_users_b" height="20">
                           Supplier:
                        </td>
                        <td class="border_users_l border_users_r border_users_b">
                            <div align="left">
                                &nbsp;{$product.supplier_id}
                            </div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td class="border_users_l border_users_r" colspan="2" style="padding-top:5px">
                <table width="100%" cellpadding="0" cellspacing="0">
                    <tr class="users_top_lightgray">
                        <td class="users_u_top_lightgray" height="25px" width="18%">
                            Compounds
                        </td>
                        <td class="users_u_top_r_lightgray" colspan='4'>
                            &nbsp;
                        </td>
                    </tr>
{if $product.components>0}
                    <tr bgcolor="#e3e3e3">
                        <td class="border_users_l border_users_b" height="20">
                            Case Number
                        </td>
                        <td class="border_users_l  border_users_b">
                            <div align="left">
                                Description
                            </div>
                        </td>
                        <td class="border_users_l border_users_b">
                            <div align="left">
                                MM/HG
                            </div>
                        </td>
                        <td class="border_users_l border_users_b">
                            <div align="left">
                                Temp
                            </div>
                        </td>
                        <td class="border_users_l border_users_r border_users_b">
                            <div align="left">
                                Weight
                            </div>
                        </td>
                    </tr>
                    {section name=i loop=$product.components}
                    <tr class="">
                        <td class="border_users_l border_users_b" height="20">
                            <div align="left">
                                &nbsp;{$product.components[i].cas}
                            </div>
                        </td>
                        <td class="border_users_l  border_users_b">
                            <div align="left">
                                &nbsp;{$product.components[i].description}
                            </div>
                        </td>
                        <td class="border_users_l border_users_b" height="20">
                            <div align="left">
                                &nbsp;{$product.components[i].mmhg}
                            </div>
                        </td>
                        <td class="border_users_l border_users_b">
                            <div align="left">
                                &nbsp;{$product.components[i].temp}
                            </div>
                        </td>
                        <td class="border_users_l border_users_r border_users_b">
                            <div align="left">
                                &nbsp;{$product.components[i].weight} %
                            </div>
                        </td>
                    </tr>
                    {/section}
{else}
                    <tr>
                        <td colspan="5" class="border_users_l border_users_r border_users_b" height="40">
                            <div style="text-align: center; font-weight: bold;">
                                No compounds in product!
                            </div>
                        </td>
                    </tr>
{/if}
                </table>
            </td>
        </tr>
        <tr>
            <td height="15" class="users_u_bottom" width="18%">
            </td>
            <td height="15" class="users_u_bottom_r">
            </td>
        </tr>
    </table>
</div>
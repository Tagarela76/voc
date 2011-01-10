{if $color eq "green"} 
{include file="tpls/notify/greenNotify.tpl" text=$message} 
{/if}
{if $color eq "orange"}
{include file="tpls/notify/orangeNotify.tpl" text=$message}
{/if}
{if $color eq "blue"}
{include file="tpls/notify/blueNotify.tpl" text=$message}
{/if}
<div style="padding:7px;">
    <table class="users" align="center" cellpadding="0" cellspacing="0">
        <tr class="users_top_yellowgreen">
            <td class="users_u_top_yellowgreen" width="27%" height="30">
                <span>View mixes</span>
            </td>
            <td class="users_u_top_r_yellowgreen" width="300">
            </td>
        </tr>
        <tr>
            <td width="50%" valign="top" style="padding:0 2px 0 5px" class="border_users_l">
                <table class="mix_id" width="100%" height="141px" cellpadding="0" cellspacing="0">
                    <tr>
                        <td colspan="2" style="border-width:0px;" height="20px">
                            <b>Mix id</b>
                        </td>
                    </tr>
                    <tr>
                        <td class="" height="20">
                            Usage Description:
                        </td>
                        <td class="">
                            <div align="left">
                                &nbsp; {$usage->description} 
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="" height="20">
                            Equipment:
                        </td>
                        <td class="">
                            <div align="left">
                                &nbsp; {$usage->equipment_id} 
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="" height="20">
                            AP Method:
                        </td>
                        <td class="">
                            <div align="left">
                                &nbsp; {$apmethod.apmethod_desc} 
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="" height="20">
                            Rule:
                        </td>
                        <td class="">
                            <div align="left">
                                &nbsp; {$rule.rule_nr} 
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="" height="20">
                            Creation date:
                        </td>
                        <td class="">
                            <div align="left">
                                &nbsp; {$usage->getCreationTime()} 
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="" height="20">
                            Expire date:
                        </td>
                        <td>
                            <div align="left">
                                &nbsp; {$expire} 
                            </div>
                        </td>
                    </tr>                    
		{if $mixValidatorResponse->isExpired()}
					<tr>
                        <td class="" height="20">
                            <div align="left"><i>Expired</i></div>
                        </td>
                        <td>
                        </td>
                    </tr>
		{/if}
		
		 {if $mixValidatorResponse->isPreExpired()}
                    <tr>
                        <td class="" height="20">
                            <div align="left"><i>Pre expired</i></div>
                        </td>
                        <td>
                        </td>
                    </tr>
		{/if}
                    <tr>
                        <td class="" height="20">
                            Exempt Rule:
                        </td>
                        <td class="">
                            <div align="left">
                                &nbsp; {$usage->exempt_rule} 
                            </div>
                        </td>
                    </tr>
                </table>
            </td>
            <td width="50%"style="padding:0 5px 0 2px" valign="top" class="border_users_r">
                <table class="mix_id" width="100%"cellpadding="0" cellspacing="0" height="136px">
                    <tr>
                        <td colspan="2" style="border-width:0px;" height="20px">
                            <b>Emissions</b>
                        </td>
                    </tr>
                    <tr>
                        <td class="" height="20">
                            Waste:
                        </td>
                        <td class="">
                            <div align="left">
                                &nbsp; {$usage->waste_percent} %
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="" height="20">
                            VOC:
                        </td>
                        <td class="">
                            <div align="left">
                                &nbsp; {$usage->voc} {$unittypeObj->getNameByID($companyDetails.voc_unittype_id)}
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="" height="20">
                            VOCLX:
                        </td>
                        <td class="">
                            <div align="left">
                                &nbsp; {$usage->voclx} 
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="" height="20">
                            VOCWX:
                        </td>
                        <td class="">
                            <div align="left">
                                &nbsp; {$usage->vocwx} 
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="" height="20">
                            Daily limit exceeded:
                        </td>
                        <td class="">
                            <div align="left">
                                &nbsp; {if $mixValidatorResponse->isDailyLimitExceeded()}<b>YES!!!</b>{else}no{/if} 
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="" height="20">
                            Department limit exceeded:
                        </td>
                        <td class="">
                            <div align="left">
                                &nbsp; {if $mixValidatorResponse->isDepartmentLimitExceeded()}<b>YES!!!</b>{else}no{/if} 
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="" height="20">
                            Facility limit exceeded:
                        </td>
                        <td class="">
                            <div align="left">
                                &nbsp; {if $mixValidatorResponse->isFacilityLimitExceeded()}<b>YES!!!</b>{else}no{/if} 
                            </div>
                        </td>
                    </tr>
                     <tr>
                        <td class="" height="20">
                            Department Annual limit exceeded:
                        </td>
                        <td class="">
                            <div align="left">
                                &nbsp; {if $mixValidatorResponse->getDepartmentAnnualLimitExceeded()}<b>YES!!!</b>{else}no{/if} 
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="" height="20">
                            Facility Annual limit exceeded:
                        </td>
                        <td class="">
                            <div align="left">
                                &nbsp; {if $mixValidatorResponse->getFacilityAnnualLimitExceeded()}<b>YES!!!</b>{else}no{/if} 
                            </div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td class="border_users_l border_users_r" colspan="2" style="padding:5px 5px 0 5px">
                <table width="100%"cellpadding="0" cellspacing="0">
                    <tr class="users_top_lightgray">
                        <td height="30" class="users_u_top_lightgray">
                            <b>Supplier</b>
                        </td>
                        <td>
                            <b>Product NR</b>
                        </td>
                        <td>
                            <b>Description</b>
                        </td>
                        <td>
                            <b>Quantity</b>
                        </td>
                        <td class="users_u_top_r_lightgray">
                            <b>Unit type</b>
                        </td>
                    </tr>
                    {foreach from=$usage->products item=product} 
                    <tr>
                        <td class="border_users_l border_users_b " height="20" width="20%">
                            {$product->getSupplier()}
                        </td>
                        <td class="border_users_l border_users_b " height="20" width="15%">
                            {*$usage.products[i].product_nr*}
                        </td>
                        <td class="border_users_l border_users_b border_users_r" height="20" width="40%">
                            {*$usage.products[i].description*}
                        </td>
                        <td class=" border_users_b border_users_r">
                            <div align="left">
                                &nbsp; {$product->quantity}
                            </div>
                        </td>
                        <td class=" border_users_b border_users_r">
                            <div align="left">
                                &nbsp; {$unittypeObj->getNameByID($product->unit_type)}
                            </div>
                        </td>
                    </tr>
                    {/foreach}
                </table>
            </td>
        </tr>        
        <tr>
            <td bgcolor="" height="20" class="users_u_bottom ">
                &nbsp; 
            </td>
            <td bgcolor="" height="20" class="users_u_bottom_r ">
                &nbsp; 
            </td>
        </tr>
    </table>
    <div align="right">
    </div>
</div>
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
            <td class="users_u_top_yellowgreen" width="27%" height="30">
                <span>View Pre Formulated Products</span>
            </td>
            <td class="users_u_top_r_yellowgreen" width="300">
            </td>
        </tr>
        <tr>
            <td width="100%" valign="top" style="padding:0 2px 0 5px" class="border_users_l">
                <table class="mix_id" width="100%"  cellpadding="0" cellspacing="0">
                    
                    <tr>
                        <td class="" height="20">
                             Description:
                        </td>
                        <td class="">
                            <div align="left">
                                &nbsp; {$pfp->getDescription()} 
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="" height="20">
                            Product count:
                        </td>
                        <td class="">
                            <div align="left">
                                &nbsp; {$pfp->getProductsCount()} 
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="" height="20">
                            Ratio:
                        </td>
                        <td class="">
                            <div align="left">
                                &nbsp; {$pfp->getRatio()} 
                            </div>
                        </td>
                    </tr>
                    
                </table>
            </td>
            
        </tr>
        <tr>
            <td class="border_users_l"  style="padding:5px 5px 0 5px">
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
                        
                        <td class="users_u_top_r_lightgray">
                            <b>Ratio</b>
                        </td>
                    </tr>
                    
                    {foreach from=$pfp->products item=product}
                    <tr>
                        <td class="border_users_l border_users_b " height="20" width="20%">
                            {$product->supplier}
                        </td>
                        <td class="border_users_l border_users_b " height="20" width="15%">
                            {$product->product_nr}
                        </td>
                        <td class="border_users_l border_users_b border_users_r" height="20" width="40%">
                            {$product->name}
                        </td>
                        
                        <td class=" border_users_b border_users_r">
                            <div align="left">
                                &nbsp; {$product->getRatio()}
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
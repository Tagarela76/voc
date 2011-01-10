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
    <table class="users" width="100%" cellpadding="0" cellspacing="0" align="center">
        <tr class="users_top_yellowgreen">
            <td class="users_u_top_yellowgreen" width="27%" height="30">
                <span>View details</span>
            </td>
            <td class="users_u_top_r_yellowgreen" width="300">
            </td>
        </tr>
        <tr>
            <td class="border_users_l border_users_b" height="20">
                Inventory Name :
            </td>
            <td class="border_users_l border_users_r border_users_b">
                <div align="left">
                    &nbsp;{$inventory->getName()}
                </div>
            </td>
        </tr>
        <tr>
            <td class="border_users_l border_users_b" height="20">
                Description : 
            </td>
            <td class="border_users_l border_users_r border_users_b">
                <div align="left">
                    &nbsp;{$inventory->getDescription()}
                </div>
            </td>
        </tr>
        <tr>
            <td class="border_users_l border_users_b" height="20">
                Type : 
            </td>
            <td class="border_users_l border_users_r border_users_b">
                <div align="left">
                    &nbsp;{$inventory->getType()}
                </div>
            </td>
        </tr>
				
        <tr>
            <td class="border_users_l border_users_r" colspan="2" style="padding:5px 3px 0 3px">
                <table width="100%" cellpadding="0" cellspacing="0">
{*if $productCount>0*}
{if $inventory->getProducts()|count > 0 }                	
				{if $inventory->getType() == Inventory::PAINT_MATERIAL}					
                    <tr class="users_top_lightgray" height="25">
                        <td colspan="8" class="users_u_top_lightgray">
                            Products
                        </td>
                        <td colspan="3" class="users_u_lightgray">
                            Date on last inventory
                        </td>
                        <td class="users_u_lightgray">
                            Total
                        </td>
                        <td colspan="2" class="users_u_top_r_lightgray">
                            &nbsp;
                        </td>
                    </tr>                    

                    <tr bgcolor="#e3e3e3">
                    	<td class="border_users_l border_users_b">
                            <div align="left">
                                ID
                            </div>
                        </td>
                        <td class="border_users_l border_users_b">
                            <div align="left">
                                Supplier
                            </div>
                        </td>
                        <td class="border_users_l border_users_b" height="20">
                            NR
                        </td>
                        <td class="border_users_l border_users_r border_users_b">
                            <div align="left">
                                Name
                            </div>
                        </td>
                        <td class="border_users_r border_users_b">
                            <div align="left">
                                 O.S. Use
                            </div>
                        </td>
                        <td class="border_users_r border_users_b">
                            <div align="left">
                               	C.S. Use
                            </div>
                        </td>
                        <td class="border_users_r border_users_b">
                            <div align="left">
                                Location of storage
                            </div>
                        </td>
                        <td class="border_users_r border_users_b">
                            <div align="left">
                               Location of use
                            </div>
                        </td>
                        <td class="border_users_r border_users_b">
                            <div align="left">
                                Inventory
                            </div>
                        </td>
                        <td class="border_users_r border_users_b">
                            <div align="left">
                                Quantity
                            </div>
                        </td>
                        <td class="border_users_r border_users_b">
                            <div align="left">
                                Unit
                            </div>
                        </td>
                        <td class="border_users_r border_users_b">
                            <div align="left">
                                Inventory
                            </div>
                        </td>
                        <td class="border_users_r border_users_b">
                            <div align="left">
                                To date left
                            </div>
                        </td>
                        <td class="border_users_r border_users_b">
                            <div align="left">
                                Gauge
                            </div>
                        </td>
                    </tr>
                    {foreach from=$inventory->getProducts() item=product}
                    <tr>
                    	<td class="border_users_l border_users_b">
                            {$product->getProductID()}
                        </td>
                        <td class="border_users_l border_users_b">
                            {$product->getSupplier()}
                        </td>
                        <td class="border_users_l border_users_b" height="20">
                            {$product->getProductNR()}
                        </td>
                        <td class="border_users_l border_users_r border_users_b" height="20">
                            {$product->getName()}
                        </td>
                        <td class="border_users_r border_users_b" height="20">
                         	{$product->getOS_use()} &nbsp; 
                        </td>
                        <td class="border_users_r border_users_b" height="20">
                            {$product->getCS_use()} &nbsp; 
                        </td>
                        <td class="border_users_r border_users_b" height="20">
                            {$product->getStorageLocation()} &nbsp; 
                        </td>
                        <td class="border_users_r border_users_b" height="20">
                        	{if $product->getUseLocation()}
                        		{foreach from=$product->getUseLocation() item=useLocation}
                        			{$useLocation.name}&nbsp;
                        		{/foreach}
                        	{else}
                        		<i>No location</i>
                        	{/if}                        	                             
                        </td>

                    {if $product->getLastInventory() }
                        <td class="border_users_r border_users_b" height="20">                        
                            {$product->lastInventory.inventory} 
                        </td>
                        <td class="border_users_r border_users_b" height="20">
                            {$product->lastInventory.quantity} 
                        </td>
                        <td class="border_users_r border_users_b" height="20">
                            {$product->lastInventory.unittypeName} 
                        </td>
                    {else}
                    	<td colspan="3" class="border_users_r border_users_b" height="20">
                            <i>No inventories yet</i> 
                        </td>
                    {/if}
                        <td class="border_users_r border_users_b" height="20">
                        	{*if $request.facilityID*}
                                     {$product->getTotalQty()}
							{*elseif $request.departmentID}
								{foreach from=$product->getUseLocation() item=useLocation}
									{if $useLocation.departmentID == $request.departmentID}
										{$useLocation.totalQty}
									{/if}
								{/foreach}																	
							{/if*}                                                   
                        </td>
                        <td class="border_users_r border_users_b" height="20">
                            {$product->getToDateLeft()} 
                        </td>
                        <td class="border_users_r border_users_b" height="20">
                        	{assign var="left" value=$product->getToDateLeft()}
							{assign var="total" value=$product->getTotalQty()|round}
							{assign var="pxCount" value=$left*200/$total|round}
                            {*INDICATOR*}                                        
                            {include file="tpls:tpls/vocIndicator.tpl" currentUsage=$left 
																  vocLimit=$total
																  pxCount=$pxCount }                                
                        </td>
                        
                    </tr>
          			{/foreach}
					
			{elseif $inventory->getType() == Inventory::PAINT_ACCESSORY}
				
				<tr class="users_top_lightgray" height="25">
                        <td colspan="5" class="users_u_top_lightgray">
                            Accessories
                        </td>                        
                        <td class="users_u_top_r_lightgray">
                            &nbsp;
                        </td>
                    </tr>                    

                    <tr bgcolor="#e3e3e3">
                        <td class="border_users_l border_users_b">
                            <div align="left">
                                Accessory ID
                            </div>
                        </td>
                        <td class="border_users_l border_users_b" height="20">
                            	Accessory Name
                        </td>
                        <td class="border_users_l border_users_r border_users_b">
                            <div align="left">
                                Unit Amount
                            </div>
                        </td>
                        <td class="border_users_r border_users_b">
                            <div align="left">
                                Unit Count
                            </div>
                        </td>
                        <td class="border_users_r border_users_b">
                            <div align="left">
                               	Unit Quantity
                            </div>
                        </td>
                        <td class="border_users_r border_users_b">
                            <div align="left">
                                Total Quantity
                            </div>
                        </td>                        
                    </tr>
					{foreach from=$inventory->getProducts() item=product}
                    <tr>
                        <td class="border_users_l border_users_b">
                            {$product->getAccessoryID()}
                        </td>
                        <td class="border_users_l border_users_b" height="20">
                            {$product->getAccessoryName()}
                        </td>
                        <td class="border_users_l border_users_r border_users_b" height="20">
                            {$product->getUnitAmount()} &nbsp;
                        </td>
                        <td class="border_users_r border_users_b" height="20">
                         	{$product->getUnitCount()} &nbsp; 
                        </td>
                        <td class="border_users_r border_users_b" height="20">
                            {$product->getUnitQuantity()} &nbsp; 
                        </td>
                        <td class="border_users_r border_users_b" height="20">
                            {$product->getTotalQuantity()} &nbsp; 
                        </td>                        
					</tr>
					{/foreach}					
				{/if}						           
{else}
                    <tr>
                        <td colspan="14" class="border_users_l border_users_r border_users_b" height="40">
                            <div style="text-align: center; font-weight: bold;">
                                No products in inventory!
                            </div>
                        </td>
                    </tr>
{/if}
                </table>
            </td>
        </tr>
        <tr>
            <td height="15" class="users_u_bottom">
            </td>
            <td height="15" class="users_u_bottom_r">
            </td>
        </tr>
    </table>
</div>

{if $inventory->getType() == Inventory::PAINT_MATERIAL}
{assign var="productID" value=$product->getProductID()}						
						<tr height="10px">
						{if $parentCategory == 'facility'}
                            <td class="border_users_r border_users_b border_users_l">
                                <input type="checkbox" checked="checked" value="{$product->getProductID()}" name="product_id[]">
                            </td>
							<td class="border_users_r border_users_b">
                                <div style="width:100%;">
                                    {$product->getProductID()}
                                </div>
                            </td>						                            
						{else}
							<td class="border_users_r border_users_b  border_users_l">
                                <div style="width:100%;">
                                    {$product->getProductID()}
									<input type="hidden" name="product_id[]" value="{$product->getProductID()}">
                                </div>
                            </td>									
						{/if}
							<td class="border_users_r border_users_b">
                                <div style="width:100%;">
                                    {$product->getSupplier()}									
                                </div>
                            </td>
                            <td class="border_users_r border_users_b">
                                <div style="width:100%;">
                                    {$product->getProductNR()}
                                </div>
                            </td>
                            <td class="border_users_r border_users_b">
                                <div style="width:100%;">
                                    {$product->getName()}
                                </div>
                            </td>
                            <td class="border_users_r border_users_b">
                                <div style="width:100%;">
								{if $parentCategory == 'facility' || $allowEdit.$productID}
                                    <input type='text' name='OS_use[{$product->getProductID()}]' value='{$product->getOS_use()}' size='7'>
									{if $validStatus.products.$i.OSuse eq 'failed'}                                    
                                    <div class="error_img"></div>
									{/if}								
								{else}
									{$product->getOS_use()}
								{/if}                                    
                                </div>
                            </td>
                            <td class="border_users_r border_users_b">
                                <div style="width:100%;">
								{if $parentCategory == 'facility' || $allowEdit.$productID}									
                                    <input type='text' name='CS_use[{$product->getProductID()}]' value='{$product->getCS_use()}' size='7'>
									{*ERORR*}									
									{if $validStatus.products.$i.CSuse eq 'failed'}                                    
                                    <div class="error_img"></div>                                    
                                    {/if}
								{else}
									{$product->getCS_use()}
								{/if}                                    
                                </div>
                            </td>
                            <td class="border_users_r border_users_b">
                                <div style="width:100%;">
                                    {if $parentCategory == 'facility'}
										<input type='text' name='storageLocation[{$product->getProductID()}]' value='{$product->getStorageLocation()}' size='7'>
										{*ERORR*}
										{if $validStatus.products.$i.storageLocation eq 'failed'}										                                    	                                    	
                                    	<div class="error_img"></div>                                    	                                    	
                                    	{/if}
									{else}
										{$product->getStorageLocation()}
									{/if}                                    
                                </div>
                            </td>
                            <td class="border_users_r border_users_b">
                            {if $parentCategory == 'facility'}
                                <div style="width:100%;" id="visualDepartmentsList_{$product->getProductID()}">								
                                    {if $product->getUseLocation()}
                        				{foreach from=$product->getUseLocation() item=useLocation}
                        						<a href="javascript:void(0);" onclick="editStorageLocation({$product->getProductID()});">{$useLocation.name}</a>&nbsp;
                        				{/foreach}
                        			{else}
                        				<a href="javascript:void(0);" onclick="editStorageLocation({$product->getProductID()});">Edit</a>
                        			{/if}									           								
                                </div>
                                <div id="hiddenDepartmentsList_{$product->getProductID()}">
                                	{foreach from=$product->getUseLocation() item=useLocation}
                        				<input type="hidden" name="useLocation[{$product->getProductID()}][]" value="{$useLocation.departmentID}"/>										
                        			{/foreach}
                                </div>
							{else}
								<div style="width:100%;">
									{foreach from=$product->getUseLocation() item=useLocation}
										{$useLocation.name}&nbsp;
									{/foreach}
								</div>
							{/if}
                            </td>						
                         {if $product->getLastInventory()}
                            <td class="border_users_r border_users_b">
                                <div style="width:100%;">
                                    {$product->lastInventory.inventory} 
                                </div>
                            </td>
                             <td class="border_users_r border_users_b">
                                <div style="width:100%;">
                                     {$product->lastInventory.quantity} 
                                </div>
                            </td>
                             <td class="border_users_r border_users_b">
                                <div style="width:100%;">
                                     {$product->lastInventory.unittypeName} 
                                </div>
                            </td>
                         {else}
                         	<td colspan="3" class="border_users_r border_users_b">
                                <div style="width:100%;">
                                    <i>No inventories yet</i> 
                                </div>
                            </td>
                         {/if}
                         	<td class="border_users_r border_users_b">
                                <div style="width:100%;">	
								{if $parentCategory == 'facility' || $allowEdit.$productID}									
                                     	<input type='text' name='totalQty[{$product->getProductID()}]' value='{$product->getTotalQty()}' size='7'>
										{*ERORR*}
										{if $validStatus.products.$i.quantity eq 'failed'}
										 	<div class="error_img"></div>
										{/if}
										{*ERORR*}
										{if $validStatus.products.$i.quantity eq 'conflict'}
										 	<div class="error_img"><span class="error_text">Should be &lt;= {$validStatus.products.$i.limit}</span></div>
										{/if}
								{else}
										{$product->getTotalQty()}								
								{/if}								                                   
                                </div>								 
                            </td>
                            <td class="border_users_r border_users_b">
                                <div style="width:100%;">
                                    {$product->getToDateLeft()} 
                                </div>
                            </td>
                        </tr>
						
{elseif $inventory->getType() == Inventory::PAINT_ACCESSORY}
{assign var="productID" value=$product->getAccessoryID()}

						<tr height="10px">
                            <td class="border_users_r border_users_b border_users_l">
                                <input type="checkbox" checked="checked" value="{$product->getAccessoryID()}" name="product_id[]">
                            </td>                          
                            <td class="border_users_r border_users_b">
                                <div style="width:100%;">
                                    {$product->getAccessoryID()}
                                </div>
                            </td>
                            <td class="border_users_r border_users_b">
                                <div style="width:100%;">
                                    {$product->getAccessoryName()}
                                </div>
                            </td>
                            <td class="border_users_r border_users_b">
                                <div style="width:100%;">
                                    <input type='text' name='unitAmount[{$product->getAccessoryID()}]' value='{$product->getUnitAmount()}' size='7'>                                    
                                    {if $validStatus.products.$i.OSuse eq 'failed'}
                                    {*ERORR*}
                                    <div class="error_img">                                      
                                    </div>
                                    {*/ERORR*}
                                    {/if}                                    
                                </div>
                            </td>
                            <td class="border_users_r border_users_b">
                                <div style="width:100%;">
                                    <input type='text' name='unitCount[{$product->getAccessoryID()}]' value='{$product->getUnitCount()}' size='7'>
                                    {if $validStatus.products.$i.locationStorage eq 'failed'}
                                    {*ERORR*}
                                    <div class="error_img">                                        
                                    </div>
                                    {*/ERORR*}
                                    {/if}
                                </div>
                            </td>
                            <td class="border_users_r border_users_b">
                                <div style="width:100%;">
                                    <input type='text' name='unitQuantity[{$product->getAccessoryID()}]' value='{$product->getUnitQuantity()}' size='7'>                                    
                                    {if $validStatus.products.$i.CSuse eq 'failed'}
                                    {*ERORR*}
                                    <div class="error_img">
                                    </div>
                                    {*/ERORR*}
                                    {/if}
                                </div>
                            </td>                            
                         	<td class="border_users_r border_users_b">
                                <div style="width:100%;">
                                     {$product->getTotalQuantity()}
                                </div>
                            </td>                            
                        </tr>
{/if}						
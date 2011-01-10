<form method="get" action="">	{*this FORM will be closed at categoriesList.tpl*}
    <div align="center" class="control_panel_padd">
        <div class="control_panel" class="logbg" align="left">
            <div class="control_panel_tl">
                <div class="control_panel_tr">
                    <div class="control_panel_bl">
                        <div class="control_panel_br">
                            <div class="control_panel_center">
                                <table cellpadding="0" cellspacing="0" class="controlCategoriesList" width="100%">
                                    <tr>
                                        {if $itemsCount > 0}
                                        	{if ($bookmarkType=="equipment" && $permissions.equipment.showSelectAll) || 
												($bookmarkType=="user" && $permissions.user.showSelectAll) || 
												($bookmarkType=="inventory" && $permissions.data.showSelectAll) || 
												($bookmarkType=="product" && $permissions.data.showSelectAll) || 
												($bookmarkType=="usage" && $permissions.data.showSelectAll) || 
												$permissions.showSelectAll}
                                        <td class="control_list" width="10%">
                                            Select: <a onclick="CheckAll(this)" name='all' class="id_company1">All</a>
                                            /<a onclick="unCheckAll(this)" name='none' class="id_company1">None</a>
                                        </td>
                                        	{/if}
                                        {/if}
                                        <td>
                                            {if ($bookmarkType=="equipment" && $permissions.equipment.add) || 
                                            	($bookmarkType=="user" && $permissions.user.add) ||  
                                            	($bookmarkType=="inventory" && $permissions.data.add) || 
                                            	($bookmarkType=="usage" && $permissions.data.add) || 
                                            	$permissions.addItem}
                                            <div class="button_alpha add_button">
                                                <input type="submit" name="action" value="showAddItem" onclick="location.href='{$addItem}'">
                                            </div>
											{/if}
                                        </td>
                                        <td>
                                            {if $itemsCount > 0} 
                                            	{if ($bookmarkType=="equipment" && $permissions.equipment.delete) || 
                                            		($bookmarkType=="user" && $permissions.user.delete) ||  
                                            		($bookmarkType=="inventory" && $permissions.data.delete) || 
                                            		($bookmarkType=="usage" && $permissions.data.delete) || 
                                            		$permissions.deleteItem}
                                            <div class="button_alpha delete_button">
                                                <input type="submit" name="action" value="deleteItem">
                                            </div>
												{/if}
                                            	{if $bookmarkType=="product"}
                                            <div class="button_alpha group_button">
                                                <input type="submit" name="action" value="groupProducts">
                                            </div>
												{/if}
                                            {/if}
                                        </td>
                                        <td class="middle">
                                            {if $smarty.session.overCategoryType!="company"}
                                            <div class="button_alpha report_button">
                                                <input type="submit" name="action" value="createReport">
                                            </div>
											{/if}
                                        </td>
										{*INDICATOR*}
                                        <td width="70%" align="right">
                                            {if $smarty.session.overCategoryType == 'department'}
                                            	{include file="tpls:tpls/vocIndicator.tpl" emissionLog="true"}
                                            {/if}
                                        </td>
										<td>
                                        	<input type="hidden" name="itemID" value="{$smarty.session.overCategoryType}">
											<input type="hidden" name="itemsCount" value="{$itemsCount}">
											{*wtf?*}
											<input type="hidden" name="company_id" value="{$smarty.session.CategoryID}">
											<input type="hidden" name="facility_id" value="{$smarty.session.CategoryID}">
											<input type="hidden" name="department_id" value="{$smarty.session.CategoryID}">
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

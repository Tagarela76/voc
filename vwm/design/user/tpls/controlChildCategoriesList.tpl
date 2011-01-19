{if $notViewChildCategory!=true}
<form method="get" action="" id='controlForm'>	{*this FORM will be closed at categoriesList.tpl*}
    <div align="center" class="control_panel_padd">
        <div class="control_panel" class="logbg" align="left">
            <div class="control_panel_tl">
                <div class="control_panel_tr">
                    <div class="control_panel_bl">
                        <div class="control_panel_br">
                            <div class="control_panel_center">
                               
							    <div class="controlCategoriesList" style="display:table;width:100%;">
							    	
                                        {*  {if $childCategoryItems|@count > 0}
                                        	{if ($request.bookmark=="equipment" && $permissions.equipment.showSelectAll) || 
												($request.bookmark=="user" && $permissions.user.showSelectAll) || 
												($request.bookmark=="inventory" && $permissions.data.showSelectAll) || 
												($request.bookmark=="accessory" && $permissions.data.showSelectAll) || 
												($request.bookmark=="product" && $permissions.data.showSelectAll) || 
												($request.bookmark=="mix" && $permissions.data.showSelectAll) || 
												$permissions.showSelectAll}
                                  <div class="select_button_float_left">
                                        <div class="control_list">
                                            <div class="control_list" style="width:130px">
												<span style='displany:inline-block'>
													Select: 
													<a onclick="CheckAll(this)" class="id_company1" >All</a>									
													 /
													<a onclick="unCheckAll(this)" class="id_company1">None</a>
												</span>
											</div>
                                        </div>
										</div>                                        
                                        	{/if}
                                        {/if}*}
									
									<div style="display:table;" class="floatleft">										
									{if $request.bookmark=="inventory" && $request.tab==Inventory::PAINT_MATERIAL && $request.category=="department"}
												{if $permissions.data.add && $permissions.data.delete}
											<div class="button_float_left">	
												<div class="button_alpha addDelete_button">
                                                	<input type="submit" name="action" value="addItem">
                                            	</div>
											</div>
												{/if}
									{else}
                                            {if ($request.bookmark=="equipment" && $permissions.equipment.add) || 
                                            	($request.bookmark=="user" && $permissions.user.add) ||  
                                            	($request.bookmark=="accessory" && $permissions.data.add) || 
                                            	($request.bookmark=="inventory" && $permissions.data.add) || 
                                            	($request.bookmark=="mix" && $permissions.data.add) || 
                                            	$permissions.addItem && $request.bookmark!="reduction" && $request.bookmark != "solventplan" &&
                                            	$request.bookmark != "regupdate"}
                                            	
											<div class="button_float_left">	
                                            <div class="button_alpha add_button">
                                                <input type="submit" name="action" value="addItem" {if $vpsSaysNo}disabled{/if}>
                                            </div>
											</div>
											{/if}
											
											{if $request.bookmark == "regupdate"}
												<div class="button_float_left">	
                                                	<input type="submit" class="button" name="action" value="markReaded" {if $vpsSaysNo}disabled{/if} />
                                           	 		<input type="hidden" name="facilityID" value="{$request.id}" />
												</div>
											{/if}
											
											{if $childCategoryItems|@count > 0 || $request.bookmark == 'logbook'} 
                                            	{if ($request.bookmark=="equipment" && $permissions.equipment.delete) || 
                                            		($request.bookmark=="user" && $permissions.user.delete) ||  
                                            		($request.bookmark=="accessory" && $permissions.data.delete) || 
                                            		($request.bookmark=="inventory" && $permissions.data.delete) || 
                                            		($request.bookmark=="mix" && $permissions.data.delete) ||
                                            		($request.bookmark=="logbook" && $permissions.data.delete) || 
                                            		$permissions.deleteItem}
											<div class="button_float_left">	
                                           			<div class="button_alpha delete_button">
                                               			 <input type="submit" name="action" value="deleteItem">
                                           			</div>
											</div>	
												{/if}
                                            	{*if $request.bookmark=="product"}
                                            	<div class="button_alpha group_button">
                                                <input type="submit" name="action" value="groupProducts">
                                           		</div>
												{/if*}
                                           	{/if}
                                            {if $request.bookmark=="docs"}
											<div class="button_float_left">	
												<input type="submit" class="button" name="action" value="Edit" onclick="location.href='?action=edit&category=docs&facilityID={$request.id}'">
											</div>
											<div class="button_float_left">	
												<div class="button_alpha delete_button">
													<input type="submit" name="action" value="deleteItem">
												</div>
											</div>												
											{/if}  
									{/if}										                                        
									</div>
                                    {if $childCategory != "company" && $show.reports}											
									<div class="button_float_left">
                                            <div class="button_alpha report_button">
                                                <input type="button" name="action" value="createReport" onclick="location.href='?action=createReport&category={$request.category}&id={$request.id}'">
                                            </div>											
                                     </div>
									{/if}
											
										{*EXPORT PAGE*}
										
										{if $childCategoryItems|@count > 0 && $childCategoryItems}
										{if $childCategory == "product" ||
											$childCategory == "mix" ||
											$childCategory == "accessory" ||
											$childCategory == "inventory" ||
											$childCategory == "equipment"
										}
										<div class="button_float_left">                                            
											<input type="button" value="Export this tab to PDF" class="button" onclick="location.href = document.location.href.replace('#','') + '&export=true'">                                            										
                                     	</div>
                                     	{/if}
                                     	{/if}
                                     	{*/EXPORT PAGE*}
                                     
                                     
										{*INDICATOR*}
                                            {if $request.category == 'department' || $request.category == 'facility'}
                                        <div class="button_float_right">
                                            	{include file="tpls:tpls/vocIndicator.tpl" emissionLog='true'}
										</div>
                                            {/if}
                                        
										
                                        	<input type="hidden" name="category" value="{$childCategory}">
											{if $request.category != 'root'}
												<input type="hidden" name="{$request.category}ID" value="{$request.id}">												
											{/if}
											{if $request.bookmark == 'inventory'}
												<input type="hidden" name="tab" value="{$request.tab}">
											{/if}
                                        
                          			 </div>
								
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
	<div class="br_10"></div>
{/if}
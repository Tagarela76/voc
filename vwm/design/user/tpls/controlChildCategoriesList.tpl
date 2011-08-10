{if $notViewChildCategory!=true}
<form method="get" action="" id='controlForm'>	{*this FORM will be closed at categoriesList.tpl*}
    <div align="center" class="control_panel_padd">
        <div class="control_panel" class="logbg" align="left">
            
<em class="bt"><b>&nbsp;</b></em>



                            <div class="control_panel_center">

                               
							    <div class="controlCategoriesList" style="display:table;width:100%;">

							    	<div style="display:table;" class="floatleft">		
									{if $request.bookmark=="inventory" && $request.tab==Inventory::PAINT_MATERIAL && $request.category=="department"}
												{if $permissions.data.add && $permissions.data.delete}
											<div class="button_float_left">	
												<div  class="button_alpha addDelete_button">
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
                                            	$permissions.addItem && $request.bookmark != "reduction" && $request.bookmark != "solventplan" &&
                                            	$request.bookmark != "regupdate" && $request.bookmark != "emissionGraphs" 
                                                && $request.bookmark != "product"}
                                            	
											<div class="button_float_left">	
                                            <div class="button_alpha add_button">
                                            	{if $request.tab == "pfp"}
                                            	
                                            		<input type="submit" name="action" value="addPFPItem" {if $vpsSaysNo}disabled{/if}>
                                            	{else}
                                            	
                                                	<input type="submit" name="action" value="addItem" {if $vpsSaysNo}disabled{/if}>
                                                {/if}
                                            </div>
											</div>
											{/if}
											
											{if $request.bookmark == "regupdate"}
												<div class="button_float_left">	
													<div class="button_alpha markasread_button">
                                                		<input type="submit" class="" name="action" value="markReaded" {if $vpsSaysNo}disabled{/if} />                                                	
                                           	 			<input type="hidden" name="facilityID" value="{$request.id}" />
                                           	 			<input type="hidden" name="tab" value="{$request.tab}" />
                                           	 		</div>
												</div>
											{/if}
											
											{if $childCategoryItems|@count > 0 || $request.bookmark == 'logbook'} 
                                            	{if ($request.bookmark=="equipment" && $permissions.equipment.delete) || 
                                            		($request.bookmark=="user" && $permissions.user.delete) ||  
                                            		($request.bookmark=="accessory" && $permissions.data.delete) || 
                                            		($request.bookmark=="inventory" && $permissions.data.delete) || 
                                            		($request.bookmark=="mix" && $permissions.data.delete) ||
                                            		($request.bookmark=="logbook" && $permissions.data.delete) ||                                                         
                                                            $permissions.deleteItem && $request.bookmark != "product"}
											<div class="button_float_left">	
                                           			<div class="button_alpha delete_button">
                                           			{if $smarty.request.tab != 'pfp'}
                                               			 <input type="submit" name="action" value="deleteItem">
                                               		{else}
                                               			 <input type="submit" name="action" value="deletePFPItem">
                                               		{/if}
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
                                    {if ($childCategory != "company" && $show.reports) || ($request.category == "company")}
                                            <div class="button_float_left">
                                            <div class="button_alpha report_button">
                                                <input type="button" name="action" value="createReport" onclick="location.href='?action=createReport&category={$request.category}&id={$request.id}'">
                                            </div>											
                                            </div> 
                                     {if $show.reports and $request.bookmark == "solventplan" and $periodType=='month'}
                                     <div class="button_float_left">
                                     <input type='button' id='edit' class='button' value='Edit' onclick="location.href='?action=edit&category=solventplan&tab=direct&facilityID={$request.id}&mm={$period.month}&yyyy={$period.year}'">
                                     </div>
                                     {/if}
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
                                            
                                         {if ($request.category == "facility" and $request.bookmark == "department" || $request.bookmark == "product")}  
                                            <div class="button_float_left">
                                            <div class="button_alpha new_product_button">
                                                <input type='button'>
                                            </div>											
                                            </div>
                                         {/if}
                                         
                                         {if ($request.category == "department" and $request.bookmark == "equipment")}  
                                            <div class="button_float_left">
                                            <div class="button_alpha new_product_button">
                                                <input type='button'>
                                            </div>											
                                            </div>
                                         {/if}
                                           
                                     	{*/EXPORT PAGE*}
                                     
                                     
										{*INDICATOR*}
                                            {if $request.category == 'department' || $request.category == 'facility'}
                                        <div class="button_float_right">
                                            	{include file="tpls:tpls/vocIndicator.tpl" emissionLog='true'}
										</div>
                                            {/if}
                                        
										{if $request.bookmark != 'emissionGraphs'}
                                        	<input type="hidden" name="category" value="{$childCategory}">
											{if $request.category != 'root'}
												<input type="hidden" name="{$request.category}ID" value="{$request.id}">												
											{/if}
											{if $request.bookmark == 'inventory'}
												<input type="hidden" name="tab" value="{$request.tab}">
											{/if}
										{else}
										
											<input type="hidden" name="action" value="browseCategory" />
											<input type="hidden" name="category" value="{$request.category}" />
											<input type="hidden" name="id" value="{$request.id}" />
											<input type="hidden" name="bookmark" value="{$request.bookmark}" />
										{/if}
                                        
                          			 </div>
								
                            </div>
            

<em class="bb"><b>&nbsp;</b></em>




        </div>
    </div>
	<div class="br_10"></div>
{/if}
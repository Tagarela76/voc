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
                                            	($request.bookmark=="inventory" && $permissions.data.add) ||
                                            	($request.bookmark=="nox" && $permissions.data.add) ||
                                            	($request.bookmark=="mix" && $permissions.data.add) ||
                                            	$request.bookmark != "reduction" && $request.bookmark != "solventplan" &&
                                            	$request.bookmark != "regupdate" && $request.bookmark != "emissionGraphs"

                                                && $request.bookmark != "product"}
								{*($request.bookmark=="accessory" && $permissions.data.add) ||*}
								{if $request.tab != "burnerRatio"}
									<div class="button_float_left">
										<div class="button_alpha add_button">
											{if $request.category == "facility" && $request.bookmark == "nox" && $request.tab == "nox" && $request.action == "browseCategory"} 
												<input type="submit" name="action" value="addNoxEmissionsByFacLevel" {if $vpsSaysNo}disabled{/if}>
											{else}
												{if $request.tab == "pfp"}
													<input type="submit" name="action" value="addPFPItem" {if $vpsSaysNo}disabled{/if}>
												{else}
													<input type="submit" name="action" value="addItem" {if $vpsSaysNo}disabled{/if}>
												{/if}
											{/if}	
										</div>
									</div>
								{/if}	
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

							{if $childCategoryItems|@count > 0 || $request.bookmark == 'logbook' }
								{if ($request.bookmark=="equipment") ||
                                            		($request.bookmark=="user") ||
                                            		($request.bookmark=="inventory") ||
                                            		($request.bookmark=="nox" && $request.tab != "burnerRatio") ||
                                            		($request.bookmark=="mix") ||
                                            		($request.bookmark=="logbook") ||
													($request.bookmark=="repairOrder") ||
													($request.bookmark=="pfpTypes") ||
                                                     $permissions.deleteItem && $request.bookmark != "product" && $request.tab != "burnerRatio"}
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

							{if $request.bookmark == 'pfpLibrary'}
								<div class="button_float_left">
									{if $request.tab == 'all'}
										<input class="button" type="submit" value="Assign Selected PFP's"/>
										<input type="hidden" name="action" value="assign"/>
									{else}
										<input class="button" type="submit" value="Remove Selected PFP's From My List"/>
										<input type="hidden" name="action" value="unassign"/>
									{/if}
								</div>
                                <div class="button_float_left">
                                    <input class="button" type="button" value="Print Blank" onclick="location.href = document.location.href.replace('#','') + '&print=true'"/>
                                </div>
							{/if}
						</div>
						{if $smarty.request.tab == 'burnerRatio'}
							<div class="button_float_left">
								<input type='button' id='edit' class='button' value='Edit' onclick="location.href='?action=edit&category=nox&facilityID={$request.id}&tab=burnerRatio'">
							</div>
						{/if}
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
								<input type="button" value="Export to PDF" class="button" onclick="location.href = document.location.href.replace('#','') + '&export=true'">
							</div>
						{/if}
						{/if}
                    {*/EXPORT PAGE*}

							{if ($request.category == "facility" and $request.bookmark == "department" || $request.bookmark == "product")}
								<div class="button_float_left">
									{*<div class="button_alpha new_product_button">*}
									<input type='button'  class='button' value='New Product' onclick="location.href='?action=addNewProduct&category={$request.category}&id={$request.id}'">
								</div>
							{/if}

							{if ($request.category == "department" and $request.bookmark == "equipment")}
								<div class="button_float_left">
									{*<div class="button_alpha new_product_button">*}
									<input type='button' class='button' Value='New Product' onclick="location.href='?action=addNewProduct&category={$request.category}&id={$request.id}'">
								</div>
							{/if}




							{*INDICATOR*}
							<!--{if $request.category == 'department' || $request.category == 'facility' && $request.bookmark != 'inventory'}
								<div class="button_float_right">
									{include file="tpls:tpls/vocIndicator.tpl" emissionLog='true'}
								</div>
							{/if}-->
							<!--<div style="clear: both;"> </div>-->
							{if $request.category == 'department' || $request.category == 'facility'}
							<div class="button_float_right">
							{*INSERT_AFTER_VOC_GAUGE*}
							{*Stupid Smarty does not support class constants*}
							{*blocksToInsert.1 is equal to Controller::INSERT_AFTER_VOC_GAUGE*}
								{if $blocksToInsert.1|@count > 0}
									{foreach from=$blocksToInsert.1 item="blockPath"}
										{include file="tpls:$blockPath"}
									{/foreach}
								{/if}
							{*/INSERT_AFTER_VOC_GAUGE*}
							</div>
							{/if}
							
							{if $request.bookmark != 'emissionGraphs'}
								<input type="hidden" name="category" value="{$childCategory}">
								{if $request.category != 'root'}
									<input type="hidden" name="{$request.category}ID" value="{$request.id}">
								{/if}
								{if $request.bookmark == 'nox' || $request.bookmark == 'inventory' }
									<input type="hidden" name="tab" value="{$request.tab}">
								{/if}
							{else}

								<input type="hidden" name="action" value="browseCategory" />
								<input type="hidden" name="category" value="{$request.category}" />
								<input type="hidden" name="id" value="{$request.id}" />
								<input type="hidden" name="bookmark" value="{$request.bookmark}" />
							{/if}
							<!--<div style="clear: both;"> </div>-->
							
							{if $request.category == 'department' || ($request.category == 'facility' && $request.bookmark == 'department')}
							<div class="button_float_right">
								<table>
									{if $request.category == 'department' || $request.category == 'facility' && $request.bookmark != 'inventory'}
										{include file="tpls:tpls/vocIndicator.tpl" emissionLog='true'}
									{/if}
									{*INSERT_AFTER_NOX_GAUGE*}
									{*Stupid Smarty does not support class constants*}
									{*blocksToInsert.4 is equal to Controller::INSERT_AFTER_VOC_GAUGE*}
									{if $blocksToInsert.4|@count > 0}
										{foreach from=$blocksToInsert.4 item="blockPath"}
											{include file="tpls:$blockPath"}
										{/foreach}
									{/if}
									{*/INSERT_AFTER_NOX_GAUGE*}
									</table>
							</div>
							{/if}
						</div>
							
					</div>


					<em class="bb"><b>&nbsp;</b></em>



				</div>
			</div>
			<div class="br_10"></div>
			{/if}
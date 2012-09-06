<div align="center" class="control_panel_padd">
    <div class="control_panel" class="logbg" align="left">
        <div class="control_panel_tl">
            <div class="control_panel_tr">
                <div class="control_panel_bl">
                    <div class="control_panel_br">
                        <div class="control_panel_center padd7">
                        	<input type="button" class="button" value="<< Back" onclick="{if $backUrl}location.href='{$backUrl}';{else}history.go(-1);return true;{/if}"> 
                        	{if $request.category != "product"}
                            	{if $request.category=="company" 	&& $permissions.company.edit || 
									$request.category=="facility" 	&& $permissions.facility.edit || 
									$request.category=="department" && $permissions.department.edit || 
									$request.category=="equipment" 	&& $permissions.equipment.edit || 
									
									$request.category=="inventory"							  || 
									$request.category=="mix" 		&& $permissions.data.edit || 
									$request.category=="nox" 		&& $permissions.data.edit && $request.departmentID || 
									$request.category=="logbook" || 
									$request.category=="workOrder" ||
									($request.category=="wastestorage" && $data->active!=0)}	
									{*$request.category=="accessory" 	&& $permissions.data.edit ||*}							
									{if $editUrl}
										{if $order.order_status != 3 && $order.order_status != 4}
											<input type="button" class="button" value="Edit" onclick="location.href='{$editUrl}'">
										{/if}	
									{else}
									<input type="button" class="button" value="Edit" onclick="location.href='?action=edit&category={$request.category}&id={$request.id}'">
									{/if}								                            
								{/if}
								{if $request.category=="company" 	&& $permissions.company.delete || 
									$request.category=="facility" 	&& $permissions.facility.delete || 
									$request.category=="department" && $permissions.department.delete || 
									$request.category=="equipment" 	&& $permissions.equipment.delete || 
									$request.category=="workOrder" ||
									 
									$request.category=="mix" 		&& $permissions.data.delete
								}
								{*$request.category=="accessory" 	&& $permissions.data.delete ||*}
									{if $deleteUrl}
									<input type="button" class="button" value="Delete" onclick="location.href='{$deleteUrl}'">
									{elseif $request.tab!="orders" }									
                            		<input type="button" class="button" value="Delete" onclick="location.href='?action=deleteItem&category={$request.category}&id={$request.id}&departmentID={$request.departmentID}'">
									{/if}
                                {/if}
								
								{if $request.category == "mix" and $request.action !== 'viewPFPDetails'}
									<input type="button" name="createLabel" class="button" value="Create Label" onclick="location.href='?action=createLabel&category={$request.category}&id={$request.id}'"/>
								{/if}	
								
								{if $request.category == "workOrder"}
									<input type="button" name="createLabel" class="button" value="Create Label" onclick="location.href='?action=createLabel&category={$request.category}&id={$request.id}&facilityID={$request.facilityID}'"/>
								{/if}
																			
								{if $request.category=="wastestorage"}
									{if $deleteORrestore eq "delete"}
										<br>
										<form name="wasteStorage"  method = "get">
											<input type='hidden' name='action' value='deleteItem'>
											<input type='hidden' name='category' value='wastestorage'>
											<input type='hidden' name='facilityID' value='{$request.facilityID}'>
											<input type='hidden' name='id' value='{$request.id}'>
											<input type='hidden' name='delete' value='1'>
											<input type="submit" class="button" value="Delete" >
											<input type='text' name='dateDeleted' id='calendar2' />
										</form>
									{/if}
										
									{if $deleteORrestore eq "restore"}
										<input type="button" class="button" value="Restore" onclick="location.href='{$restoreUrl}'" />
									{/if}
									{if $data->active!='0'}
										<form name="wasteStorage" method = "get">
											<input type='hidden' name='action' value='deleteItem'>
											<input type='hidden' name='category' value='wastestorage'>
											<input type='hidden' name='facilityID' value='{$request.facilityID}'>
											<input type='hidden' name='id' value='{$request.id}'>
											<input type='hidden' name='delete' value='1'>
											<input type='submit' class='button' id='empty' value='Empty'  />
											<input type='text' name='dateEmpty' id='calendar' />
										</form>
									{/if}
								{/if}
                            {/if}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
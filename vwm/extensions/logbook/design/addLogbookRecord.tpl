{if $color eq "green"}
	{include file="tpls:tpls/notify/greenNotify.tpl" text=$message}
{/if}
{if $color eq "orange"}
	{include file="tpls:tpls/notify/orangeNotify.tpl" text=$message}
{/if}
{if $color eq "blue"}
	{include file="tpls:tpls/notify/blueNotify.tpl" text=$message}
{/if}

<form name='addLogbook' enctype="multipart/form-data"  action={if $request.action == 'addItem'}'?action=addItem&category=logbook&facilityID={$request.facilityID}'{else}'?action=edit&category=logbook&facilityID={$request.facilityID}&id={$request.id}'{/if} method='post'>
	<div class="padd7">
			<table class="users" align="center" cellpadding="0" cellspacing="0">
				<tr class="users_u_top_size users_top">
					<td class="users_u_top">
						<span >Add Logbook record</span>
					</td>
					<td class="users_u_top_r">
						&nbsp;
					</td>
				</tr>
				
				<tr>
					<td class="border_users_l border_users_b border_users_r" width='30%'>Type</td>
					<td class="border_users_b border_users_r">
			
						<select name='typeOfRecord' {if $request.action == 'edit'}style='display:none;'{/if} >
							{section name=i loop=$constAction}
								<option value='{$constAction[i]}' {if $logbookType==$constAction[i]}selected="selected"{/if}>{$constAction[i]}</option>
							{/section}				
						</select>	
						{if $request.action == 'edit'}{$data->type}{/if}
					</td>
				</tr>
				
				<tr >
					<td class="border_users_l border_users_b border_users_r">Date (mm/dd/yyyy)</td>
					<td class="border_users_b border_users_r">
						<div align='left'>
							<input type="text" name="date" id="calendar1" class="calendarFocus" value='{$data->date}'/>
						</div>
						{*ERORR*}
						{if $validation.summary=='failed'}													
							{if $validation.date!=null}
								<div class="error_img"><span class="error_text">{$validation.date}</span></div>
							{/if}					
						{/if}
						{*/ERORR*} 			
					</td>
				</tr>
				
				<tr id="tr_installed" style='display:none;'>
					<td class="border_users_l border_users_b border_users_r" height="20">Installed</td>
					<td class="border_users_b border_users_r">
						<input type="radio" name="setFilter" id="installed" value="installed" {if $data->installed==true}checked{/if}>			
					</td>
				</tr>
				
				<tr id="tr_removed" style='display:none;'>
					<td class="border_users_l border_users_b border_users_r" height="20">Removed</td>
					<td class="border_users_b border_users_r">
						 <input type="radio" name="setFilter" id="removed" value="removed" {if $data->installed!=true}checked{/if}>			
					</td>
				</tr>
				
				<tr id="tr_filterType" style='display:none;'>
					<td class="border_users_l border_users_b border_users_r">Filter type</td>
					<td class="border_users_b border_users_r">
						 <div align='left'>
						 	<input type="text" name="filterType" id="filterType" value='{$data->filter_type}'>
						 </div>
						 {*ERORR*}
						 {if $validation.summary=='failed'}													
							{if $validation.filter_type!=null}
								<div class="error_img"><span class="error_text">{$validation.filter_type}</span></div>
							{/if}					
						 {/if}
						 {*/ERORR*}			
					</td>
				</tr>
				
				<tr id="tr_filterSize" style='display:none;'>
					<td class="border_users_l border_users_b border_users_r">Filter size</td>
					<td class="border_users_b border_users_r">
						 <input type="text" name="filterSize" id="filterSize" value='{$data->filter_size}'>			
					</td>
				</tr>
				
				<tr id="tr_description" style='display:none;'>
					<td class="border_users_l border_users_b border_users_r">Description</td>
					<td class="border_users_b border_users_r">
						<textarea cols="50" rows="5" name="description" id="description">{$data->description}</textarea>			
					</td>
				</tr>
				
				<tr id="tr_operator" style='display:none;'>
					<td class="border_users_l border_users_b border_users_r">Operator</td>
					<td class="border_users_b border_users_r">
						<div align='left'>
							<input type='text' name='operator' id='operator' value='{$data->operator}'/>
						</div>
						{*ERORR*}
						 {if $validation.summary=='failed'}													
							{if $validation.operator!=null}
								<div class="error_img" ><span class="error_text">{$validation.operator}</span></div>
							{/if}					
						 {/if}
						 {*/ERORR*}				
					</td>
				</tr>
				
				<tr id="tr_reason" style='display:none;'>
					<td class="border_users_l border_users_b border_users_r">Reason</td>
					<td class="border_users_b border_users_r">
						 <div align='left'>
						 	<input type='text' name='reason' id='reason' value='{$data->reason}'/>	
						 </div>
						 {*ERORR*}
						 {if $validation.summary=='failed'}													
							{if $validation.reason!=null}
								<div class="error_img"><span class="error_text">{$validation.reason}</span></div>
							{/if}					
						 {/if}
						 {*/ERORR*}				
					</td>
				</tr>
				
				<tr id="tr_action" style='display:none;'>
					<td class="border_users_l border_users_b border_users_r">Action</td>
					<td class="border_users_b border_users_r">
						 <div align='left'>
							<input type='text' name='action' id='action' value='{$data->action}'/>
						 </div>
						 {*ERORR*}
						 {if $validation.summary=='failed'}													
							{if $validation.action!=null}
								<div class="error_img"><span class="error_text">{$validation.action}</span></div>
							{/if}					
						 {/if}
						 {*/ERORR*}					
					</td>
				</tr>
				
				<tr id='tr_department' style='display:none;'>
					<td class="border_users_l border_users_b border_users_r">Department</td>
					<td class="border_users_b border_users_r">
						<select name='department' id='department'>				
							{section name=i loop=$departmentList}
								<option value='{$departmentList[i].id}' {if $data->department_id==$departmentList[i].id}selected="selected"{/if}>{$departmentList[i].name}</option>
							{/section}				
						</select>				
					</td>
				</tr>
				
				<tr id='tr_equipment' style='display:none;'>
					<td class="border_users_l border_users_b border_users_r">Equipment</td>
					<td class="border_users_b border_users_r">
						<select name='equipment' id='equipment'></select>				
					</td>
				</tr>
				
				<tr id='tr_upload' style='display:none;'>
					<td class="border_users_l border_users_b border_users_r">Upload document</td>
					<td class="border_users_b border_users_r">
						<div align='left'>
							<input type="file" name='upload'/>
						</div>	
						{*ERORR*}
						 {if $validation.summary=='failed'}													
							{if $validation.upload!=null}
								<div class="error_img" ><span class="error_text">{$validation.upload}</span></div>
							{/if}					
						 {/if}
						 {*/ERORR*}		
					</td>
				</tr>
				
				<tr>
			        <td height="20" class="users_u_bottom"></td>
			        <td height="20" class="users_u_bottom_r"></td>
			   	</tr>		
			</table>
			
			<br>
			<div width='100%' align='right'>	
				<input type='submit' name='save' value='Save' class='button' />		
				<input type='button' name='cancel' value='Cancel' class="button" onclick="location='?action=browseCategory&category=facility&id={$request.facilityID}&bookmark=logbook'" />
				<span style="padding-right:50">&nbsp;</span>		
			</div>
	</div>
</form>
<script type="text/javascript">var setEquipment="{$data->equipment_id}" </script>
<script type="text/javascript" src="modules/js/jquery-ui-1.8.2.custom/js/jquery-1.4.2.min.js"></script>
<script type="text/javascript" src="modules/js/jquery-ui-1.8.2.custom/js/jquery-ui-1.8.2.custom.min.js"></script>
<script type="text/javascript" src="modules/js/addLogbookRecord.js"></script>

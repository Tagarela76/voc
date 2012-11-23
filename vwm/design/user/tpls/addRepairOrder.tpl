{literal}
<script type="text/javascript">
	$(function() {
		//	global reminderPage object defined at manageReminders.js
		repairOrderPage.facilityId = {/literal} {$request.facilityID} {literal};
        repairOrderPage.woId = {/literal} {$request.id} {literal};
	});
</script>
{/literal}
<div id="notifyContainer">
	{if $color eq "green"}
		{include file="tpls:tpls/notify/greenNotify.tpl" text=$message}
	{/if}
	{if $color eq "orange"}
		{include file="tpls:tpls/notify/orangeNotify.tpl" text=$message}
	{/if}
	{if $color eq "blue"}
		{include file="tpls:tpls/notify/blueNotify.tpl" text=$message}
	{/if}
</div>
	
<div class="padd7">
	<form action='' name="addRepairOrder" onsubmit="return false;">
		<table class="users addCompany" cellpadding="0" cellspacing="0" align="center">
			<tr class="users_header_orange">
				<td>
					<div class="users_header_orange_l"><div><b>{if $request.action eq "addItem"}Adding for a new {$repairOrderLabel}{else}Editing {$repairOrderLabel}{/if}</b></div></div>
				</td>
				<td>
					<div class="users_header_orange_r"><div>&nbsp;</div></div>
				</td>	
			</tr>
			
			<tr class="border_users_b border_users_r">		
				<td class="border_users_l" height="20" width="15%">
					{$repairOrderLabel} number:
				</td>
				<td>
					<div align="left">
						<input id='repairOrderNumber' type='text' name='name' value='{$data->number|escape}' maxlength="64">
					</div>					
			     		{*ERROR*}					
							<div id="error_number" class="error_img" style="display:none;"><span class="error_text">Error!</span></div>					    						
							<div id="error_number_alredyExist" class="error_img" style="display:none;"><span class="error_text">Entered number is alredy in use!</span></div>
						{*/ERROR*}									
													
				</td>					
			</tr>
			
			<tr class="border_users_b border_users_r">			
				<td height="20" class="border_users_l">
					{$repairOrderLabel} description:
				</td>
				<td>
					<div align="left">
						<textarea id='repairOrderDescription' name='repairOrderDescription' cols="49" rows="5">{$data->description|escape}</textarea>
					</div>							
			     				{*ERROR*}					
								<div id="error_description" class="error_img" style="display:none;"><span class="error_text">Error!</span></div>
							    {*/ERROR*}						    						
				</td>					
			</tr>
			
			<tr class="border_users_b border_users_r">			
				<td height="20" class="border_users_l">
					Customer Name:
				</td>
				<td>
					<div align="left">
						<input id='repairOrderCustomerName' type='text' name='repairOrderCustomerName' value='{$data->customer_name|escape}' maxlength="30">
					</div>							
			     				{*ERROR*}					
								<div id="error_customer_name" class="error_img" style="display:none;"><span class="error_text">Error!</span></div>
							    {*/ERROR*}						    						
				</td>					
			</tr>
			<tr class="border_users_b border_users_r">			
				<td height="20" class="border_users_l">
					{$repairOrderLabel} Status:
				</td>
				<td>
					<div align="left">
						<input id='repairOrderStatus' type='text' name='repairOrderStatus' value='{$data->status|escape}' maxlength="30">
					</div>							
			     				{*ERROR*}					
								<div id="error_status" class="error_img" style="display:none;"><span class="error_text">Error!</span></div>
							    {*/ERROR*}						    						
				</td>					
			</tr>
			<tr class="border_users_b border_users_r">			
				<td height="20" class="border_users_l">
					{$repairOrderLabel} VIN number:
				</td>
				<td>
					<div align="left">
						<input id='repairOrderVin' type='text' name='repairOrderVin' value='{$data->vin|escape}' maxlength="30">
					</div>							
			     				{*ERROR*}					
								<div id="error_vin" class="error_img" style="display:none;"><span class="error_text">Error!</span></div>
							    {*/ERROR*}						    						
				</td>					
			</tr>
			<tr height="10px">
				<td height="20" class="border_users_l">
					Departments:
				</td>
                <td>
					<div align="left">
					<div id="departments2wo_list">{$woDepartmentsName} 
						<input type='hidden' name='woDepartments_id' id='woDepartments_id' value="{$woDepartments}" />
					</div>
					<a href="#" onclick="repairOrderPage.manageRepairOrder.openDialog();">edit</a>
                    </div>
                    {*ERROR*}					
                    <div id="error_woDepartments" class="error_img" style="display:none;"><span class="error_text">This value should not be blank.</span></div>	
                    {*/ERROR*}
                </td>    
			</tr>
			<tr class="border_users_l border_users_r">
				<td colspan="2">&nbsp;</td>
			</tr>
			
			<tr>
				<td height="20" class="users_u_bottom">&nbsp;</td>
				<td height="20" class="users_u_bottom_r">&nbsp;</td>
			</tr>
		</table>
				
		
		<table cellpadding="6" cellspacing="0" align="center" width="95%">
			<tr>
				<td>
		{*BUTTONS*}
		<div align="right">
			<input type='button' name='cancel' class="button" value='Cancel' 
				{if $request.action eq "addItem"} onClick="location.href='?action=browseCategory&category=facility&id={$request.facilityID}&bookmark=repairOrder'"
			
				{elseif $request.action eq "edit"} onClick="location.href='?action=viewDetails&category=repairOrder&id={$request.id}&facilityID={$data->facility_id}'"
				{/if}
			>
			<input type='submit' name='save' class="button" value='Save' onClick="saveRepairOrderDetails();">						
		</div>
		
		{*HIDDEN*}
		<input type='hidden' name='action' value={$request.action}>		
		{if $request.action eq "addItem"}
			<input type='hidden' name='facility_id' value='{$request.facilityID}'>
		{/if}			
		{if $request.action eq "edit"}
			<input type="hidden" name="id" value="{$data->facility_id|escape}">
		{/if}
		<input type="hidden" name="work_order_id" id="work_order_id" value="{$data->id|escape}">
		
		</form>
						</td>
			</tr>
		</table>
</div>
<div id="setDepartmentToWoContainer" title="Set department to work order" style="display:none;">Loading ...</div>	
{include file="tpls:tpls/pleaseWait.tpl" text=$pleaseWaitReason}	
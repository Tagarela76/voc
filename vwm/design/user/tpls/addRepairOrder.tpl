{literal}
<script type="text/javascript">
	$(function() {
        
    $('#calendar').datepicker({ dateFormat: '{/literal}{$dataChain->getFromTypeController('getFormatForCalendar')}{literal}' }); 
		//	global reminderPage object defined at manageReminders.js
		repairOrderPage.facilityId = {/literal} {$facilityDetails.facility_id} {literal};
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
    <form id="addRepairOrder" name="addRepairOrder" action='{$sendFormAction}' method="post">	
	<!--<form action='' name="addRepairOrder" onsubmit="return false;">-->
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
						<input id='repairOrderNumber' type='text' name='number' value='{$data->number|escape}' maxlength="64">
					</div>					
			     	{foreach from=$violationList item="violation"}
                        {if $violation->getPropertyPath() eq 'number' || $violation->getPropertyPath() eq 'uniqueName'}							
                        {*ERROR*}					
                        <div class="error_img" style="float: left;"><span class="error_text">{$violation->getMessage()}</span></div>
                        {*/ERROR*}						    
                        {/if}
                    {/foreach}									
													
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
			     	{foreach from=$violationList item="violation"}
                        {if $violation->getPropertyPath() eq 'repairOrderDescription' || $violation->getPropertyPath() eq 'uniqueName'}							
                        {*ERROR*}					
                        <div class="error_img" style="float: left;"><span class="error_text">{$violation->getMessage()}</span></div>
                        {*/ERROR*}						    
                        {/if}
                    {/foreach}						    						
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
			     	{foreach from=$violationList item="violation"}
                        {if $violation->getPropertyPath() eq 'repairOrderCustomerName' || $violation->getPropertyPath() eq 'uniqueName'}							
                        {*ERROR*}					
                        <div class="error_img" style="float: left;"><span class="error_text">{$violation->getMessage()}</span></div>
                        {*/ERROR*}						    
                        {/if}
                    {/foreach}						    						
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
			     	{foreach from=$violationList item="violation"}
                        {if $violation->getPropertyPath() eq 'repairOrderStatus' || $violation->getPropertyPath() eq 'uniqueName'}							
                        {*ERROR*}					
                        <div class="error_img" style="float: left;"><span class="error_text">{$violation->getMessage()}</span></div>
                        {*/ERROR*}						    
                        {/if}
                    {/foreach}							    						
				</td>					
			</tr>
            {if $data instanceof VWM\Apps\WorkOrder\Entity\AutomotiveWorkOrder}
			<tr class="border_users_b border_users_r">			
				<td height="20" class="border_users_l">
					{$repairOrderLabel} VIN number:
				</td>
				<td>
					<div align="left">
						<input id='repairOrderVin' type='text' name='repairOrderVin' value='{$data->vin|escape}' maxlength="30">
					</div>							
			     	{foreach from=$violationList item="violation"}
                        {if $violation->getPropertyPath() eq 'repairOrderVin' || $violation->getPropertyPath() eq 'uniqueName'}							
                        {*ERROR*}					
                        <div class="error_img" style="float: left;"><span class="error_text">{$violation->getMessage()}</span></div>
                        {*/ERROR*}						    
                        {/if}
                    {/foreach}					    						
				</td>	
			</tr>
			{/if}
            
            <tr class="border_users_b border_users_r">			
				<td height="20" class="border_users_l">
					 Overhead:
				</td>
				<td>
					<div align="left">
						<input id='repairOrderOverhead' type='text' name='repairOrderOverhead' value='{$data->getOverhead()}' maxlength="30">
                        <select id='overheadUnitType' name='overheadUnitType' style="width:40px">
                            <option value="0" {if $data->getOverheadUnitType() == 0}selected{/if}>$</option>
                            <option value="1" {if $data->getOverheadUnitType() == 1}selected{/if}>%</option>
                        </select>
					</div>
                    {foreach from=$violationList item="violation"}
                        {if $violation->getPropertyPath() eq 'overhead' || $violation->getPropertyPath() eq 'uniqueName'}							
                        {*ERROR*}					
                        <div class="error_img" style="float: left;"><span class="error_text">{$violation->getMessage()}</span></div>
                        {*/ERROR*}						    
                        {/if}
                    {/foreach}	
				</td>	
			</tr>
            
            <tr class="border_users_b border_users_r">			
				<td height="20" class="border_users_l">
					 Profit:
				</td>
				<td>
					<div align="left">
						<input id='repairOrderProfit' type='text' name='repairOrderProfit' value='{$data->getProfit()}' maxlength="30">
                        <select id = 'profitUnitType' name='profitUnitType' style="width:40px">
                            <option value="0" {if $data->getProfitUnitType() == 0}selected{/if}>$</option>
                            <option value="1" {if $data->getProfitUnitType() == 1}selected{/if}>%</option>
                        </select>
					</div>
                    {foreach from=$violationList item="violation"}
                        {if $violation->getPropertyPath() eq 'profit' || $violation->getPropertyPath() eq 'uniqueName'}							
                        {*ERROR*}					
                        <div class="error_img" style="float: left;"><span class="error_text">{$violation->getMessage()}</span></div>
                        {*/ERROR*}						    
                        {/if}
                    {/foreach}	
				</td>	
			</tr>
            
            {if $action == 'add'}
                <tr class="border_users_b border_users_r">
                    <td height="20" class="border_users_l">
                        Select Process:
                    </td>
                    <td>
                        <select name = 'woProcessId'>
                            {foreach from=$processList item='process'}
                                <option value='{$process->id|escape}'>{$process->name|escape}</option>
                            {/foreach}
                        </select>
                        <!--<input type='hidden' name='woProcessId' id='woProcessId' value="{$processList[0]->id}"/>-->
                    </td>
                </tr>
            {/if} 
            
            {*Select creation time*}
            <tr class="border_users_b border_users_r">			
				<td height="20" class="border_users_l">
					Creation Time:
				</td>
				<td>
                    <div align="left">
                        <input type="text" name="creationTime" id="calendar" class="calendarFocus" value='{$creationTime|escape}'/>
                    </div>						
				</td>					
			</tr>
			{if $request.parent_category == 'facility'}
			<tr height="10px">
				<td height="20" class="border_users_l">
					Departments:
				</td>
                <td>										
					<div align="left">
						<div id="departments2wo_list">{$woDepartmentsName}
							<input type='hidden' name='woDepartments_id' id='woDepartments_id' value="{$woDepartments|escape}" />
						</div>
						<a href="#" onclick="repairOrderPage.manageRepairOrder.openDialog();">edit</a>
                    </div>
						{if $woDepartmentsError eq 'true'}
						{*ERROR*}
						<div class="error_img" style="float: left;"><span class="error_text">This value should not be blank.</span></div>
						{*/ERROR*}
						{/if}					
                </td>    
			</tr>
			{else}
			<tr height="10px">
				<td height="20" class="border_users_l">
					&nbsp;
				</td>
                <td class="border_users_r">
					<input type='hidden' name='woDepartments_id' id='woDepartments_id' value="{$woDepartments|escape}" />
					&nbsp;
                </td>
			</tr>
			{/if}
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
			{if $request.category == 'department'}
			<input type='button' name='cancel' class="button" value='Cancel'
				{if $request.action eq "addItem"} onClick="location.href='?action=browseCategory&category=department&id={$request.departmentID}&bookmark=repairOrder'"
				{elseif $request.action eq "edit"} onClick="location.href='?action=viewDetails&category=repairOrder&id={$request.id}&departmentId={}'"
				{/if}
			>
			{elseif $request.category == 'facility'}
			<input type='button' name='cancel' class="button" value='Cancel'
				{if $request.action eq "addItem"} onClick="location.href='?action=browseCategory&category=facility&id={$request.facilityID}&bookmark=repairOrder'"
				{elseif $request.action eq "edit"} onClick="location.href='?action=viewDetails&category=repairOrder&id={$request.id}&facilityID={$data->facility_id}'"
				{/if}
			>
			{/if}
            <input type='submit' name='save' class="button" value='Save'>
			<!--<input type='submit' name='save' class="button" value='Save' onClick="saveRepairOrderDetails();">-->						
		</div>
		
		{*HIDDEN*}
		<input type='hidden' name='action' value={$request.action}>		
		{if $request.action eq "addItem"}
			<input type='hidden' name='facility_id' value='{$facilityDetails.facility_id|escape}'>
		{/if}			
		{if $request.action eq "edit"}
			<input type="hidden" name="id" value="{$data->facility_id|escape}">
		{/if}
		<input type="hidden" name="work_order_id" id="work_order_id" value="{$data->id|escape}">
		
		</form>
						</td>
			</tr>
		</table>
 </form>       
</div>
<div id="setDepartmentToWoContainer" title="Set department to work order" style="display:none;">Loading ...</div>	
{include file="tpls:tpls/pleaseWait.tpl" text=$pleaseWaitReason}	

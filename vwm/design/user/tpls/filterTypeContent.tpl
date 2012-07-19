
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

<div style="padding:7px;">

	<form id="addEquipmentForm" name="addEquipment">		
        
		<table class="users" align="center" cellpadding="0" cellspacing="0">
			<tr class="users_header_orange">
				<td height="30" width="30%">
					<div class="users_header_orange_l"><div><span ><b>{if $request.action eq "addItem"}Adding for a new equipment{else}Editing equipment{/if}</b></span></div></div>
				</td>
				<td><div class="users_header_orange_r"><div>&nbsp;</div></div>				
				</td>								
			</tr>				
						
			<tr>
				<td class="border_users_l border_users_b" width="15%" height="20">
					Description
				</td>
				<td class="border_users_l border_users_b border_users_r">
					<div align="left" >	<input type='text' name='equip_desc' value='{$data.equip_desc}'></div>												
								{*ERORR*}
									<div id="error_equip_desc" style="width:80px;margin:2px 0px 0px 5px; display:none;" align="left"><img src='design/user/img/alert1.gif' height=16  style="float:left;">
									<font style="float:left;vertical-align:bottom;color:red;margin:1px 0px 0px 5px;">Error!</font></div>
								{*/ERORR*}														
				</td>
			</tr>
									
			<tr>
				<td class="border_users_l border_users_b" height="20">
					Permit
				</td>
				<td class="border_users_l border_users_b border_users_r">
					<div align="left" >	<input type='text' name='permit' value='{$data.permit}'></div>				
								{*ERORR*}
									<div id="error_permit" style="width:80px;margin:2px 0px 0px 5px; display:none;" align="left"><img src='design/user/img/alert1.gif' height=16  style="float:left;">
									<font style="float:left;vertical-align:bottom;color:red;margin:1px 0px 0px 5px;">Error!</font></div>
								{*/ERORR*}							
				</td>
			</tr>
		
			<tr>
				<td class="border_users_l border_users_b" height="20">
					Expire {*if $data.date_type=='d-m-Y g:iA'}(dd-mm-yyyy){else}(mm/dd/yyyy){/if*}({$data.expire->getFormatInfo()})				
				</td>
				<td class="border_users_l border_users_b border_users_r">
					<div align="left" >
						<input type="text" name="expire_date" id="calendar1" class="calendarFocus" value='{$data.expire->formatOutput()}'/>							
					</div>												
								{*ERORR*}
									<div id="error_expire_date"style="width:80px;margin:2px 0px 0px 5px; display:none;" align="left"><img src='design/user/img/alert1.gif' height=16  style="float:left;">
									<font style="float:left;vertical-align:bottom;color:red;margin:1px 0px 0px 5px;">Error!</font></div>
								{*/ERORR*}			
				</td>
			</tr>
			
			<tr>
				<td class="border_users_l border_users_b" height="20">
					Daily
				</td>
				<td class="border_users_l border_users_b border_users_r">
					<div align="left" >	<input type='text' name='daily' id='daily' value='{$data.daily}'"></div>															
								{*ERORR*}
									<div id="error_daily" style="width:80px;margin:2px 0px 0px 5px; display:none;" align="left"><img src='design/user/img/alert1.gif' height=16  style="float:left;">
									<font style="float:left;vertical-align:bottom;color:red;margin:1px 0px 0px 5px;">Error!</font></div>
								{*/ERORR*}				
				</td>
			</tr>
			
			<tr>
				<td class="border_users_l border_users_b" height="20">
					Department track :
				</td>
				<td class="border_users_l border_users_b border_users_r">
					<div align="left" >					
						<input type="checkbox" name="dept_track" value="yes" {if $data.dept_track!="no"}checked="yes"{/if}">
					</div>
				</td>
			</tr>
						
			<tr>
				<td class="border_users_l border_users_b" height="20">
					Facility track :
				</td>
				<td class="border_users_l border_users_b border_users_r">
					<div align="left" >					
						<input type="checkbox" name="facility_track" value="yes" {if $data.facility_track!=="no"}checked="yes"{/if}">
					</div>
				</td>
			</tr>
			
			{if $show.inventory}
				{include file="tpls:inventory/design/addEquipment.tpl" inventory=$inventoryList inventoryDet=$inventoryDet data=$data}
			{/if}
			
            <tr>
             	 <td height="20" class="users_u_bottom">
                 </td>
                 <td height="20" class="users_u_bottom_r">
                 </td>
            </tr>
		
		</table>
	
	{*BUTTONS*}	
	<div align="right" class="margin5">
		<input type='button' name='cancel' class="button" value='Cancel' 
				{if $request.action eq "addItem"} onClick="location.href='?action=browseCategory&category=department&id={$request.id}&bookmark=equipment'"
				{elseif $request.action eq "edit"} onClick="location.href='?action=browseCategory&category=department&id={$data.department_id}&bookmark=equipment'"
				{/if}
			>
		<input type='button' name='save' class="button" value='Save' onClick="saveEquipmentDetails();">		
	</div>
	
	
	{*HIDDEN*}
	<input type='hidden' name='action' value='{$request.action}'>	
	{if $request.action eq "addItem"}
		<input type='hidden' name='department_id' value='{$request.departmentID}'>
	{/if}	
	{if $request.action eq "edit"}
		<input type="hidden" name="id" value="{$request.id}">
		<input type='hidden' name='department_id' value='{$data.department_id}'>
	{/if}
		
</form>


	<script type='text/javascript'>
	  var dateType='{$data.date_type}';
	  {literal}
	  $(document).ready(function () { 
        
        	//if (dateType=='d-m-Y g:iA')
		//	{
        		//popUpCal.dateFormat = 'DMY-';					 
				$('#calendar1').datepicker({ dateFormat: '{/literal}{$data.expire->getFromTypeController('getFormatForCalendar')}{*dd-mm-yy*}{literal}' }); 
		//	}		
        //    else
		//	{
        //    	 $('#calendar1').datepicker({ dateFormat: 'mm/dd/yy' });            
            	//popUpCal.dateFormat = 'MDY/';
		//	}						    		   	
      });
	  {/literal}
    </script>


</div>

{include file="tpls:tpls/pleaseWait.tpl" text=$pleaseWaitReason}		

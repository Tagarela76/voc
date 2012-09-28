
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

	<form id="addReminders" name="addReminders" action='{$sendFormAction}' method="post">		
		<table class="users" align="center" cellpadding="0" cellspacing="0">
			<tr class="users_header_orange">
				<td height="30" width="30%">
					<div class="users_header_orange_l"><div><span ><b>{if $request.action eq "addItem"}Adding for a new Reminder{else}Editing Reminder{/if}</b></span></div></div>
				</td>
				<td>
					<div class="users_header_orange_r"><div>&nbsp;</div></div>				
				</td>								
			</tr>				

			<tr>
				<td class="border_users_l border_users_b" width="15%" height="20">
					Name
				</td>
				<td class="border_users_l border_users_b border_users_r">
					<div align="left" style="float: left;">	<input type='text' name='name' value='{$data->name|escape}'></div>												
						{foreach from=$violationList item="violation"}
							{if $violation->getPropertyPath() eq 'name' || $violation->getPropertyPath() eq 'uniqueName'}							
							{*ERROR*}					
							<div class="error_img" style="float: left;"><span class="error_text">{$violation->getMessage()}</span></div>
							{*/ERROR*}						    
							{/if}
						{/foreach}	
				</td>
			</tr>

			<tr>
				<tr>
				<td class="border_users_l border_users_b" width="15%" height="20">
					Date
				</td>
				<td class="border_users_l border_users_b border_users_r">
					<div align="left" style="float: left;">	<input type='text' name="date" id="reminderDate" class="calendarFocus" value='{$data->date|escape}'></div>												
						{foreach from=$violationList item="violation"}
							{if $violation->getPropertyPath() eq 'date'}							
							{*ERROR*}					
							<div class="error_img" style="float: left;"><span class="error_text">{$violation->getMessage()}</span></div>
							{*/ERROR*}						    
							{/if}
						{/foreach}	
				</td>
			</tr>
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
				   {if $request.action eq "addItem"} onClick="location.href='?action=browseCategory&category=facility&id={$request.id}&bookmark=reminders'"
				   {elseif $request.action eq "edit"} onClick="location.href='?action=viewDetails&category=reminders&id={$request.id}&facilityID={$request.facilityID}'"  
				   {/if}
				   >
			<input type='submit' name='save' class="button" value='Save'>		
		</div>


		{*HIDDEN*}
		<input type='hidden' name='action' value='{$request.action}'>	
		{if $request.action eq "edit"}
			<input type='hidden' id='id' name='id' value='{$data->id}'>
		{/if}
		<input type='hidden' id='facility_id' name='facility_id' value='{$request.facilityID}'>

</form>
</div>

{literal}
	<script type="text/javascript">
		$(document).ready(function(){      
			//	set calendar
			$('#reminderDate').datetimepicker({ dateFormat: '{/literal}{$dataChain->getFromTypeController('getFormatForCalendar')}{literal}'});												
		});
	</script>
{/literal}
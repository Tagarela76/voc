
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

	<form id="addForm" name="addForm" action='{$sendFormAction}' method="post">		
    {if $request.tab eq 'burner'}    
		<table class="users" align="center" cellpadding="0" cellspacing="0">
			<tr class="users_header_orange">
				<td height="30" width="30%">
					<div class="users_header_orange_l"><div><span ><b>{if $request.action eq "addItem"}Adding for a new burner{else}Editing burner{/if}</b></span></div></div>
				</td>
				<td>
					<div class="users_header_orange_r"><div>&nbsp;</div></div>				
				</td>								
			</tr>				
						
			<tr>
				<td class="border_users_l border_users_b" width="15%" height="20">
					Model
				</td>
				<td class="border_users_l border_users_b border_users_r">
					<div align="left" style="float: left;">	<input type='text' name='model' value='{$data.model}'></div>												
							{if $validStatus.summary eq 'false'}
							{if $validStatus.model eq 'failed'}
			     				{*ERROR*}					
                        		<div class="error_img" style="float: left;"><span class="error_text">Error!</span></div>
							    {*/ERROR*}
						    {elseif $validStatus.model eq 'alreadyExist'}
								<div class="error_img"><span class="error_text">Entered model is already in use!</span></div>
							{/if}
						    {/if}
																	
				</td>
			</tr>
			
			<tr>
				<td class="border_users_l border_users_b" width="15%" height="20">
					Serial Number
				</td>
				<td class="border_users_l border_users_b border_users_r">
					<div align="left" style="float: left;">	<input type='text' name='serial' value='{$data.serial}'></div>												
							{if $validStatus.summary eq 'false'}
							{if $validStatus.serial eq 'failed'}
			     				{*ERROR*}					
                        		<div class="error_img"style="float: left;"><span class="error_text">Error!</span></div>
							    {*/ERROR*}
						    {elseif $validStatus.serial eq 'alreadyExist'}
								<div class="error_img"><span class="error_text">Entered serial is already in use!</span></div>
							{/if}
						    {/if}
																	
				</td>
			</tr>	
			
			
			<tr>
				<td class="border_users_l border_users_b" width="15%" height="20">
					Manufacturer
				</td>
				<td class="border_users_l border_users_b border_users_r">
					<div align="left" style="float: left;">	
						<select name='manufacturer_id'>
							{section loop=$burnerManufacturers name=i}
								<option value="{$burnerManufacturers[i].id}" {if $burnerManufacturers[i].id == $data.manufacturer_id}selected{/if}> {$burnerManufacturers[i].name}</option>
							{/section}
						</select>																						
				</td>
			</tr>

			<tr>
				<td class="border_users_l border_users_b" width="15%" height="20">
					Input
				</td>
				<td class="border_users_l border_users_b border_users_r">
					<div align="left" style="float: left;">	<input type='text' name='input' id='input' value='{$data.input}'></div>												
							{if $validStatus.summary eq 'false'}
							{if $validStatus.input eq 'failed'}
			     				{*ERROR*}					
                        		<div class="error_img"style="float: left;"><span class="error_text">Error!</span></div>
							    {*/ERROR*}
						    {/if}
							{/if}
																	
				</td>
			</tr>
<script type="text/javascript">
								$("#input").numeric();
</script>
			<tr>
				<td class="border_users_l border_users_b" width="15%" height="20">
					Output
				</td>
				<td class="border_users_l border_users_b border_users_r">
					<div align="left" style="float: left;">	<input type='text' name='output' id='output' value='{$data.output}'></div>												
							{if $validStatus.summary eq 'false'}
							{if $validStatus.output eq 'failed'}
			     				{*ERROR*}					
                        		<div class="error_img"style="float: left;"><span class="error_text">Error!</span></div>
							    {*/ERROR*}

						    {/if}
							{/if}
																	
				</td>
			</tr>
<script type="text/javascript">
								$("#output").numeric();
</script>			
			<tr>
				<td class="border_users_l border_users_b" width="15%" height="20">
					BTUS/KW'S per hour rating 

				</td>
				<td class="border_users_l border_users_b border_users_r">
					<div align="left"style="float: left;" >	<input type='text' name='btu' id='btu' value='{$data.btu}'></div>												
							{if $validStatus.summary eq 'false'}
							{if $validStatus.btu eq 'failed'}
			     				{*ERROR*}					
                        		<div class="error_img"style="float: left;"><span class="error_text">Error!</span></div>
							    {*/ERROR*}
						    {elseif $validStatus.btu eq 'alreadyExist'}
								<div class="error_img"><span class="error_text">Entered btu is already in use!</span></div>
							{/if}
						    {/if}
																	
				</td>
			</tr>	
<script type="text/javascript">
								$("#btu").numeric();
</script>			
			<tr>
              	 <td height="20" class="users_u_bottom">
                 </td>
                 <td height="20" class="users_u_bottom_r">
                 </td>
            </tr>
			
		
		</table>
	{else}
		<table class="users" align="center" cellpadding="0" cellspacing="0">
			<tr class="users_header_orange">
				<td height="30" width="30%">
					<div class="users_header_orange_l"><div><span ><b>{if $request.action eq "addItem"}Adding for a new NOx emissions{else}Editing NOx Emission{/if}</b></span></div></div>
				</td>
				<td>
					<div class="users_header_orange_r"><div>&nbsp;</div></div>				
				</td>								
			</tr>				
						
			<tr>
				<td class="border_users_l border_users_b" width="15%" height="20">
					Description
				</td>
				<td class="border_users_l border_users_b border_users_r">
					<div align="left" style="float: left;">	<input type='text' name='description' value='{$data.description}'></div>												
							{if $validStatus.summary eq 'false'}
							{if $validStatus.description eq 'failed'}
			     				{*ERROR*}					
                        		<div class="error_img" style="float: left;"><span class="error_text">Error!</span></div>
							    {*/ERROR*}
						    {elseif $validStatus.description eq 'alreadyExist'}
								<div class="error_img"><span class="error_text">Entered description is already in use!</span></div>
							{/if}
						    {/if}
																	
				</td>
			</tr>
			
			<tr>
				<td class="border_users_l border_users_b" width="15%" height="20">
					Burner
				</td>
				<td class="border_users_l border_users_b border_users_r">
					<div align="left" style="float: left;">	
						<select name="burner_id" id="burner_id">
							{section  loop=$burners name=i}
								<option value="{$burners[i].burner_id}" {if $burners[i].burner_id == $data.burner_id}selected{/if}> {$burners[i].model}&nbsp;>&nbsp;{$burners[i].serial} </option>
							{/section}	
						</select>	
					</div>												
							{if $validStatus.summary eq 'false'}
							{if $validStatus.burner_id eq 'failed'}
			     				{*ERROR*}					
                        		<div class="error_img"style="float: left;"><span class="error_text">Error!</span></div>
							    {*/ERROR*}
							{/if}
						    {/if}
																	
				</td>
			</tr>	
			
			
			<tr>
				<td class="border_users_l border_users_b" width="15%" height="20">
					Start Time
				</td>
				<td class="border_users_l border_users_b border_users_r">
					<div align="left" style="float: left;">	<input type='text' name="start_time" id="calendar1" class="calendarFocus" value='{$data.start_time}'></div>												
							{if $validStatus.summary eq 'false'}
							{if $validStatus.start_time eq 'failed'}
			     				{*ERROR*}					
                        		<div class="error_img"style="float: left;"><span class="error_text">Error!</span></div>
							    {*/ERROR*}
							{/if}
						    {/if}
																	
				</td>
			</tr>

			<tr>
				<td class="border_users_l border_users_b" width="15%" height="20">
					End Time
				</td>
				<td class="border_users_l border_users_b border_users_r">
					<div align="left" style="float: left;">	<input type='text' name="end_time" id="calendar2" class="calendarFocus" value='{$data.end_time}'></div>												
							{if $validStatus.summary eq 'false'}
							{if $validStatus.end_time eq 'failed'}
			     				{*ERROR*}					
                        		<div class="error_img"style="float: left;"><span class="error_text">Error!</span></div>
							    {*/ERROR*}
						    {/if}
							{/if}
																	
				</td>
			</tr>
			<tr>
				<td class="border_users_l border_users_b" width="15%" height="20">
					Gas Unit Used
				</td>
				<td class="border_users_l border_users_b border_users_r">
					<div align="left" style="float: left;">	<input type='text' name='gas_unit_used' id='gas_unit_used' value='{$data.gas_unit_used}'></div>												
							{if $validStatus.summary eq 'false'}
							{if $validStatus.gas_unit_used eq 'failed'}
			     				{*ERROR*}					
                        		<div class="error_img"style="float: left;"><span class="error_text">Error!</span></div>
							    {*/ERROR*}

						    {/if}
							{/if}
																	
				</td>
			</tr>
<script type="text/javascript">
								$("#gas_unit_used").numeric();
</script>			
			<tr>
				<td class="border_users_l border_users_b" width="15%" height="20">
					Notes

				</td>
				<td class="border_users_l border_users_b border_users_r">
					<div align="left"style="float: left;" >	<textarea name='note' id='note' >{$data.note}</textarea></div>												
															
				</td>
			</tr>	
		
			<tr>
              	 <td height="20" class="users_u_bottom">
                 </td>
                 <td height="20" class="users_u_bottom_r">
                 </td>
            </tr>
			
		
		</table>		
	{/if}	
	{*BUTTONS*}	
	<div align="right" class="margin5">
		<input type='button' name='cancel' class="button" value='Cancel' 
				{if $request.action eq "addItem"} onClick="location.href='?action=browseCategory&category=department&id={$request.departmentID}&bookmark=nox&tab={$request.tab}'"
				{elseif $request.action eq "edit"} onClick="location.href='?action=browseCategory&category=department&id={$request.departmentID}&bookmark=nox&tab={$request.tab}'"
				{/if}
		>
		<input type='submit' name='save' class="button" value='Save'>		
	</div>
	
	
	{*HIDDEN*}
	<input type='hidden' name='action' value='{$request.action}'>
	<input type='hidden' name='tab' value='{$request.tab}'>
	{if $request.action eq "addItem"}
		<input type='hidden' id='department_id' name='department_id' value='{$request.departmentID}'>
	{/if}	
	{if $request.action eq "edit"}
		{if $data.burner_id}<input type="hidden" name="burner_id" value="{$data.burner_id}">{/if}
		{if $data.nox_id}<input type="hidden" name="nox_id" value="{$data.nox_id}">{/if}
		<input type='hidden' id='department_id' name='department_id' value='{$request.departmentID}'>
	{/if}
		
	</form>
</div>

				{literal}
				<script type="text/javascript">
					$(document).ready(function(){      
						//	set calendar
						$('#calendar1').datetimepicker({ dateFormat: '{/literal}{$dataChain->getFromTypeController('getFormatForCalendar')}{literal}'});												
						$('#calendar2').datetimepicker({ dateFormat: '{/literal}{$dataChain->getFromTypeController('getFormatForCalendar')}{literal}'});
					});
				</script>
				{/literal}
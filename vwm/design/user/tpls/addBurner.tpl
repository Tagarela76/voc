
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
					<div align="left" >	<input type='text' name='model' value='{$data.model}'></div>												
							{if $validStatus.summary eq 'false'}
							{if $validStatus.model eq 'failed'}
			     				{*ERROR*}					
                        		<div class="error_img"><span class="error_text">Error!</span></div>
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
					<div align="left" >	<input type='text' name='serial' value='{$data.serial}'></div>												
							{if $validStatus.summary eq 'false'}
							{if $validStatus.serial eq 'failed'}
			     				{*ERROR*}					
                        		<div class="error_img"><span class="error_text">Error!</span></div>
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
					<div align="left" >	<input type='text' name='manufacturer_id' value='{$data.manufacturer_id}'></div>												
							{if $validStatus.summary eq 'false'}
							{if $validStatus.manufacturer_id eq 'failed'}
			     				{*ERROR*}					
                        		<div class="error_img"><span class="error_text">Error!</span></div>
							    {*/ERROR*}
						    {elseif $validStatus.manufacturer_id eq 'alreadyExist'}
								<div class="error_img"><span class="error_text">Entered manufacturer_id is already in use!</span></div>
							{/if}
						    {/if}
																	
				</td>
			</tr>
			
			<tr>
				<td class="border_users_l border_users_b" width="15%" height="20">
					BTUS/KW'S per hour rating 

				</td>
				<td class="border_users_l border_users_b border_users_r">
					<div align="left" >	<input type='text' name='btu' value='{$data.btu}'></div>												
							{if $validStatus.summary eq 'false'}
							{if $validStatus.btu eq 'failed'}
			     				{*ERROR*}					
                        		<div class="error_img"><span class="error_text">Error!</span></div>
							    {*/ERROR*}
						    {elseif $validStatus.btu eq 'alreadyExist'}
								<div class="error_img"><span class="error_text">Entered btu is already in use!</span></div>
							{/if}
						    {/if}
																	
				</td>
			</tr>			
			<tr>
              	 <td height="20" class="users_u_bottom">
                 </td>
                 <td height="20" class="users_u_bottom_r">
                 </td>
            </tr>
			
		
		</table>
	{else}
		
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


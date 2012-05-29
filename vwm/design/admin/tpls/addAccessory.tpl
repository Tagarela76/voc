
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

	<form id="addAccessoryForm" name="addAccessory" action='{$sendFormAction}' method="post">		
        
		<table class="users" align="center" cellpadding="0" cellspacing="0">
			<tr class="users_header_orange">
				<td height="30" width="30%">
					<div class="users_header_orange_l"><div><span ><b>{if $request.action eq "addItem"}Adding for a new accessory{else}Editing accessory{/if}</b></span></div></div>
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
					<div align="left" >	<input type='text' name='accessory_desc' value='{$data.name}'></div>												
							{if $validStatus.summary eq 'false'}
							{if $validStatus.name eq 'failed'}
			     				{*ERROR*}					
                        		<div class="error_img"><span class="error_text">Error!</span></div>
							    {*/ERROR*}
						    {elseif $validStatus.name eq 'alreadyExist'}
								<div class="error_img"><span class="error_text">Entered name is already in use!</span></div>
							{/if}
						    {/if}
																	
				</td>
			</tr>
			<tr>
				<td class="border_users_l border_users_b" height="20">
					Jobber:
				</td>
				<td class="border_users_l border_users_b border_users_r">
					<div align="left" >
						<select id="selectJobber" name="jobber_id" >
							{if isset($jobbers)}
								{foreach item=jobber from=$jobbers}
									<option value="{$jobber->jobber_id}" {if $jobber->jobber_id == $data.jobber_id} selected='selected' {/if} >{$jobber->name}</option>
								{/foreach}
							{/if}
	
						</select>
					</div>							
			
				</td>
			</tr>
			<tr>
				<td class="border_users_l border_users_b" height="20">
					Vendor:
				</td>
				<td class="border_users_l border_users_b border_users_r">
					<div align="left" >
						<select id="selectVendor" name="vendor_id" >
							{if isset($vendors)}
								{foreach from=$vendors item=vendor}
									<option value="{$vendor.vendor_id}" {if $vendor.vendor_id == $data.vendor_id} selected='selected' {/if} >{$vendor.name}</option>
								{/foreach}
							{/if}
	
						</select>
					</div>							
			
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
				{if $request.action eq "addItem"} onClick="location.href='?action=browseCategory&category=accessory'"
				{elseif $request.action eq "edit"} onClick="location.href='?action=browseCategory&category=accessory&id={$request.id}'"
				{/if}
		>
		<input type='submit' name='save' class="button" value='Save'>		
	</div>
	
	
	{*HIDDEN*}
	<input type='hidden' name='action' value='{$request.action}'>	
	{if $request.action eq "addItem"}
		<input type='hidden' id='department_id' name='department_id' value='{$request.id}'>
	{/if}	
	{if $request.action eq "edit"}
		<input type="hidden" name="accessory_id" value="{$data.id}">
		<input type='hidden' id='department_id' name='department_id' value='{$request.departmentID}'>
	{/if}
		
	</form>
</div>


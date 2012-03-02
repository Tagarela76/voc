<script type="text/javascript">
	var accessLevel='{$bookmark}';
</script>
<script type="text/javascript" src='modules/js/jquery-ui-1.8.2.custom/js/jquery-1.4.2.min.js'></script>
<script type='text/javascript' src='modules/js/registration.js'></script>



<div style="padding:7px;">
	{if $color eq "green"}
		{include file="tpls:tpls/notify/greenNotify.tpl" text=$message}
	{/if}
	{if $color eq "orange"}
		{include file="tpls:tpls/notify/orangeNotify.tpl" text=$message}
	{/if}
	{if $color eq "blue"}
		{include file="tpls:tpls/notify/blueNotify.tpl" text=$message}
	{/if}
	{if $color eq "blue2" && $itemsCount == 0}
		{include file="tpls:tpls/notify/blueNotify.tpl" text=$message}
	{/if}
	
	<form method='post' action='admin.php?action={$request.action}&category=users&bookmark={$bookmark}{if $request.action neq "addItem"}&id={$request.id}{/if}'>
		<table class="users" align="center" cellpadding="0" cellspacing="0">
			<tr class="users_u_top_size users_top">
				<td class="users_u_top" height="30" width="20%">
					{if $request.action == 'edit'}
						<span >Edit user</span>
					{else}
						<span >Registering user</span>
					{/if}
				</td>
				<td class="users_u_top_r" >
					&nbsp;
				</td>					
			</tr>

			<tr>
				<td class="border_users_l border_users_b" height="20">
					Login*:
					
				</td>
				<td class="border_users_l border_users_b border_users_r">
					<div align="left" >
						<input type='text' name='accessname' value='{$reg_field.accessname}'>
					</div>
					{if $check.accessname == 'failed'}
						<div style="width:130px;vertical-align: bottom;">
							<img src='design/user/img/alert.gif' height=17  style="border:0px solid white;float:left;margin:0px;">
							Wrong entering
						</div>
					{elseif $check.accessname == 'alreadyExist'}
						<div style="width:130px;vertical-align: bottom;color:red;">
							<img src='design/user/img/alert.gif' height=17  style="border:0px solid white;float:left;margin:0px;">
							This login is already in use!
						</div>
					{/if}
				</td>
			</tr>
						
			<tr>
							<td class="border_users_l border_users_b" height="20">
								Password:
							</td>
							<td class="border_users_l border_users_b border_users_r">
							<div align="left" >	<input type='password' name='password' value='{$reg_field.password}'></div>
							{if $check.password == 'different'}
								<div style="width:130px;vertical-align: bottom;"><img src='design/user/img/alert.gif' height=17  style="border:0px solid white;float:left;margin:0px;">
								Different</div>
							{else}
								{if $check.password == 'failed'}
									<div style="width:130px;vertical-align: bottom;"><img src='design/user/img/alert.gif' height=17  style="border:0px solid white;float:left;margin:0px;">
								Wrong entering</div>
								{/if}
							{/if}
							</td>
						</tr>
						
						<tr>
							<td class="border_users_l border_users_b" height="20">
								Confirm password:
							</td>
							<td class="border_users_l border_users_b border_users_r">
								<div align="left" ><input type='password' name='confirm_password' value='{$reg_field.confirm_password}'></div>
							{if $check.password != 'different' && $check.confirm_password == 'failed'}
								<div style="width:130px;vertical-align: bottom;"><img src='design/user/img/alert.gif' height=17  style="border:0px solid white;float:left;margin:0px;">
								Wrong entering</div>
							{/if}
							</td>
						</tr>
						
						<tr>
							<td class="border_users_l border_users_b" height="20">
								User name**:
							</td>
							<td class="border_users_l border_users_b border_users_r">
							<div align="left" >	<input type='text' name='username' value='{$reg_field.username}'></div>
							{if $check.username== 'failed'}
								<div style="width:130px;vertical-align: bottom;"><img src='design/user/img/alert.gif' height=17  style="border:0px solid white;float:left;margin:0px;">
								Wrong entering</div>
							{/if}
							</td>
						</tr>
						
						<tr>
							<td class="border_users_l border_users_b" height="20">
								Phone:
							</td>
							<td class="border_users_l border_users_b border_users_r">
							<div align="left" >	<input type='text' name='phone' value='{$reg_field.phone}'></div>
							{if $check.phone	== 'failed'}
								<div style="width:130px;vertical-align: bottom;"><img src='design/user/img/alert.gif' height=17  style="border:0px solid white;float:left;margin:0px;">
								Wrong entering</div>
							{/if}
							</td>
						</tr>
						
						<tr>
							<td class="border_users_l border_users_b" height="20">
								Mobile phone:
							</td>
							<td class="border_users_l border_users_b border_users_r">
							<div align="left" >	<input type='text' name='mobile' value='{$reg_field.mobile}'></div>
							{if $check.mobile== 'failed'}
								<div style="width:130px;vertical-align: bottom;"><img src='design/user/img/alert.gif' height=17  style="border:0px solid white;float:left;margin:0px;">
								Wrong entering</div>
							{/if}
							</td>
						</tr>
						
						
						
						<tr>
							<td class="border_users_l border_users_b" height="20">
								Email:
							</td>
							<td class="border_users_l border_users_b border_users_r">
							<div align="left" >	<input type='text' name='email' value='{$reg_field.email}'></div>
							
							
							{if $check.email == 'failed'}
								<div style="width:130px;vertical-align: bottom;"><img src='design/user/img/alert.gif' height=17  style="border:0px solid white;float:left;margin:0px;">
								Wrong entering</div>
							{/if}
							
							
							</td>
							
						</tr>
						
						
						
						
						
						<tr>
							<td class="border_users_l border_users_b" height="20">
								Access level:
							</td>
							<td class="border_users_l border_users_b border_users_r">
							<div align="left" >
							
							{if $bookmark=="company"}
								Company level
								<input type="hidden" name="accesslevel_id" value="0">
							{elseif $bookmark=="facility"}
								Facility level
								<input type="hidden" name="accesslevel_id" value="1">
							{elseif $bookmark=="department"}
								Department level
								<input type="hidden" name="accesslevel_id" value="2">
							{elseif $bookmark=="admin"}
								Superuser level
								<input type="hidden" name="accesslevel_id" value="3">
							{elseif $bookmark=="sales"}
								Sales level
								<input type="hidden" name="accesslevel_id" value="4">	
							{elseif $bookmark=="supplier"}
								Supplier level
								<input type="hidden" name="accesslevel_id" value="5">									
							{/if}
							</div>
							</td>
						</tr>
				{if $bookmark=="supplier"}
							<tr>
							<td class="border_users_l border_users_b" height="20">
								Jobber:
							</td>
							<td class="border_users_l border_users_b border_users_r">
							<div align="left" >
							<select id="selectSupplier" name="jobber_id" >
							{if isset($jobbers)}
								{foreach item=jobber from=$jobbers}
									<option value="{$jobber->jobber_id}" {if $jobber->jobber_id == $reg_field.jobber_id} selected='selected' {/if} >{$jobber->name}</option>
								{/foreach}
							{/if}
	
							</select>
								<span id='facError' class="error_text" style="display:none">Error</span>
							</div>							
							</td>
						</tr>
						
											</tr>
					{/if}							
						
						

					{if $bookmark!="admin" and $bookmark!="sales" and $bookmark!="supplier"}	
							<tr>
							<td class="border_users_l border_users_b" height="20">
								Company:
							</td>
							<td class="border_users_l border_users_b border_users_r">
								<div align="left" >
								<select id="selectCompany" name="company_id">
									{*<option value="">Select a company</option>*}
									{section name=i loop=$company}
										<option value="{$company[i].id}" {if $company[i].id == $reg_field.company_id} selected='selected' {/if} >{$company[i].name}</option>
									{/section}
								</select>
									<span id='compError' class="error_text" style="display:none">Error</span>
								</div>
								
							</td>
						</tr>
				{/if}
				{if $bookmark=="facility" || $bookmark=="department"}
							<tr>
							<td class="border_users_l border_users_b" height="20">
								Facility:
							</td>
							<td class="border_users_l border_users_b border_users_r">
							<div align="left" >
							<select id="selectFacility" name="facility_id" >
							{if isset($facility)}
								{section name=i loop=$facility}
									<option value="{$facility[i].id}" {if $facility[i].id == $reg_field.facility_id} selected='selected' {/if} >{$facility[i].name}</option>
								{/section}
							{/if}
	
							</select>
								<span id='facError' class="error_text" style="display:none">Error</span>
							</div>							
							</td>
						</tr>
						
											</tr>
					{/if}	
					{if $bookmark=="department"}	
							<tr>
							<td class="border_users_l border_users_b" height="20">
								Department:
							</td>
							<td class="border_users_l border_users_b border_users_r">
							<div align="left" >	<select id="selectDepartment" name="department_id">
							{if isset($department)}
								{section name=i loop=$department}
									<option value="{$department[i].id}" {if $department[i].id == $reg_field.department_id} selected='selected' {/if} >{$department[i].name}</option>
								{/section}
							{/if}
	
							</select>
							<span id='depError' class="error_text" style="display:none">Error</span>
							</div>
							
							</td>
						</tr>
					{/if}	
					
					{*if $bookmark=="supplier"}	
							<tr>
							<td class="border_users_l border_users_b" height="20">
								Supplier:
							</td>
							<td class="border_users_l border_users_b border_users_r">
							<div align="left" >	<select id="selectDepartment" name="department_id">
							{if isset($department)}
								{section name=i loop=$department}
									<option value="{$department[i].id}" {if $department[i].id == $reg_field.department_id} selected='selected' {/if} >{$department[i].name}</option>
								{/section}
							{/if}
	
							</select>
							<span id='depError' class="error_text" style="display:none">Error</span>
							</div>
							
							</td>
						</tr>
					{/if*}					
						
						<tr>
             				 <td height="20" class="users_u_bottom">
             	 				&nbsp;
                			 </td>
                			 <td height="20" class="users_u_bottom_r">
                 				&nbsp;
                 			</td>
           				</tr>	
			</table>

	<div align="right">
		<br>
		{if $request.action eq "edit"}					
			<input type='submit' id='saveButton' class="button" name='save' value='Save'>
		{else}
			<input type='submit' id='saveButton' class="button" name='save' value='Register'>
		{/if}
		<span style="padding-right:50">&nbsp;</span>
	</div>
<br />* You'll use this to log in to the voc web manager. Other user will no see your access name	
<br />** This is your real name. We'll can print it on some reports or show it to other users		
</form>				
</div>	

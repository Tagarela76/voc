	{if $color eq "green"}
		{include file="tpls:tpls/notify/greenNotify.tpl" text=$message}
	{/if}
	{if $color eq "orange"}
		{include file="tpls:tpls/notify/orangeNotify.tpl" text=$message}
	{/if}
	{if $color eq "blue"}
		{include file="tpls:tpls/notify/blueNotify.tpl" text=$message}
	{/if}
<div style="padding:7px;">
		<table class="users" align="center" cellpadding="0" cellspacing="0">
			<tr class="users_u_top_size users_top" >
				<td class="users_u_top" width="27%" height="30" >
					<span >{if $currentOperation eq "addItem"}Adding for a new country{else}Editing country{/if}</span>
				</td>
				<td class="users_u_top_r" >
					&nbsp;
				</td>					
			</tr>
			
<form method='post' action='admin.php?action={$request.action}&category=country{if $request.action neq "addItem"}&id={$request.id}{/if}'>
			<tr height="10px">
		
							<td class="border_users_l border_users_b" height="20">
								Country Name:
							</td>
							<td class="border_users_l border_users_b border_users_r">
							<div align="left" >	<input type='text' name='country_name' value='{$data.country_name}'></div>
							
							{if $validStatus.summary eq 'false'}
							{if $validStatus.country_name eq 'failed'}
			     				{*ERROR*}					
								<div style="width:80px;margin:2px 0px 0px 5px;" align="left"><img src='design/user/img/alert1.gif' height=16  style="float:left;">
								<font style="float:left;vertical-align:bottom;color:red;margin:1px 0px 0px 5px;">Error!</font></div>
							    {*/ERROR*}
						    {elseif $validStatus.country_name eq 'alredyExist'}
								<div style="width:220px;margin:2px 0px 0px 5px;" align="left"><img src='design/user/img/alert1.gif' height=16  style="float:left;">
							    <font style="float:left;vertical-align:bottom;color:red;margin:1px 0px 0px 5px;">Entered name is alredy in use!</font></div>
							{/if}
						    {/if}
								
							</td>
							<tr>
							<td class="border_users_l border_users_b" height="20">
								Format of date:
							</td>
							<td class="border_users_l border_users_b border_users_r">
							<div align="left" >	
							
								<select name="date_type" id="selectDateType">
								   <option value='d-m-Y g:iA' {if $data.date_type == 'd-m-Y g:iA'}selected="selected"{/if}> dd-mm-yyyy </option>
								   <option value='m/d/Y g:iA' {if $data.date_type == 'm/d/Y g:iA'}selected="selected"{/if}> mm/dd/yyyy </option>
								</select>
							</div>
							</td>
							</tr>
						
						<tr>
							<td class="border_users_l border_users_b" height="20">
								New state :
							</td>
							<td class="border_users_l border_users_b border_users_r">
							<div align="left" >	<input type='text' name='state_name' value='{$data.state_name}'></div>
							
							{if $validStatus.summary eq 'false'}
							{if $validStatus.state_name eq 'failed'}
						
								{*ERORR*}
									<div style="width:80px;margin:2px 0px 0px 5px;" align="left"><img src='design/user/img/alert1.gif' height=16  style="float:left;">
									<font style="float:left;vertical-align:bottom;color:red;margin:1px 0px 0px 5px;">Error!</font></div>
								{*/ERORR*}
							{elseif $validStatus.state_name eq 'alredyExist'}
								<div style="width:220px;margin:2px 0px 0px 5px;" align="left"><img src='design/user/img/alert1.gif' height=16  style="float:left;">
							    <font style="float:left;vertical-align:bottom;color:red;margin:1px 0px 0px 5px;">Entered name is alredy in use!</font></div>
							{/if}
							{/if}
						
							</td>
						</tr>
					
						</tr>			
					
					
						</tr>
						
						<tr>
             				 <td height="20" class="users_u_bottom">
             	 				&nbsp;
                			 </td>
                			 <td height="20" class="users_u_bottom_r">
                 				&nbsp;
                 			</td>
           				</tr>
			</table>
			
			<br>
			<div align="right">
				<input type='submit' name='save' class="button" value='Add state to country'>	
				<input type='submit' name='save' class="button" value='Save'>
				<input type='button' name='cancel' class="button" value='Cancel' 
					{if $action.request=='edit'} onclick='location.href="admin.php?action=viewDetails&category=country&id={$request.id}"'{/if}
					{if $action.request=='addItem'} onclick='location.href="admin.php?action=browseCategory&category=tables&bookmark=country"'{/if}>
				<span style="padding-right:50">&nbsp;</span>
			</div>
		
		
		
{if $stateCount > 0}		
<div style="padding:7px;">
        <table class="users" align="center" cellpadding="0" cellspacing="0">
        	<tr class="users_u_top_size users_top" >
				<td class="users_u_top" width="27%" height="30" >
					Select
				</td>
				<td class="users_u_top_r" >
					State Name
				</td>
			</tr>
				 
			{section name=i loop=$stateCount}						
			<tr >
				<td class="border_users_l border_users_b" height="20">
					<input type="checkbox"  checked="checked" value="{$statesAdded.states[i].state_id}" name="state_id_{$smarty.section.i.index}" onclick="return CheckCB(this);">
				</td>
				<td class="border_users_l border_users_b border_users_r">
          
			            <div style="width:100%;">

					        <input type='text' name='state_name_{$smarty.section.i.index}' value='{$statesAdded.states[i].name}'>
							{if $validStatus.summary eq 'false'}
							{if $validStatus[i].name eq 'failed'}
						
								{*ERORR*}
									<div style="width:80px;margin:2px 0px 0px 5px;" align="left"><img src='design/user/img/alert1.gif' height=16  style="float:left;">
									<font style="float:left;vertical-align:bottom;color:red;margin:1px 0px 0px 5px;">Error!</font></div>
								{*/ERORR*}
							{/if}
							{/if}

							
						</div >
		
</td>

				
						</tr>
{/section}		
		
		

						
						
						<tr>
             				 <td height="20" class="users_u_bottom">
             	 				&nbsp;
                			 </td>
                			 <td height="20" class="users_u_bottom_r">
                 				&nbsp;
                 			</td>
           				</tr>
						</table>			
		
{/if}		
		
		
		 <input type='hidden' name='stateCount' value='{$stateCount}'>
		
		{*  <input type='hidden' name='itemID' value='country'>
		<input type='hidden' name='categoryID' value='class'>
		<input type='hidden' name='action' value={$currentOperation}>
		{if $currentOperation eq "updateItem"}
			<input type="hidden" name="id" value="{$ID}">
		{/if} *}
		</form>
</div>	

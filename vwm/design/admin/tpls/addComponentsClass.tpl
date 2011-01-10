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
			<tr class="users_u_top_size users_top">
				<td class="users_u_top" width="27%" height="30" >
					<span >{if $currentOperation eq "addItem"}Adding for a new compound{else}Editing compound{/if}</span>
				</td>
				<td class="users_u_top_r" >
					&nbsp;
				</td>				
			</tr>
<form method='POST' action='admin.php?action={$currentOperation}&categoryID=class&itemID=components{if $currentOperation neq "addItem"}&id={$ID}{/if}'>
			<tr height="10px">
							
							<td class="border_users_l border_users_b" height="20">
								Case Number:
							</td>
							<td class="border_users_l border_users_b border_users_r">
							<div align="left" >	<input type='text' name='cas' value='{$data.cas}'></div>
							
							{if $validStatus.summary eq 'false'}
							{if $validStatus.cas eq 'failed'}
			     				{*ERROR*}					
								<div style="width:80px;margin:2px 0px 0px 5px;" align="left"><img src='design/user/img/alert1.gif' height=16  style="float:left;">
								<font style="float:left;vertical-align:bottom;color:red;margin:1px 0px 0px 5px;">Error!</font></div>
							    {*/ERROR*}
							{elseif $validStatus.cas eq 'alredyExist'}
								<div style="width:220px;margin:2px 0px 0px 5px;" align="left"><img src='design/user/img/alert1.gif' height=16  style="float:left;">
							    <font style="float:left;vertical-align:bottom;color:red;margin:1px 0px 0px 5px;">Entered name is alredy in use!</font></div>
						    {/if}
						    {/if}
								
							</td>
						</tr>
						
						<tr height="10px">
							
							<td class="border_users_l border_users_b" height="20">
								EC Number:
							</td>
							<td class="border_users_l border_users_b border_users_r">
							<div align="left" >	<input type='text' name='EINECS' value='{$data.EINECS}'></div>
							
							{if $validStatus.summary eq 'false'}
							{if $validStatus.EINECS eq 'failed'}
			     				{*ERROR*}					
								<div style="width:80px;margin:2px 0px 0px 5px;" align="left"><img src='design/user/img/alert1.gif' height=16  style="float:left;">
								<font style="float:left;vertical-align:bottom;color:red;margin:1px 0px 0px 5px;">Error!</font></div>
							    {*/ERROR*}
							{elseif $validStatus.EINECS eq 'alredyExist'}
								<div style="width:220px;margin:2px 0px 0px 5px;" align="left"><img src='design/user/img/alert1.gif' height=16  style="float:left;">
							    <font style="float:left;vertical-align:bottom;color:red;margin:1px 0px 0px 5px;">Entered name is alredy in use!</font></div>
						    {/if}
						    {/if}
								
							</td>
						</tr>
							
						<tr>			
							<td class="border_users_l border_users_b" height="20">
								Description:
							</td>
							<td class="border_users_l border_users_b border_users_r">
							<div align="left" >	<input type='text' name='description' value='{$data.description}'></div>
							
								{if $validStatus.summary eq 'false'}
								{if $validStatus.description eq 'failed'}
				     				{*ERROR*}					
									<div style="width:80px;margin:2px 0px 0px 5px;" align="left"><img src='design/user/img/alert1.gif' height=16  style="float:left;">
									<font style="float:left;vertical-align:bottom;color:red;margin:1px 0px 0px 5px;">Error!</font></div>
								    {*/ERROR*}
								{/if}
							    {/if}
								
							</td>
					
						
						<tr class="users_u_top_size users_top_lightgray">			
							<td  height="20">
								<u><b>Agencies:</b></u>
							</td>
							<td >
							<div align="left">
							&nbsp;
							</div>
							</td>
						</tr>
						
						{section name=i loop=$data.agencies}
						<tr>
							<td class="border_users_l border_users_b" height="20">
								{$data.agencies[i].name}:
							</td>
							<td class="border_users_l border_users_b border_users_r">
							<div align="left">
								<input type="checkbox" name="agency_{$smarty.section.i.index}" value="yes" {if $data.agencies[i].control=="yes"}checked{/if}>
							</div>
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
			<br>
			<div align="right">
				<input type='submit' name='save' class="button" value='Save'>
				<input type='button' name='cancel' class="button" value='Cancel' 
					{if $currentOperation=='edit'} onclick='location.href="admin.php?action=viewDetails&categoryID=class&itemID=components&id={$ID}"'{/if}
					{if $currentOperation=='addItem'} onclick='location.href="admin.php?action=browseCategory&categoryID=class&itemID=components"'{/if}>
				<span style="padding-right:50">&nbsp;</span>
			</div>	
		
			{section name=i loop=$data.agencies}
				<input type='hidden' name='agency_name_{$smarty.section.i.index}' value='{$data.agencies[i].name}'>
			{/section}	
			</form>
</div>	

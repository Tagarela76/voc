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
					<span >{if $request.action eq "addItem"}Adding for a new tab{else}Editing tab{/if}</span>
				</td>
				<td class="users_u_top_r" >
					&nbsp;
				</td>					
			</tr>
<form method='POST' action='admin.php?action={$request.action}&category=tabs{if $request.action neq "addItem"}&id={$request.id}{/if}'>
			<tr height="10px">
		
							<td class="border_users_l border_users_b" height="20">
								Tab:
							</td>
							<td class="border_users_l border_users_b border_users_r">
							<div align="left" >{$data.id}</div>																						
							</td>
					
						</tr>
						
						<tr height="10px">
		
							<td class="border_users_l border_users_b" height="20">
								String:
							</td>
							<td class="border_users_l border_users_b border_users_r">
								<div align="left" >	<input type='text' name='string' value='{$data.string}'></div>
								
							{if !$validStatus}							
			     				{*ERROR*}					
								<div style="width:160px;margin:2px 0px 0px 5px;" align="left"><img src='design/user/img/alert1.gif' height=16  style="float:left;">
								<font style="float:left;vertical-align:bottom;color:red;margin:1px 0px 0px 5px;">Should be less 120 symbols and not empty</font></div>
							    {*/ERROR*}						    							
						    {/if}													
							</td>
					
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
				<input type='submit' name='save' class="button" value='Save'>
				<input type='button' name='cancel' class="button" value='Cancel' 
					onclick='location.href="admin.php?action=browseCategory&category=tables&bookmark=tabs"'>
				<span style="padding-right:50">&nbsp;</span>
			</div>		
		</form>
</div>	
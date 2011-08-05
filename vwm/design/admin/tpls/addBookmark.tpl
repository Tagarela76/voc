<div style="padding:7px;">
<form method='POST' action='admin.php?action={$request.action}&category=bookmarks&bookmark={$request.bookmark}{if $request.subBookmark}&subBookmark={$request.subBookmark}{*else}&subBookmark={$request.bookmark*}{/if}'>
		<table class="users rd" align="center" cellpadding="0" cellspacing="0">
			<tr class="users_u_top_size users_top">
				<td class="users_u_top" height="30" width="20%">
					<span >{if $request.action eq "addItem"}Adding for a new bookmark{else}Editing bookmark{/if}</span>
				</td>
				
				<td class="users_u_top_r">
				</td>				
			
			<tr height="10px">
				<td class="border_users_l border_users_b">
						Name:
				</td>
				<td class="border_users_l border_users_b border_users_r">
					<div align="left" style='display:inline; float:left;'>
						<input type='text' name='name' value='{$data->name}'>  <span style='color:Red;'>*</span>
					</div>
					
					{if $data->errors.name}
					
						<div style="margin:2px 0px 0px 5px;" align="left"><img src='design/user/img/alert1.gif' height=16  style="float:left;">
									<font style="float:left;vertical-align:bottom;color:red;margin:1px 0px 0px 5px;">{$data->errors.name}</font>
						</div>
					{/if}
				</td>				
			</tr>
			
			<tr>
             	 <td height="20" class="users_u_bottom">
             	 	All Fields marked with a red * are required
                 </td>
                 <td height="20" class="users_u_bottom_r">
                 	&nbsp;
                 </td>
            </tr>			
						
						
			</table>
	<div align="right">
		<br>
		<input type='submit' name='save' class="button" value='Save'>
		<input type='button' class="button" id='cancelButton' value='Cancel'>
		<span style="padding-right:50">&nbsp;</span>
		</div>
		
		</form>
</div>

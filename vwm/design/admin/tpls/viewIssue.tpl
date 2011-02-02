	{if $color eq "green"}
		{include file="tpls:tpls/notify/greenNotify.tpl" text=$message}
	{/if}
	
	{if $color eq "orange"}
		{include file="tpls:tpls/notify/orangeNotify.tpl" text=$message}
	{/if}
	
	{if $color eq "blue"}
		{include file="tpls:tpls/notify/blueNotify.tpl" text=$message}
	{/if}
	
<form method="POST" action='admin.php?action=edit&category=issue&id={$request.id}'>			  
		<table class="users" cellpadding="0" cellspacing="0" align="center">
			<tr class="users_top_yellowgreen users_u_top_size" >
				<td class="users_u_top_yellowgreen" width="15%">View details</td>
				<td class="users_u_top_r_yellowgreen" ></td>				
			</tr>					
						
						<tr height="20px">
							<td class="border_users_l border_users_b border_users_r" >
								Issue ID
							</td>
							<td class="border_users_b border_users_r">
								<div align="" >&nbsp;{$issue.issueID}</div>
							</td>
						</tr>
						
						<tr height="20px">
							<td class="border_users_b border_users_l border_users_r">
								Title
							</td>
							<td class="border_users_b border_users_r">
								<div align="left" >	&nbsp;{$issue.title}</div>
							</td>
						</tr>
						
						<tr height="20px">
							<td class="border_users_b border_users_l border_users_r">
								Description
							</td>
							<td class="border_users_b border_users_r">
							<div align="left" >	&nbsp;{$issue.description}</div>
							</td>
						</tr>
						
						<tr height="20px">
							<td class="border_users_b border_users_l border_users_r">
								Author
							</td>
							<td class="border_users_b border_users_r">
							<div align="left" >	&nbsp;{$issue.author}</div>
							</td>
						</tr>
						
						<tr height="20px">
							<td class="border_users_b border_users_l border_users_r">
								Referer
							</td>
							<td class="border_users_b border_users_r">
							<div align="left" >	&nbsp;{$issue.referer}</div>
							</td>
						</tr>
						
						<tr height="20px">
							<td class="border_users_b border_users_l border_users_r">
								Priority
							</td>
							<td class="border_users_b border_users_r">
								<div align="left" >
									&nbsp;
									<select name="priority">
										<option value="low" {if $issue.priority eq "low"} selected="selected" {/if}>Low</option>
										<option value="normal" {if $issue.priority eq "normal"} selected="selected" {/if}>Normal</option>
										<option value="high" {if $issue.priority eq "high"} selected="selected" {/if}>High</option>
									</select>
								</div>
							</td>
						</tr>
						
						<tr height="20px">
							<td class="border_users_b border_users_l border_users_r">
								Status
							</td>
							<td class="border_users_b border_users_r">
							<div align="left" >
								&nbsp;
								<select name="status">
									<option value="completed" {if $issue.status eq "completed"} selected="completed" {/if}>Completed</option>
									<option value="new" {if $issue.status eq "new"} selected="new" {/if}>New</option>
									<option value="ongoing" {if $issue.status eq "ongoing"} selected="ongoing" {/if}>Ongoing</option>
								</select>
							</div>
							</td>
						</tr>
						
				
						<tr>
						    <td   height="20" class="users_u_bottom">
							</td> 
							<td class="users_u_bottom_r">
							</td>
						</tr>
						
</table>	
<div align="center">
<div align="right" class="buttonpadd">
		<input type='submit' name='save' class="button" value='Save'>
</div></div>
	
	{*  <input type='hidden' name='itemID' value='issue'>
	<input type='hidden' name='category' value='issue'>
	<input type='hidden' name='action' value="updateItem">
	<input type="hidden" name="id" value="{$ID}">*}
</div>
</form>	
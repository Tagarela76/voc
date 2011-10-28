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
	<table class="users"  align="center" cellpadding="0" cellspacing="0">
		<tr class="users_top_yellowgreen" >
			<td class="users_u_top_yellowgreen" width="27%" height="30" >
				<span>New User Request. View details</span>
			</td>
			<td class="users_u_top_r_yellowgreen" width="300">
			</td>
		</tr>
		
		<tr>
			<td class="border_users_l border_users_b" height="20">
				Action:
			</td>
			<td class="border_users_l border_users_r border_users_b">
				<div align="left" >&nbsp;{$userRequest->action}</div>
			</td>
		</tr>
		
		<tr>
			<td class="border_users_l border_users_b" height="20">
				User Name:
			</td>
			<td class="border_users_l border_users_r border_users_b">
				<div align="left" >&nbsp;{if $userRequest->action_type eq 'add'}{$userRequest->new_username}{else}{$userRequest->username}{/if}</div>
			</td>
		</tr>	
		
		{if $userRequest->action_type eq 'change'}
		<tr>
			<td class="border_users_l border_users_b" height="20">
				New User Name:
			</td>
			<td class="border_users_l border_users_r border_users_b">
				<div align="left" >&nbsp;{$userRequest->new_username}</div>
			</td>
		</tr>
		{/if}
		
		<tr>
			<td class="border_users_l border_users_b" height="20">
				Access Level:
			</td>
			<td class="border_users_l border_users_r border_users_b">
				<div align="left" >&nbsp;{$userRequest->category_type}</div>
			</td>
		</tr>		
		
		{if $userRequest->category_type eq 'department'}
		<tr>
			<td class="border_users_l border_users_b" height="20">
				Department Name:
			</td>
			<td class="border_users_l border_users_r border_users_b">
				<div align="left" >&nbsp;{$userRequest->department_name}</div>
			</td>
		</tr>	
		<tr>
			<td class="border_users_l border_users_b" height="20">
				Facility Name:
			</td>
			<td class="border_users_l border_users_r border_users_b">
				<div align="left" >&nbsp;{$userRequest->facility_name}</div>
			</td>
		</tr>
		<tr>
			<td class="border_users_l border_users_b" height="20">
				Company Name:
			</td>
			<td class="border_users_l border_users_r border_users_b">
				<div align="left" >&nbsp;{$userRequest->company_name}</div>
			</td>
		</tr>
		{/if}
		
		{if $userRequest->category_type eq 'facility'}
		<tr>
			<td class="border_users_l border_users_b" height="20">
				Facility Name:
			</td>
			<td class="border_users_l border_users_r border_users_b">
				<div align="left" >&nbsp;{$userRequest->facility_name}</div>
			</td>
		</tr>
		<tr>
			<td class="border_users_l border_users_b" height="20">
				Company Name:
			</td>
			<td class="border_users_l border_users_r border_users_b">
				<div align="left" >&nbsp;{$userRequest->company_name}</div>
			</td>
		</tr>
		{/if}
		
		{if $userRequest->category_type eq 'company'}
		<tr>
			<td class="border_users_l border_users_b" height="20">
				Company Name:
			</td>
			<td class="border_users_l border_users_r border_users_b">
				<div align="left" >&nbsp;{$userRequest->company_name}</div>
			</td>
		</tr>	
		{/if}
		
		<tr>
			<td class="border_users_l border_users_b" height="20">
				Request Date:
			</td>
			<td class="border_users_l border_users_r border_users_b">
				<div align="left" >	&nbsp;{$userRequest->date}</div>
			</td>
		</tr>
		
		{*if $userRequest->category_type eq 'facility' || $userRequest->category_type eq 'department'*}
		{if $userRequest->action_type eq 'add'}
		{if $userRequest->category_type neq 'company'}	
		<tr>
			<td class="border_users_l border_users_b" height="20">
				Creator Name:
			</td>
			<td class="border_users_l border_users_r border_users_b">
				<div align="left" >&nbsp;{$userRequest->creator_user}</div>
			</td>
		</tr>
		{/if}
		{else}
		<tr>
			<td class="border_users_l border_users_b" height="20">
				Creator Name:
			</td>
			<td class="border_users_l border_users_r border_users_b">
				<div align="left" >&nbsp;{$userRequest->creator_user}</div>
			</td>
		</tr>	
		{/if}
		
		<tr>
			<td class="border_users_l border_users_b" height="20">
				Status:
			</td>
			<td class="border_users_l border_users_r border_users_b">
				<div align="" >	
					<select>
						<option {if $userRequest->status eq 'new'} selected {/if}>new</option>
						<option {if $userRequest->status eq 'accept'} selected {/if}>accept</option>
						<option {if $userRequest->status eq 'deny'} selected {/if}>deny</option>
					</select>
				</div>
			</td>
		</tr>
		
		<tr>
			<td class="border_users_l border_users_b" height="20">
				Additional Mail Comments:
			</td>
			<td class="border_users_l border_users_r border_users_b">
				<input type="checkbox" id="addComments" onclick="showAddComment();"/>
			</td>
		</tr>
		
		<tr id="addCommentRow" style="display: none;">
			<td class="border_users_l border_users_b" height="20">
				Comments:
			</td>
			<td class="border_users_l border_users_r border_users_b">
				<textarea name="comment" id="addCommentTextArea"  rows="40" class="message" cols="20" wrap="hard" strolling="yes">Comments:</textarea>
			</td>
		</tr>

		<tr>
			<td colspan="2" align="right" class="border_users_l border_users_r">
				<br/>
				<div style="margin-right: 20px;">
					<input type="button" class="button" value="Save"/>
					<input type="button" class="button" value="Cancel" onclick="location.href='{$userRequest->back_url}'"/>
				</div>
			</td>
		</tr>
		
		<tr>
			<td  height="15" class="users_u_bottom">
			</td>
			<td height="15" class="users_u_bottom_r">
			</td>
		</tr>
	</table>
<script>
	{literal}
	$(function() {
	 	$("#addCommentTextArea").focus(function(){
	 		if($(this).val() == "Comments:") {
	 			$(this).val("");
	 		}
	 	});
	 	
	 	$("#addCommentTextArea").focusout(function(){
	 		if($(this).val() == "") {
	 			$(this).val("Comments:");
	 		}
	 	});
	});	
	
	function showAddComment(){
		check = !document.getElementById('addComments').checked;
			console.log(check);
		if (check == true){
			$('#addCommentRow').hide();
		} else {
			$('#addCommentRow').show();
		}	
	}
	{/literal}	
</script>

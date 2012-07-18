{if $color eq "green"}
	{include file="tpls:tpls/notify/greenNotify.tpl" text=$message}
{/if}

{if $color eq "orange"}
	{include file="tpls:tpls/notify/orangeNotify.tpl" text=$message}
{/if}

{if $color eq "blue"}
	{include file="tpls:tpls/notify/blueNotify.tpl" text=$message}
{/if}
<div style="padding: 7px;">
<form id="saveForm" action="admin.php?action=viewDetails&category=userRequest&id={$userRequest->id}" enctype="multipart/form-data" method="post">
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
		
		{if $userRequest->action_type eq 'add'}
		<tr>
			<td class="border_users_l border_users_b" height="20">
				Access Name:
			</td>
			<td class="border_users_l border_users_r border_users_b">
				<div align="left" >&nbsp;{$userRequest->new_accessname}</div>
			</td>
		</tr>
		{/if}
		
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
				<div align="left" >&nbsp;{$userRequest->department_name|escape}</div>
			</td>
		</tr>	
		<tr>
			<td class="border_users_l border_users_b" height="20">
				Facility Name:
			</td>
			<td class="border_users_l border_users_r border_users_b">
				<div align="left" >&nbsp;{$userRequest->facility_name|escape}</div>
			</td>
		</tr>
		<tr>
			<td class="border_users_l border_users_b" height="20">
				Company Name:
			</td>
			<td class="border_users_l border_users_r border_users_b">
				<div align="left" >&nbsp;{$userRequest->company_name|escape}</div>
			</td>
		</tr>
		{/if}
		
		{if $userRequest->category_type eq 'facility'}
		<tr>
			<td class="border_users_l border_users_b" height="20">
				Facility Name:
			</td>
			<td class="border_users_l border_users_r border_users_b">
				<div align="left" >&nbsp;{$userRequest->facility_name|escape}</div>
			</td>
		</tr>
		<tr>
			<td class="border_users_l border_users_b" height="20">
				Company Name:
			</td>
			<td class="border_users_l border_users_r border_users_b">
				<div align="left" >&nbsp;{$userRequest->company_name|escape}</div>
			</td>
		</tr>
		{/if}
		
		{if $userRequest->category_type eq 'company'}
		<tr>
			<td class="border_users_l border_users_b" height="20">
				Company Name:
			</td>
			<td class="border_users_l border_users_r border_users_b">
				<div align="left" >&nbsp;{$userRequest->company_name|escape}</div>
			</td>
		</tr>	
		{/if}
		
		{if $userRequest->action_type eq 'add'}		
		<tr>
			<td class="border_users_l border_users_b" height="20">
				Email:
			</td>
			<td class="border_users_l border_users_r border_users_b">
				<div align="left" >	&nbsp;{$userRequest->email}</div>
			</td>
		</tr>
		
		<tr>
			<td class="border_users_l border_users_b" height="20">
				Phone:
			</td>
			<td class="border_users_l border_users_r border_users_b">
				<div align="left" >	&nbsp;{$userRequest->phone}</div>
			</td>
		</tr>
		
		<tr>
			<td class="border_users_l border_users_b" height="20">
				Mobile:
			</td>
			<td class="border_users_l border_users_r border_users_b">
				<div align="left" >	&nbsp;{$userRequest->mobile}</div>
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
		
		{if $userRequest->action_type eq 'add'}
		{if $userRequest->category_type neq 'company'}	
		<tr>
			<td class="border_users_l border_users_b" height="20">
				Creater Name:
			</td>
			<td class="border_users_l border_users_r border_users_b">
				<div align="left" >&nbsp;{$userRequest->creater_user}</div>
			</td>
		</tr>
		{/if}
		{else}
			{if $userRequest->creater_user neq ''}
		<tr>
			<td class="border_users_l border_users_b" height="20">
				Creater Name:
			</td>
			<td class="border_users_l border_users_r border_users_b">
				<div align="left" >&nbsp;{$userRequest->creater_user}</div>
			</td>
		</tr>	
			{/if}
		{/if}
		
		<tr>
			<td class="border_users_l border_users_b" height="20">
				Status:
			</td>
			<td class="border_users_l border_users_r border_users_b">
				<div align="" >	
					{if $userRequest->status eq 'new'}
					<select name="selectStatus">
						<option {if $userRequest->status eq 'new'} selected {/if}>new</option>
						<option {if $userRequest->status eq 'accept'} selected {/if}>accept</option>
						<option {if $userRequest->status eq 'deny'} selected {/if}>deny</option>
					</select>
					{else}
						&nbsp;<b>{$userRequest->status}</b>
					{/if}	
				</div>
			</td>
		</tr>
		
		{if $userRequest->status eq 'new'}
		<tr>
			<td class="border_users_l border_users_b" height="20">
				Additional Mail Comments:
			</td>
			<td class="border_users_l border_users_r border_users_b">
				<input type="checkbox" id="addComments" onclick="showAddComment();"/>
				<input type="hidden" name="commentsCheckUncheck" id="commentsCheck" value=""/>
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
		{/if}
		
		<tr>
			<td colspan="2" align="right" class="border_users_l border_users_r">
				<br/>
				<div style="margin-right: 20px;">
					{if $userRequest->status eq 'new'}
					{if $error neq ''}
						<font color="red">{$error}</font>
					{/if}	
					<input type="button" class="button" value="Save" onclick="saveRequest();"/>
					<input type="hidden" name="actionSave" id="buttonSave" value=""/>
					<input type="button" class="button" value="Cancel" onclick="location.href='{$userRequest->back_url}'"/>
					{else}
					<input type="button" class="button" value="Ok" onclick="location.href='{$userRequest->back_url}'"/>	
					{/if}
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
	<input type="hidden" name="actionType" value="{$userRequest->action_type}"/>
</form>
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
		if (check == true){
			$('#addCommentRow').hide();
		} else {
			$('#addCommentRow').show();
		}
	}

	function saveRequest(){
		if (document.getElementById('addComments').checked == true) {
			document.getElementById('commentsCheck').value = 'ON';
		} else {
			document.getElementById('commentsCheck').value = 'OFF';
		}	
		document.getElementById('buttonSave').value = 'Save';
		document.getElementById('saveForm').submit();
	}
	{/literal}	
</script>

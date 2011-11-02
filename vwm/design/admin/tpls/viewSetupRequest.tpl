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
<form id="saveForm" action="admin.php?action=viewDetails&category=setupRequest&id={$setupRequest->id}" enctype="multipart/form-data" method="post">	
	<table class="users"  align="center" cellpadding="0" cellspacing="0">
		<tr class="users_top_yellowgreen" >
			<td class="users_u_top_yellowgreen" width="27%" height="30" >
				<span>New {$setupRequest->category|capitalize} Request. View details</span>
			</td>
			<td class="users_u_top_r_yellowgreen" width="300">
			</td>
		</tr>
		
		<tr>
			<td class="border_users_l border_users_b" height="20">
				{$setupRequest->category|capitalize} Name:
			</td>
			<td class="border_users_l border_users_r border_users_b">
				<div align="left" >&nbsp;{$setupRequest->name}</div>
			</td>
		</tr>
		
		{if $setupRequest->category eq 'facility'}
		<tr>
			<td class="border_users_l border_users_b" height="20">
				Company:
			</td>
			<td class="border_users_l border_users_r border_users_b">
				<div align="left" >&nbsp;{$setupRequest->parent_name}</div>
			</td>
		</tr>	
		{/if}	
		
		{if $setupRequest->category eq 'department'}
		<tr>
			<td class="border_users_l border_users_b" height="20">
				Facility:
			</td>
			<td class="border_users_l border_users_r border_users_b">
				<div align="left" >&nbsp;{$setupRequest->parent_name}</div>
			</td>
		</tr>	
		<tr>
			<td class="border_users_l border_users_b" height="20">
				Company:
			</td>
			<td class="border_users_l border_users_r border_users_b">
				<div align="left" >&nbsp;{$setupRequest->company_name}</div>
			</td>
		</tr>	
		{/if}
		
		{if $setupRequest->category eq 'facility'}
		<tr>
			<td class="border_users_l border_users_b" height="20">
				EPA/ID Number:
			</td>
			<td class="border_users_l border_users_r border_users_b">
				<div align="left" >&nbsp;{$setupRequest->epa}</div>
			</td>
		</tr>	
		{/if}
		
		{if $setupRequest->category eq 'facility' || $setupRequest->category eq 'department'}
		<tr>
			<td class="border_users_l border_users_b" height="20">
				VOC Monthly Limit:
			</td>
			<td class="border_users_l border_users_r border_users_b">
				<div align="left" >&nbsp;{$setupRequest->voc_monthly_limit}</div>
			</td>
		</tr>	
		<tr>
			<td class="border_users_l border_users_b" height="20">
				VOC Annual Limit:
			</td>
			<td class="border_users_l border_users_r border_users_b">
				<div align="left" >&nbsp;{$setupRequest->voc_annual_limit}</div>
			</td>
		</tr>	
		{/if}
		
		{if $setupRequest->category eq 'company' || $setupRequest->category eq 'facility'}
		<tr>
			<td class="border_users_l border_users_b" height="20">
				Country:
			</td>
			<td class="border_users_l border_users_r border_users_b">
				<div align="left" >	&nbsp;{$setupRequest->country_name}</div>
			</td>
		</tr>
		
		<tr>
			<td class="border_users_l border_users_b" height="20">
				State:
			</td>
			<td class="border_users_l border_users_r border_users_b">
				<div align="left" >	&nbsp;{$setupRequest->state}</div>
			</td>
		</tr>
		
		<tr>
			<td class="border_users_l border_users_b" height="20">
				City:
			</td>
			<td class="border_users_l border_users_r border_users_b">
				<div align="left" >	&nbsp;{$setupRequest->city}</div>
			</td>
		</tr>
		
		<tr>
			<td class="border_users_l border_users_b" height="20">
				Address:
			</td>
			<td class="border_users_l border_users_r border_users_b">
				<div align="left" >	&nbsp;{$setupRequest->address}</div>
			</td>
		</tr>
		
		<tr>
			<td class="border_users_l border_users_b" height="20">
				Email:
			</td>
			<td class="border_users_l border_users_r border_users_b">
				<div align="left" >	&nbsp;{$setupRequest->email}</div>
			</td>
		</tr>
		
		<tr>
			<td class="border_users_l border_users_b" height="20">
				Phone:
			</td>
			<td class="border_users_l border_users_r border_users_b">
				<div align="left" >	&nbsp;{$setupRequest->phone}</div>
			</td>
		</tr>
		
		<tr>
			<td class="border_users_l border_users_b" height="20">
				Fax:
			</td>
			<td class="border_users_l border_users_r border_users_b">
				<div align="left" >	&nbsp;{$setupRequest->fax}</div>
			</td>
		</tr>
		
		<tr>
			<td class="border_users_l border_users_b" height="20">
				Zip/Postal Code:
			</td>
			<td class="border_users_l border_users_r border_users_b">
				<div align="left" >	&nbsp;{$setupRequest->zip_code}</div>
			</td>
		</tr>
		
		<tr>
			<td class="border_users_l border_users_b" height="20">
				Contact:
			</td>
			<td class="border_users_l border_users_r border_users_b">
				<div align="left" >	&nbsp;{$setupRequest->contact}</div>
			</td>
		</tr>
		
		<tr>
			<td class="border_users_l border_users_b" height="20">
				Title:
			</td>
			<td class="border_users_l border_users_r border_users_b">
				<div align="left" >	&nbsp;{$setupRequest->title}</div>
			</td>
		</tr>
		{/if}		
		
		{if $setupRequest->category eq 'department'}
		<tr>
			<td class="border_users_l border_users_b" height="20">
				Email:
			</td>
			<td class="border_users_l border_users_r border_users_b">
				<div align="left" >	&nbsp;{$setupRequest->email}</div>
			</td>
		</tr>	
		{/if}	
		
		<tr>
			<td class="border_users_l border_users_b" height="20">
				Request Date:
			</td>
			<td class="border_users_l border_users_r border_users_b">
				<div align="left" >	&nbsp;{$setupRequest->date}</div>
			</td>
		</tr>
		
		{if $setupRequest->category eq 'facility' || $setupRequest->category eq 'department'}
		<tr>
			<td class="border_users_l border_users_b" height="20">
				Creater Name:
			</td>
			<td class="border_users_l border_users_r border_users_b">
				<div align="left" >&nbsp;{$setupRequest->creater_name}</div>
			</td>
		</tr>
		{/if}
		
		<tr>
			<td class="border_users_l border_users_b" height="20">
				Status:
			</td>
			<td class="border_users_l border_users_r border_users_b">
				<div align="" >	
					{if $setupRequest->status eq 'new'}
					<select name="selectStatus">
						<option {if $setupRequest->status eq 'new'} selected {/if}>new</option>
						<option {if $setupRequest->status eq 'accept'} selected {/if}>accept</option>
						<option {if $setupRequest->status eq 'deny'} selected {/if}>deny</option>
					</select>
					{else}
						&nbsp;<b>{$setupRequest->status}</b>
					{/if}	
				</div>
			</td>
		</tr>
		
		{if $setupRequest->status eq 'new'}
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
					{if $setupRequest->status eq 'new'}
						{if $error neq ''}
							<font color="red">{$error}</font>
						{/if}
						<input type="button" class="button" value="Save" onclick="saveRequest();"/>
						<input type="hidden" name="actionSave" id="buttonSave" value=""/>
						<input type="button" class="button" value="Cancel" onclick="location.href='{$setupRequest->back_url}'"/>
					{else}
						<input type="button" class="button" value="Ok" onclick="location.href='{$setupRequest->back_url}'"/>
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
	<input type="hidden" name="category" value="{$setupRequest->category}"/>
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

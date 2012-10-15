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
	<form method="post">
		<table class="users"  align="center" cellpadding="0" cellspacing="0">
			<tr class="users_top_yellowgreen" >
				<td class="users_u_top_yellowgreen" width="27%" height="30" >
					<span>New Product Request. View details</span>
				</td>
				<td class="users_u_top_r_yellowgreen" width="300">
				</td>
			</tr>

			<tr>
				<td class="border_users_l border_users_b" height="20">
					Product ID/Number:
				</td>
				<td class="border_users_l border_users_r border_users_b">
					<div align="left" >&nbsp;{$productRequest->product_id|escape}</div>
				</td>
			</tr>

			<tr>
				<td class="border_users_l border_users_b" height="20">
					Name:
				</td>
				<td class="border_users_l border_users_r border_users_b">
					<div align="left" >&nbsp;{$productRequest->name|escape}</div>
				</td>
			</tr>	

			<tr>
				<td class="border_users_l border_users_b" height="20">
					Supplier:
				</td>
				<td class="border_users_l border_users_r border_users_b">
					<div align="left" >&nbsp;{$productRequest->supplier|escape}</div>
				</td>
			</tr>

			<tr>
				<td class="border_users_l border_users_b" height="20">
					Description:
				</td>
				<td class="border_users_l border_users_r border_users_b">
					<div align="left" >&nbsp;{$productRequest->description|escape}</div>
				</td>
			</tr>		

			<tr>
				<td class="border_users_l border_users_b" height="20">
					MSDS File:
				</td>
				<td class="border_users_l border_users_r border_users_b">
					{assign var=msds value=$productRequest->getMsds()}
					<div align="left" >&nbsp;{if $msds->name}<a href="{$msds->name|escape}">view</a>{else}&mdash;{/if}</div>
				</td>
			</tr>	

			<tr>
				<td class="border_users_l border_users_b" height="20">
					Request Date:
				</td>
				<td class="border_users_l border_users_r border_users_b">
					{assign var=requestDate value=$productRequest->date}
					<div align="left" >	&nbsp;{$requestDate->format($smarty.const.DEFAULT_DATE_FORMAT)|escape}</div>
				</td>
			</tr>

			<tr>
				<td class="border_users_l border_users_b" height="20">
					Creator Name:
				</td>
				<td class="border_users_l border_users_r border_users_b">
					{assign var=requestAuthor value=$productRequest->getUser()}
					<div align="left" >&nbsp;{$requestAuthor->username|escape}</div>
				</td>
			</tr>

			<tr>
				<td class="border_users_l border_users_b" height="20">
					Status:
				</td>
				<td class="border_users_l border_users_r border_users_b">
					<div align="" >	
						<select name="status">
							{assign var="statusOptions" value=$productRequest->getStatusOptions()}
							{foreach from=$statusOptions item="statusOptionName" key="key"}
								<option value="{$key}" {if $productRequest->status eq $key} selected {/if}>{$statusOptionName}</option>						
							{/foreach}
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
						<input type="submit" class="button" value="Save"/>
						<input type="button" class="button" value="Cancel" onclick="location.href='{$productRequest->back_url}'"/>
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
			console.log(check);
		if (check == true){
			$('#addCommentRow').hide();
		} else {
			$('#addCommentRow').show();
		}	
	}
		{/literal}	
	</script>

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
				<span >{if $currentOperation eq "addItem"}Adding for a new Industry Sub-Category{else}Editing Industry Sub-Category{/if}</span>
			</td>
			<td class="users_u_top_r" width="300">
				&nbsp;
			</td>				
		</tr>
		<form method='POST' action='admin.php?action={$request.action}&category=industrySubType{if $request.action neq "addItem"}&id={$request.id}{/if}'>
		<tr height="10px">
			<td class="border_users_l border_users_b" height="20">
				Industry Sub-Category:
			</td>
			<td class="border_users_l border_users_b border_users_r">
				<div align="left"><input type='text' name='industrySubType_desc' value='{$data.industrySubType_desc}' size="40%"/></div>
							
					{if $validStatus.summary eq 'false'}
						{if $validStatus.industrySubType_desc eq 'alredyExist'}
							<div style="width:220px;margin:2px 0px 0px 5px;" align="left"><img src='design/user/img/alert1.gif' height=16  style="float:left;">
								<font style="float:left;vertical-align:bottom;color:red;margin:1px 0px 0px 5px;">Entered name is alredy in use!</font>
							</div>
						{/if}
					{/if}								
			</td>
		</tr>
		<tr height="10px">
			<td class="border_users_l border_users_b" height="20">
				Industry Type:
			</td>
			<td class="border_users_l border_users_b border_users_r">
				<div align="left">
					<select name='industrySubType_parent'>
						{foreach from=$data.industrySubType_parent item=parent}
							<option value="{$parent.id}" {if $parent.id eq $data.industrySubType_parentID} selected {/if}>{$parent.type}</option>
						{/foreach}	
					</select>
				</div>
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
	<div align="right">
		<br>
		<input type='submit' name='save' class="button" value='Save'/>
		<input type='button' name='cancel' class="button" value='Cancel' 
			{if $request.action=='edit'} onclick='location.href="admin.php?action=viewDetails&category=industrySubType&id={$request.id}"'{/if}
			{if $request.action=='addItem'} onclick='location.href="admin.php?action=browseCategory&category=tables&bookmark=industrySubType"'{/if}/>
		<span style="padding-right:50">&nbsp;</span>
		</div>
		{*  <input type='hidden' name='itemID' value='apmethod'>
		<input type='hidden' name='categoryID' value='class'>
		<input type='hidden' name='action' value={$currentOperation}>
		
		{if $currentOperation eq "updateItem"}
			<input type="hidden" name="id" value="{$ID}">
		{/if}*}
		</form>
</div>	

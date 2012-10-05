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
				<span >{if $currentOperation eq "addItem"}Adding for a new Industry Type{else}Editing Industry Type{/if}</span>
			</td>
			<td class="users_u_top_r" width="300">
				&nbsp;
			</td>				
		</tr>
		<form method='POST' action='admin.php?action={$request.action}&category=industryType{if $request.action neq "addItem"}&id={$request.id}{/if}'>
		<tr height="10px">
			<td class="border_users_l border_users_b" height="20">
				Industry Type:
			</td>
			<td class="border_users_l border_users_b border_users_r">
				<div align="left"><input type='text' name='industryType_desc' value='{$data->type}'/></div>
							
					{foreach from=$violationList item="violation"}
                        {if $violation->getPropertyPath() eq 'type' || $violation->getPropertyPath() eq 'uniqueName'}							
                        {*ERROR*}					
                        <div class="error_img" style="float: left;"><span class="error_text">{$violation->getMessage()}</span></div>
                        {*/ERROR*}						    
                        {/if}
                    {/foreach}								
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
			{if $request.action=='edit'} onclick='location.href="admin.php?action=viewDetails&category=industryType&id={$request.id}"'{/if}
			{if $request.action=='addItem'} onclick='location.href="admin.php?action=browseCategory&category=tables&bookmark=industryType"'{/if}/>
		<span style="padding-right:50">&nbsp;</span>
		</div>
		</form>
</div>	
	
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
				<td class="users_u_top" height="30" width="20%">
					<span >{if $request.action eq "addItem"}Adding for a new Agency{else}Editing Agency{/if}</span>
				</td>
				<td class="users_u_top_r">
				</td>				
			</tr>
<form method='POST' action='admin.php?action={$request.action}&category=agency{if $request.action neq "addItem"}&id={$request.id}{/if}'>
			<tr height="10px">
				<td class="border_users_l border_users_b">
						Agency name US:
				</td>
				<td class="border_users_l border_users_b border_users_r">
					<div align="left" >	<input type='text' name='name_us' value='{$data.name_us}'></div>
								
					{if $validStatus.summary eq 'false'}
					{if $validStatus.name_us eq 'failed'}
				     	{*ERROR*}					
						<div style="width:80px;margin:2px 0px 0px 5px;" align="left"><img src='design/user/img/alert1.gif' height=16  style="float:left;">
						<font style="float:left;vertical-align:bottom;color:red;margin:1px 0px 0px 5px;">Error!</font></div>
						{*/ERROR*}
					{elseif $validStatus.name_us eq 'alredyExist'}
					<div style="width:220px;margin:2px 0px 0px 5px;" align="left"><img src='design/user/img/alert1.gif' height=16  style="float:left;">
						<font style="float:left;vertical-align:bottom;color:red;margin:1px 0px 0px 5px;">Entered name is alredy in use!</font>
					</div>
					{/if}
					{/if}
				</td>				
			</tr><tr height="10px">
				<td class="border_users_l border_users_b">
						Agency name EU:
				</td>
				<td class="border_users_l border_users_b border_users_r">
					<div align="left" >	<input type='text' name='name_eu' value='{$data.name_eu}'></div>
								
					{if $validStatus.summary eq 'false'}
					{if $validStatus.name_eu eq 'failed'}
				     	{*ERROR*}					
						<div style="width:80px;margin:2px 0px 0px 5px;" align="left"><img src='design/user/img/alert1.gif' height=16  style="float:left;">
						<font style="float:left;vertical-align:bottom;color:red;margin:1px 0px 0px 5px;">Error!</font></div>
						{*/ERROR*}
					{elseif $validStatus.name_eu eq 'alredyExist'}
					<div style="width:220px;margin:2px 0px 0px 5px;" align="left"><img src='design/user/img/alert1.gif' height=16  style="float:left;">
						<font style="float:left;vertical-align:bottom;color:red;margin:1px 0px 0px 5px;">Entered name is alredy in use!</font>
					</div>
					{/if}
					{/if}
				</td>				
			</tr><tr height="10px">
				<td class="border_users_l border_users_b">
						Agency name China:
				</td>
				<td class="border_users_l border_users_b border_users_r">
					<div align="left" >	<input type='text' name='name_cn' value='{$data.name_cn}'></div>
								
					{if $validStatus.summary eq 'false'}
					{if $validStatus.name_cn eq 'failed'}
				     	{*ERROR*}					
						<div style="width:80px;margin:2px 0px 0px 5px;" align="left"><img src='design/user/img/alert1.gif' height=16  style="float:left;">
						<font style="float:left;vertical-align:bottom;color:red;margin:1px 0px 0px 5px;">Error!</font></div>
						{*/ERROR*}
					{elseif $validStatus.name_cn eq 'alredyExist'}
					<div style="width:220px;margin:2px 0px 0px 5px;" align="left"><img src='design/user/img/alert1.gif' height=16  style="float:left;">
						<font style="float:left;vertical-align:bottom;color:red;margin:1px 0px 0px 5px;">Entered name is alredy in use!</font>
					</div>
					{/if}
					{/if}
				</td>				
			</tr>
			<tr height="10px">
				<td class="border_users_l border_users_b">
						Description:
				</td>
				<td class="border_users_l border_users_b border_users_r">
					<div align="left" >	<input type='text' name='description' value='{$data.description}'/></div>			
				</td>				
			</tr>
			<tr height="10px">
				<td class="border_users_l border_users_b">
						Country:
				</td>
				<td class="border_users_l border_users_b border_users_r">
					<div align="left" >
						<select name="country" id="selectCountry">
                            {section name=i loop=$country}
                            	<option value='{$country[i].id}'> {$country[i].name}  </option>
                            {/section}
                        </select>
					</div>		
						
				</td>				
			</tr>
			<tr height="10px">
				<td class="border_users_l border_users_b">
						Location:
				</td>
				<td class="border_users_l border_users_b border_users_r">
					<div align="left" >	<input type='text' name='location' value='{$data.location}'/></div>			
				</td>				
			</tr>
			<tr height="10px">
				<td class="border_users_l border_users_b">
						Contact information:
				</td>
				<td class="border_users_l border_users_b border_users_r">
					<div align="left" >	<input type='text' name='contact_info' value='{$data.contact_info}'/></div>			
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
		<input type='submit' name='save' class="button" value='Save'>
		<input type='button' class="button" id='cancelButton' value='Cancel'>
		<span style="padding-right:50">&nbsp;</span>
		</div>
		
		</form>
</div>

{*Я знаю это тупо, но onclick='location.href="admin.php?action=browseCategory&categoryID=class&itemID=agency"' не работает. Я выпал в осадок *}
{literal}
	<script type='text/javascript'>
		$(function(){					
			$('#cancelButton').click(function()
			{
				{/literal}
					{if $request.action=='edit'} location.href="admin.php?action=viewDetails&category=agency&id={$request.id}"{/if}
					{if $request.action=='addItem'} location.href="admin.php?action=browseCategory&category=tables&bookmark=agency"{/if}					
				{literal}
			});
						
			if ({/literal}'{$data.country_id}'{literal}!='')
			{
				$('#selectCountry option[value ={/literal}{$data.country_id}{literal}]').attr('selected',true);
			}
			else
			{
				$('#selectCountry option[value =215]').attr('selected',true);
			}
			
		});	
	</script>
{/literal}
	

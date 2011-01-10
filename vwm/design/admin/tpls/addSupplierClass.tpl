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
					<span >{if $currentOperation eq "addItem"}Adding for a new supplier{else}Editing supplier{/if}</span>
				</td>
				<td class="users_u_top_r" >
					&nbsp;
				</td>					
			</tr>
<form method='POST' action='admin.php?action={$currentOperation}&categoryID=class&itemID=supplier{if $currentOperation neq "addItem"}&id={$ID}{/if}'>
			<tr height="10px">
		
							<td class="border_users_l border_users_b" height="20">
								Supplier Description:
							</td>
							<td class="border_users_l border_users_b border_users_r">
							<div align="left" >	<input type='text' name='supplier_desc' value='{$data.supplier_desc}{$data.description}'></div>
							
							{if $validStatus.summary eq 'false'}
							{if $validStatus.description eq 'failed'}
			     				{*ERROR*}					
								<div style="width:80px;margin:2px 0px 0px 5px;" align="left"><img src='design/user/img/alert1.gif' height=16  style="float:left;">
								<font style="float:left;vertical-align:bottom;color:red;margin:1px 0px 0px 5px;">Error!</font></div>
							    {*/ERROR*}
						    {elseif $validStatus.description eq 'alredyExist'}
								<div style="width:220px;margin:2px 0px 0px 5px;" align="left"><img src='design/user/img/alert1.gif' height=16  style="float:left;">
							    <font style="float:left;vertical-align:bottom;color:red;margin:1px 0px 0px 5px;">Entered name is alredy in use!</font></div>
							{/if}
						    {/if}
								
							</td>
					
						</tr>
						
						<tr height="10px">
		
							<td class="border_users_l border_users_b" height="20">
								Contact Person:
							</td>
							<td class="border_users_l border_users_b border_users_r">
								<div align="left" >	<input type='text' name='contact' value='{$data.contact}'></div>
								
							{if $validStatus.summary eq 'false'}
							{if $validStatus.contact eq 'failed'}
			     				{*ERROR*}					
								<div style="width:80px;margin:2px 0px 0px 5px;" align="left"><img src='design/user/img/alert1.gif' height=16  style="float:left;">
								<font style="float:left;vertical-align:bottom;color:red;margin:1px 0px 0px 5px;">Error!</font></div>
							    {*/ERROR*}						    
							{/if}
						    {/if}													
							</td>
					
						</tr>
						<tr height="10px">
		
							<td class="border_users_l border_users_b" height="20">
								Phone:
							</td>
							<td class="border_users_l border_users_b border_users_r">
								<div align="left" >	<input type='text' name='phone' value='{$data.phone}'></div>
							
							{if $validStatus.summary eq 'false'}
							{if $validStatus.phone eq 'failed'}
			     				{*ERROR*}					
								<div style="width:80px;margin:2px 0px 0px 5px;" align="left"><img src='design/user/img/alert1.gif' height=16  style="float:left;">
								<font style="float:left;vertical-align:bottom;color:red;margin:1px 0px 0px 5px;">Error!</font></div>
							    {*/ERROR*}						    
							{/if}
						    {/if}																
							</td>
					
						</tr>
						
						<tr height="10px">
		
							<td class="border_users_l border_users_b" height="20">
								Address:
							</td>
							<td class="border_users_l border_users_b border_users_r">
								<div align="left" >	<input type='text' name='address' value='{$data.address}'></div>
			
							{if $validStatus.summary eq 'false'}
							{if $validStatus.address eq 'failed'}
			     				{*ERROR*}					
								<div style="width:80px;margin:2px 0px 0px 5px;" align="left"><img src='design/user/img/alert1.gif' height=16  style="float:left;">
								<font style="float:left;vertical-align:bottom;color:red;margin:1px 0px 0px 5px;">Error!</font></div>
							    {*/ERROR*}						    
							{/if}
						    {/if}																
							</td>
					
						</tr>
						
						<tr height="10px">
		
							<td class="border_users_l border_users_b" height="20">
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
					{if $currentOperation=='edit'} onclick='location.href="admin.php?action=viewDetails&categoryID=class&itemID=supplier&id={$ID}"'{/if}
					{if $currentOperation=='addItem'} onclick='location.href="admin.php?action=browseCategory&categoryID=class&itemID=supplier"'{/if}>
				<span style="padding-right:50">&nbsp;</span>
			</div>		
		</form>
</div>	

{literal}
	<script type='text/javascript'>
		$(function(){			
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
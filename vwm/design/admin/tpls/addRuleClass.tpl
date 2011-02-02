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
					<span >{if $request.action eq "addItem"}Adding for a new rule{else}Editing rule{/if}</span>
				</td>
				<td class="users_u_top_r" >
					&nbsp;
				</td>			
			</tr>
<form method='POST' action='admin.php?action={$request.action}&category=rule{if $request.action neq "addItem"}&id={$request.id}{/if}'>
						<tr height="10px">
							
							<td class="border_users_l border_users_b" height="20">
								Rule NR US:
							</td>
							<td class="border_users_l border_users_b border_users_r">
							<div align="left" >	<input type='text' name='rule_nr_us' value='{$data.rule_nr_us}'></div>
							
							{if $validStatus.summary eq 'false'}
							{if $validStatus.rule_nr_us eq 'failed'}
			     				{*ERROR*}					
								<div style="width:80px;margin:2px 0px 0px 5px;" align="left"><img src='design/user/img/alert1.gif' height=16  style="float:left;">
								<font style="float:left;vertical-align:bottom;color:red;margin:1px 0px 0px 5px;">Error!</font></div>
							    {*/ERROR*}
						    {elseif $validStatus.rule_nr_us eq 'alredyExist'}
								<div style="width:220px;margin:2px 0px 0px 5px;" align="left"><img src='design/user/img/alert1.gif' height=16  style="float:left;">
							    <font style="float:left;vertical-align:bottom;color:red;margin:1px 0px 0px 5px;">Entered name is alredy in use!</font></div>
							{/if}
						    {/if}
								
							</td>
						</tr>
						
						<tr height="10px">
							
							<td class="border_users_l border_users_b" height="20">
								Rule NR EU:
							</td>
							<td class="border_users_l border_users_b border_users_r">
							<div align="left" >	<input type='text' name='rule_nr_eu' value='{$data.rule_nr_eu}'></div>
							
							{if $validStatus.summary eq 'false'}
							{if $validStatus.rule_nr_eu eq 'failed'}
			     				{*ERROR*}					
								<div style="width:80px;margin:2px 0px 0px 5px;" align="left"><img src='design/user/img/alert1.gif' height=16  style="float:left;">
								<font style="float:left;vertical-align:bottom;color:red;margin:1px 0px 0px 5px;">Error!</font></div>
							    {*/ERROR*}
						    {elseif $validStatus.rule_nr_eu eq 'alredyExist'}
								<div style="width:220px;margin:2px 0px 0px 5px;" align="left"><img src='design/user/img/alert1.gif' height=16  style="float:left;">
							    <font style="float:left;vertical-align:bottom;color:red;margin:1px 0px 0px 5px;">Entered name is alredy in use!</font></div>
							{/if}
						    {/if}
								
							</td>
						</tr>
						<tr height="10px">
							
							<td class="border_users_l border_users_b" height="20">
								Rule NR China:
							</td>
							<td class="border_users_l border_users_b border_users_r">
							<div align="left" >	<input type='text' name='rule_nr_cn' value='{$data.rule_nr_cn}'></div>
							
							{if $validStatus.summary eq 'false'}
							{if $validStatus.rule_nr_cn eq 'failed'}
			     				{*ERROR*}					
								<div style="width:80px;margin:2px 0px 0px 5px;" align="left"><img src='design/user/img/alert1.gif' height=16  style="float:left;">
								<font style="float:left;vertical-align:bottom;color:red;margin:1px 0px 0px 5px;">Error!</font></div>
							    {*/ERROR*}
						    {elseif $validStatus.rule_nr_cn eq 'alredyExist'}
								<div style="width:220px;margin:2px 0px 0px 5px;" align="left"><img src='design/user/img/alert1.gif' height=16  style="float:left;">
							    <font style="float:left;vertical-align:bottom;color:red;margin:1px 0px 0px 5px;">Entered name is alredy in use!</font></div>
							{/if}
						    {/if}
								
							</td>
						</tr>
							
						<tr>			
							<td class="border_users_l border_users_b" height="20">
								Rule Description:
							</td>
							<td class="border_users_l border_users_b border_users_r">
							<div align="left" >	<input type='text' name='rule_desc' value='{$data.rule_desc}'></div>
							
							{if $validStatus.summary eq 'false'}
							{if $validStatus.description eq 'failed'}
			     				{*ERROR*}					
								<div style="width:80px;margin:2px 0px 0px 5px;" align="left"><img src='design/user/img/alert1.gif' height=16  style="float:left;">
								<font style="float:left;vertical-align:bottom;color:red;margin:1px 0px 0px 5px;">Error!</font></div>
							    {*/ERROR*}
							{/if}
						    {/if}
								
							</td>
					
						</tr>
						
						<tr>
							<td class="border_users_l border_users_b" height="20">
								Country:
							</td>
							<td class="border_users_l border_users_b border_users_r">
							<div align="left" >	
				<select name="country" id="selectCountry" onchange="getStateList(this)">
					{section name=i loop=$country}
						<option value='{$country[i].country_id}' {if $country[i].country_id eq $data.country}selected="selected"{/if}>{$country[i].name} </option>
					{/section}
				</select>
				</div>
							</td>
						</tr>
						
						<tr>
							<td class="border_users_l border_users_b" height="20">
								State:
							</td>
							<td class="border_users_l border_users_b border_users_r">
							<div align="left" >	
				
								<select name="selectState" id="selectState" {if $selectMode eq true} style="display: block" {else} style="display: none" {/if}>
									{section name=i loop=$state}
										<option value='{$state[i].state_id}' {if $state[i].state_id eq $data.state} selected="selected" {/if}> {$state[i].name} </option>
									{/section}
								</select>
								
								<input type='text' name='textState' id='textState' value='{if $selectMode ne true}{$data.state}{/if}' {if $selectMode eq true} style="display: none" {else} style="display: block" {/if}>
								</div>
								{if $validStatus.summary eq 'false'}
									{if $validStatus.state eq 'failed'}
								
										{*ERORR*}
											<div style="width:80px;margin:2px 0px 0px 5px;" align="left"><img src='design/user/img/alert1.gif' height=16  style="float:left;">
											<font style="float:left;vertical-align:bottom;color:red;margin:1px 0px 0px 5px;">Error!</font></div>
										{*/ERORR*}

									{/if}
								{/if}
				</div>
							</td>
						</tr>
						
						<tr>			
							<td class="border_users_l border_users_b" height="20">
								County:
							</td>
							<td class="border_users_l border_users_b border_users_r">
							<div align="left" >	<input type='text' name='county' value='{$data.county}'></div>
							
							{if $validStatus.summary eq 'false'}
							{if $validStatus.county eq 'failed'}
			     				{*ERROR*}					
								<div style="width:80px;margin:2px 0px 0px 5px;" align="left"><img src='design/user/img/alert1.gif' height=16  style="float:left;">
								<font style="float:left;vertical-align:bottom;color:red;margin:1px 0px 0px 5px;">Error!</font></div>
							    {*/ERROR*}
							{/if}
						    {/if}
								
							</td>
					
						</tr>
						
						
						<tr>			
							<td class="border_users_l border_users_b" height="20">
								City:
							</td>
							<td class="border_users_l border_users_b border_users_r">
							<div align="left" >	<input type='text' name='city' value='{$data.city}'></div>
							
							{if $validStatus.summary eq 'false'}
							{if $validStatus.city eq 'failed'}
			     				{*ERROR*}					
								<div style="width:80px;margin:2px 0px 0px 5px;" align="left"><img src='design/user/img/alert1.gif' height=16  style="float:left;">
								<font style="float:left;vertical-align:bottom;color:red;margin:1px 0px 0px 5px;">Error!</font></div>
							    {*/ERROR*}
							{/if}
						    {/if}
								
							</td>
					
						</tr>
						
						<tr>			
							<td class="border_users_l border_users_b" height="20">
								ZIP:
							</td>
							<td class="border_users_l border_users_b border_users_r">
							<div align="left" >	<input type='text' name='zip' value='{$data.zip}'></div>
							
							{if $validStatus.summary eq 'false'}
							{if $validStatus.zip eq 'failed'}
			     				{*ERROR*}					
								<div style="width:80px;margin:2px 0px 0px 5px;" align="left"><img src='design/user/img/alert1.gif' height=16  style="float:left;">
								<font style="float:left;vertical-align:bottom;color:red;margin:1px 0px 0px 5px;">Error!</font></div>
							    {*/ERROR*}
							{/if}
						    {/if}
								
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
			{if $request.action=='edit'} onclick='location.href="admin.php?action=viewDetails&category=rule&id={$request.id}"'{/if}
			{if $request.action=='addItem'} onclick='location.href="admin.php?action=browseCategory&category=tables&bookmark=rule"'{/if}>
		<span style="padding-right:50">&nbsp;</span>
		</div>		
		</form>
</div>	

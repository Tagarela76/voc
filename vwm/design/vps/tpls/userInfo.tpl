	<div class="padd_top20">
{*notifications*}
	{if $message }
		{include file="tpls:../user/tpls/notify/greenNotify.tpl" text=$message}		
	{/if}
{**}
	<form action="vps.php" method="GET">		
	<div class="padd7 ">
			    {*shadow*}
	<div class="shadow">
	<div class="shadow_top">
	<div class="shadow_bottom">
	 {**}
			<table  class="users_vps" align="center" cellpadding="0" cellspacing="0"  width=700px;>			
			<tr class="users_top_yellowgreen users_u_top_size" >
				<td class="users_u_top_yellowgreen" width="250px">
					My info 
				</td>
				<td class="users_u_top_r_yellowgreen"></td>
			</tr>
			<tr class="hov_company" >
				<td class="border_users_l border_users_r border_users_b">
					First Name:
				</td>
				<td class="border_users_r border_users_b">
					<input type="text" name="firstName" value="{$userData.firstName}">
				</td>
			</tr>
			<tr class="hov_company" >
				<td class="border_users_l border_users_r border_users_b">
					Last Name:
				</td>
				<td class="border_users_r border_users_b">
					<input type="text" name="lastName" value="{$userData.lastName}">
				</td>
			</tr>
			<tr class="hov_company">
				<td class="border_users_l border_users_r border_users_b">
					Currency:
				</td>
				<td class="border_users_r border_users_b">
					<select name='currency_id'>
						{if $userData.showAddUser}
							{foreach from=$currenciesList item=currency}						 
								<option value='{$currency.id}' {if $currency.id == $smarty.const.DEFAULT_CURRENCY} selected {/if} >{$currency.iso} &mdash; {$currency.description}</option>
							{/foreach}
						{else}
							{foreach from=$currenciesList item=currency}						 
								<option value='{$currency.id}' {if $currency.id == $userData.currency_id} selected {/if} >{$currency.iso} &mdash; {$currency.description}</option>
							{/foreach}
						{/if}
					</select>					
				</td>
			</tr>
			<tr class="hov_company">
				<td class="border_users_l border_users_r border_users_b">
					Secondary Contact:
				</td>
				<td class="border_users_r border_users_b">
					<input type="text" name="secondaryContact" value="{$userData.secondary_contact}">
				</td>
			</tr>
			<trclass="hov_company">
				<td class="border_users_l border_users_r border_users_b">
					Email:
				</td>
				<td class="border_users_r border_users_b">
					<input type="text" name="email" value="{$userData.email}">
				</td>
			</tr>
			<tr class="hov_company">
				<td class="border_users_l border_users_r border_users_b">
					Secondary Email:
				</td>
				<td class="border_users_r border_users_b">
					<input type="text" name="secondaryEmail" value="{$userData.secondary_email}">
				</td>
			</tr>
			<tr class="hov_company">
				<td class="border_users_l border_users_r border_users_b">
					Company:
				</td>
				<td class="border_users_r border_users_b">
					<input type="text" value="{$companyName}" disabled>
					<input type="hidden" name="companyID" value="{$userData.company_id}">
				</td>
			</tr>
			<tr class="hov_company">
				<td class="border_users_l border_users_r border_users_b">
					Address 1:
				</td>
				<td class="border_users_r border_users_b">
					<input type="text" name="address1" value="{$userData.address1}">
				</td>
			</tr>
			<tr class="hov_company">
				<td class="border_users_l border_users_r border_users_b">
					Address 2:
				</td>
				<td class="border_users_r border_users_b">
					<input type="text" name="address2" value="{$userData.address2}">
				</td>
			</tr>
			<tr class="hov_company">
				<td class="border_users_l border_users_r border_users_b">
					City:
				</td>
				<td class="border_users_r border_users_b">
					<input type="text" name="city" value="{$userData.city}">
				</td>
			</tr>
			<tr class="hov_company">
				<td class="border_users_l border_users_r border_users_b">
					State/Region:
				</td>
				<td class="border_users_r border_users_b">
					<select name="state">
						{section name=i loop=$states}
							<option value='{$states[i].id}' {if $states[i].id eq $userData.state_id} selected="selected" {/if}> {$states[i].name} </option>
						{/section}
					</select>
				</td>
			</tr>
			<tr class="hov_company">
				<td class="border_users_l border_users_r border_users_b">
					Zip:
				</td>
				<td class="border_users_r border_users_b">
					<input type="text" name="zip" value="{$userData.zip}">
				</td>
			</tr>
			<tr>
				<td class="border_users_l border_users_r border_users_b">
					Country:
				</td>
				<td class="border_users_r border_users_b">				
					<select name="country">
						{section name=i loop=$countries}
							<option value='{$countries[i].country_id}' {if $countries[i].country_id eq $userData.country_id}selected="selected"{/if}> {$countries[i].name} </option>
						{/section}
					</select>
				</td>
			</tr>
			<tr>
				<td class="border_users_l border_users_r border_users_b">
					Phone:
				</td>
				<td class="border_users_r border_users_b">
					<input type="text" name="phone" value="{$userData.phone}">
				</td>
			</tr>
			<tr>
				<td class="border_users_l border_users_r border_users_b">
					Fax:
				</td>
				<td class="border_users_r border_users_b">
					<input type="text" name="fax" value="{$userData.fax}">
				</td>
			 </tr>
				<tr>
					<td class="users_u_bottom">&nbsp;</td>
					<td class="users_u_bottom_r" style="padding:3px">&nbsp;</td>
					</tr>
						</table>
								{*shadow*}	
		</div>
        </div>
        </div>
		{**}																													
        <div align="center">
            <div align="right" style="width:650px ;">
                <input type="submit" value="Save" class="button">
            </div>
        </div>
		<input type="hidden" name="action" value="{$action}">
		<input type="hidden" name="category" value="{$category}">
		
		{if $userData.showAddUser} {* adding new user when he is registered at VOC*}
			<input type="hidden" name="step" value="first">			
			<input type="hidden" name="accessname" value="{$userData.accessname}">
			<input type="hidden" name="password" value="{$userData.password}">
			<input type="hidden" name="accessLevelID" value="{$userData.accesslevel_id}">
		{/if}</div>
	</form>
</div>
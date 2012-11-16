<script type="text/javascript">
	var usaID = {$usaID};
</script>
{if $error_message}
		{include file="tpls:tpls/notify/orangeNotify.tpl" text=$error_message}
{/if}
<div style="padding:7px;">
<form method='POST' action='admin.php?action={$request.action}&category=contacts{if $request.action neq "addItem"}&id={$request.id}{/if}&subBookmark={if $smarty.request.subBookmark}{$smarty.request.subBookmark}{else}contacts{/if}{if $request.page}&page={$request.page}{/if}{if $request.subBookmark}&subBookmark={$request.subBookmark}{/if}'>
		<table class="users rd" align="center" cellpadding="0" cellspacing="0">
			<tr class="users_u_top_size users_top">
				<td class="users_u_top" height="30" width="20%">
					<span >{if $request.action eq "addItem"}Adding for a new contact{else}Editing contact{/if}</span>
				</td>
				
				<td class="users_u_top_r">
				</td>				
			</tr>

			<tr style="height:10px;">
				<td class="border_users_l border_users_b">
						Company:
				</td>
				<td class="border_users_l border_users_b border_users_r">
					<div align="left" style='display:inline; float:left;'>	
						<input type='text' name='company' value='{$data->company|escape}'> 
					</div>
					
					{if $data->errors.company}
					
						<div style="margin:2px 0px 0px 5px;" align="left"><img src='design/user/img/alert1.gif' height=16  style="float:left;">
									<font style="float:left;vertical-align:bottom;color:red;margin:1px 0px 0px 5px;">{$data->errors.company}</font>
						</div>
					{/if}
				</td>	
							
			</tr>
			
			<tr height="10px">
				<td class="border_users_l border_users_b">
						Contact:
				</td>
				<td class="border_users_l border_users_b border_users_r">
					<div align="left" style='display:inline; float:left;'>
						<input type='text' name='contact' value='{$data->contact|escape}'>  <span style='color:Red;'>*</span>
					</div>
					
					{if $data->errors.contact}
					
						<div style="margin:2px 0px 0px 5px;" align="left"><img src='design/user/img/alert1.gif' height=16  style="float:left;">
									<font style="float:left;vertical-align:bottom;color:red;margin:1px 0px 0px 5px;">{$data->errors.contact}</font>
						</div>
					{/if}
				</td>				
			</tr>
						
			<tr height="10px">
				<td class="border_users_l border_users_b">
						Title:
				</td>
				<td class="border_users_l border_users_b border_users_r">
					<div align="left" style='display:inline; float:left;'>
						<input type='text' name='title' value='{$data->title|escape}'> 
					</div>
					{if $data->errors.title}
					
						<div style="margin:2px 0px 0px 5px;" align="left"><img src='design/user/img/alert1.gif' height=16  style="float:left;">
									<font style="float:left;vertical-align:bottom;color:red;margin:1px 0px 0px 5px;">{$data->errors.title}</font>
						</div>
					{/if}
				</td>				
			</tr>
			
			<tr height="10px">
				<td class="border_users_l border_users_b">
						Type:
				</td>
				<td class="border_users_l border_users_b border_users_r">
					<div align="left" style='display:inline; float:left;'>
					{foreach from=$typelist item=types}
						<input id='{$types.id}{$types.name}' type='checkbox' name='type[]' value='{$types.id}' {foreach from=$data->type item=type}{if $type.type_id == $types.id }checked{/if}{/foreach}><label for="{$types.id}{$types.name}">{$types.name}</label>&nbsp;
					{/foreach}
						
					{*foreach from=$data->type item=type}<input id='{$type.type_id}{$type.name}' type='checkbox' name='type' value='{$type.type_id}'><label for="{$type.type_id}{$type.name}">{$type.name}</label>&nbsp;{/foreach*}
						 
					</div>

				</td>				
			</tr>			
			
			<tr height="10px">
				<td class="border_users_l border_users_b">
						Phone:
				</td>
				<td class="border_users_l border_users_b border_users_r">
					<div align="left" style='display:inline; float:left;'>
						<input type='text' name='phone' value='{$data->phone}'>  
					</div>
					{if $data->errors.phone}
					
						<div style="margin:2px 0px 0px 5px;" align="left"><img src='design/user/img/alert1.gif' height=16  style="float:left;">
									<font style="float:left;vertical-align:bottom;color:red;margin:1px 0px 0px 5px;">{$data->errors.phone}</font>
						</div>
					{/if}
				</td>				
			</tr>
			
			<tr height="10px">
				<td class="border_users_l border_users_b">
						Cell/mobile phone:
				</td>
				<td class="border_users_l border_users_b border_users_r">
					<div align="left" style='display:inline; float:left;'>
						<input type='text' name='cellphone' value='{$data->cellphone}'>  
					</div>
					{if $data->errors.cellphone}
					
						<div style="margin:2px 0px 0px 5px;" align="left"><img src='design/user/img/alert1.gif' height=16  style="float:left;">
									<font style="float:left;vertical-align:bottom;color:red;margin:1px 0px 0px 5px;">{$data->errors.cellphone}</font>
						</div>
					{/if}
				</td>				
			</tr>
			
			<tr height="10px">
				<td class="border_users_l border_users_b">
						Fax:
				</td>
				<td class="border_users_l border_users_b border_users_r">
					<div align="left" style='display:inline; float:left;'>
						<input type='text' name='fax' value='{$data->fax}'>  
					</div>
					{if $data->errors.fax}
					
						<div style="margin:2px 0px 0px 5px;" align="left"><img src='design/user/img/alert1.gif' height=16  style="float:left;">
									<font style="float:left;vertical-align:bottom;color:red;margin:1px 0px 0px 5px;">{$data->errors.fax}</font>
						</div>
					{/if}
				</td>				
			</tr>
			
			<tr height="10px">
				<td class="border_users_l border_users_b">
						Email:
				</td>
				<td class="border_users_l border_users_b border_users_r">
					<div align="left" style='display:inline; float:left;'>
						<input type='text' name='email' value='{$data->email}'>  
					</div>
					{if $data->errors.email}
					
						<div style="margin:2px 0px 0px 5px;" align="left"><img src='design/user/img/alert1.gif' height=16  style="float:left;">
									<font style="float:left;vertical-align:bottom;color:red;margin:1px 0px 0px 5px;">{$data->errors.email}</font>
						</div>
					{/if}
				</td>				
			</tr>
			
			<tr height="10px">
				<td class="border_users_l border_users_b">
						Website:
				</td>
				<td class="border_users_l border_users_b border_users_r">
					<div align="left" style='display:inline; float:left;'>
						<input type='text' name='website' value='{$data->website}'>  
					</div>
					{if $data->errors.website}
					
						<div style="margin:2px 0px 0px 5px;" align="left"><img src='design/user/img/alert1.gif' height=16  style="float:left;">
									<font style="float:left;vertical-align:bottom;color:red;margin:1px 0px 0px 5px;">{$data->errors.website}</font>
						</div>
					{/if}
				</td>				
			</tr>			
			
			<tr height="10px">
				<td class="border_users_l border_users_b">
						Mailing address:
				</td>
				<td class="border_users_l border_users_b border_users_r">
					<div align="left" style='display:inline; float:left;'>
						<input type='text' name='mail' value='{$data->mail|escape}'>  
					</div>
					{if $data->errors.mail}
					
						<div style="margin:2px 0px 0px 5px;" align="left"><img src='design/user/img/alert1.gif' height=16  style="float:left;">
									<font style="float:left;vertical-align:bottom;color:red;margin:1px 0px 0px 5px;">{$data->errors.mail}</font>
						</div>
					{/if}
				</td>				
			</tr>

			
			<!--<tr height="10px">
				<td class="border_users_l border_users_b">
						Government Agencies:
				</td>
				<td class="border_users_l border_users_b border_users_r">
					<div align="left" style='display:inline; float:left;'>
						<input type='text' name='government_agencies' value='{$data->government_agencies}'>
					</div>
					
				</td>				
			</tr>-->
			
			<!--  <tr height="10px">
				<td class="border_users_l border_users_b">
						Affiliations:
				</td>
				<td class="border_users_l border_users_b border_users_r">
					<div align="left" style='display:inline; float:left;'>
						<input type='text' name='affiliations' value='{$data->affiliations}'>
					</div>
				</td>				
			</tr>-->
			
			<tr height="10px">
				<td class="border_users_l border_users_b">
						Industry:
				</td>
				<td class="border_users_l border_users_b border_users_r">
					<div align="left" style='display:inline; float:left;'>
						<input type='text' name='industry' value='{$data->industry|escape}'>
					</div>
					{if $data->errors.industry}
					
						<div style="margin:2px 0px 0px 5px;" align="left"><img src='design/user/img/alert1.gif' height=16  style="float:left;">
									<font style="float:left;vertical-align:bottom;color:red;margin:1px 0px 0px 5px;">{$data->errors.industry}</font>
						</div>
					{/if}
				</td>				
			</tr>
			
			<tr height="10px">
				<td class="border_users_l border_users_b">
						Country:
				</td>
				<td class="border_users_l border_users_b border_users_r">
					<div align="left" style='display:inline; float:left;'>
						<select name="country_id" id="country">
							{foreach from=$countries item=country}
								<option value="{$country.id}" {if $country.id == $data->country_id}selected="selected"{/if}>{$country.name}</option>
							{/foreach}
						</select>	
						  
						  
					</div>
					{if $data->errors.country}
					
						<div style="margin:2px 0px 0px 5px;" align="left"><img src='design/user/img/alert1.gif' height=16  style="float:left;">
									<font style="float:left;vertical-align:bottom;color:red;margin:1px 0px 0px 5px;">{$data->errors.country}</font>
						</div>
					{/if}
				</td>				
			</tr>
			
			<tr height="10px">
				<td class="border_users_l border_users_b">
						State:
				</td>
				<td class="border_users_l border_users_b border_users_r">
					<div align="left" style='display:inline; float:left;'>
					
						{if $data->country_id == $usaID}
							{assign var='isUsa' value=true}
						{else}
							{assign var='isUsa' value=false}
						{/if}
						
						<select name="selState"  onchange="get_state_name();"  id="selState" {if !$isUsa}style="display:none;"{/if}>
							{foreach from=$states item=state}
								<option value="{$state.id}" {if $state.id == $data->state_id}selected{/if} >{$state.name}</option>
							{/foreach}
						</select>
						{literal}<script>function get_state_name(){$('#state_name_5').attr('value', $('#selState :selected').html());}</script>{/literal}
						<input type='text' name='txState' id="txState" value='{$data->state}' {if $isUsa}style="display:none;"{/if}>
						<input type="hidden" name="state_name_5" id="state_name_5"  />  
						<input type="hidden" name="state_select_type" id="state_select_type" {if $isUsa}value="select"{else}value="text"{/if} />
						
					</div>
					{if $data->errors.state and !$isUsa}
					
						<div style="margin:2px 0px 0px 5px;" align="left"><img src='design/user/img/alert1.gif' height=16  style="float:left;">
									<font style="float:left;vertical-align:bottom;color:red;margin:1px 0px 0px 5px;">{$data->errors.state}</font>
						</div>
					{/if}
				</td>				
			</tr>
			
			<tr height="10px">
				<td class="border_users_l border_users_b">
						City:
				</td>
				<td class="border_users_l border_users_b border_users_r">
					<div align="left" style='display:inline; float:left;'>
						<input type='text' name='city' value='{$data->city}'>
					</div>
					
					{if $data->errors.city}
					
						<div style="margin:2px 0px 0px 5px;" align="left"><img src='design/user/img/alert1.gif' height=16  style="float:left;">
									<font style="float:left;vertical-align:bottom;color:red;margin:1px 0px 0px 5px;">{$data->errors.city}</font>
						</div>
					{/if}
				</td>				
			</tr>			
			
			<tr height="10px">
				<td class="border_users_l border_users_b">
						Zip Code:
				</td>
				<td class="border_users_l border_users_b border_users_r">
					<div align="left" style='display:inline; float:left;'>
						<input type='text' name='zip_code' value='{$data->zip_code}'>
					</div>
					
					{if $data->errors.zip_code}
					
						<div style="margin:2px 0px 0px 5px;" align="left"><img src='design/user/img/alert1.gif' height=16  style="float:left;">
									<font style="float:left;vertical-align:bottom;color:red;margin:1px 0px 0px 5px;">{$data->errors.zip_code}</font>
						</div>
					{/if}
				</td>				
			</tr>
			
			<tr height="10px">
				<td class="border_users_l border_users_b">
						Account number:
				</td>
				<td class="border_users_l border_users_b border_users_r">
					<div align="left" style='display:inline; float:left;'>
						<input type='text' name='acc_number' value='{$data->acc_number|escape}'>
					</div>
					
					{if $data->errors.acc_number}
					
						<div style="margin:2px 0px 0px 5px;" align="left"><img src='design/user/img/alert1.gif' height=16  style="float:left;">
									<font style="float:left;vertical-align:bottom;color:red;margin:1px 0px 0px 5px;">{$data->errors.acc_number}</font>
						</div>
					{/if}
				</td>				
			</tr>
			
			<tr height="10px">
				<td class="border_users_l border_users_b">
						Paint Supplier:
				</td>
				<td class="border_users_l border_users_b border_users_r">
					<div align="left" style='display:inline; float:left;'>
						<input type='text' name='paint_supplier' value='{$data->paint_supplier|escape}'>
					</div>
					
					{if $data->errors.paint_supplier}
					
						<div style="margin:2px 0px 0px 5px;" align="left"><img src='design/user/img/alert1.gif' height=16  style="float:left;">
									<font style="float:left;vertical-align:bottom;color:red;margin:1px 0px 0px 5px;">{$data->errors.paint_supplier}</font>
						</div>
					{/if}
				</td>				
			</tr>
			
			<tr height="10px">
				<td class="border_users_l border_users_b">
						Paint System:
				</td>
				<td class="border_users_l border_users_b border_users_r">
					<div align="left" style='display:inline; float:left;'>
						<input type='text' name='paint_system' value='{$data->paint_system|escape}'>
					</div>
					
					{if $data->errors.paint_system}
					
						<div style="margin:2px 0px 0px 5px;" align="left"><img src='design/user/img/alert1.gif' height=16  style="float:left;">
									<font style="float:left;vertical-align:bottom;color:red;margin:1px 0px 0px 5px;">{$data->errors.paint_system}</font>
						</div>
					{/if}
				</td>				
			</tr>		
			
			<tr height="10px">
				<td class="border_users_l border_users_b">
						Jobber:
				</td>
				<td class="border_users_l border_users_b">
					<div align="left" style='display:inline; float:left;'>
						<textarea name='jobber' rows="5" >{$data->jobber|escape}</textarea>
					</div>
				</td>				
			</tr>			
			
			<tr height="10px">
				<td class="border_users_l border_users_b">
						Comments:
				</td>
				<td class="border_users_l border_users_b border_users_r">
					<div align="left" style='display:inline; float:left;'>
						<textarea name='comments' rows="5" >{$data->comments|escape}</textarea>
					</div>
				</td>				
			</tr>
			
            <tr height="10px">
				<td class="border_users_l border_users_b">
						Shop type:
				</td>
				<td class="border_users_l border_users_b border_users_r">
					<div align="left" style='display:inline; float:left;'>
						<select name="shop_type">
							{foreach from=$data->getShopTypeOptions() item=shop_type_id key=shop_type_name}
								<option value="{$shop_type_id}" {if $shop_type_id == $data->shop_type}selected{/if}>{$shop_type_name}</option>
							{/foreach}
						</select>
					</div>
				</td>
			</tr>
            
            <tr height="10px">
				<td class="border_users_l border_users_b">
						Features:
				</td>
				<td class="border_users_l border_users_b border_users_r">
					<div align="left" style='display:inline; float:left;'>
                        {foreach from=$data->getFeaturesOptions() item=features_id key=features_name}
                            &nbsp;<input type="checkbox" name="features[]" value="{$features_id}" {if in_array($features_name, $data->getFeaturesName())}checked{/if}/> {$features_name|escape}
                        {/foreach}
					</div>
				</td>
			</tr>

			<tr>
             	 <td height="20" class="users_u_bottom">
             	 	All Fields marked with a red * are required
                 </td>
                 <td height="20" class="users_u_bottom_r">
                 	&nbsp;
                 </td>
            </tr>			
						
						
			</table>
	<div align="right">
		<br>
		<input type='submit' name='save' class="button" value='Save'>
		<input type='button' class="button" id='cancelButton' value='Cancel' onclick="location.href='admin.php?action=browseCategory&category={if $request.bookmark="contacts"}salescontacts{/if}&bookmark={$request.bookmark}{if $request.page}&page={$request.page}{/if}{if $request.subBookmark}&subBookmark={$request.subBookmark}{/if}'">
		<span style="padding-right:50">&nbsp;</span>
		</div>
		{if $request.action eq "addItem"}<input type='hidden' name='creater_id' value={$creater_id}>{/if}
		</form>
</div>

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
	<form method='POST' action='admin.php?action={$request.action}&category=product{if $request.action neq "addItem"}&id={$request.id}{else}&companyID={$request.comanyID}{/if}'>
		<table class="users" align="center" cellpadding="0" cellspacing="0">
			<tr class="users_u_top_size users_top" >
				<td class="users_u_top" width="27%" height="30" >
					<span >{if $request.action eq "addItem"}Adding for a new product{else}Editing product{/if}</span>
				</td>
				<td class="users_u_top_r" >
					&nbsp;
				</td>			
			</tr>
			
			<tr>
							<td class="border_users_l border_users_b" height="20">
									Product No :
							</td>
							<td class="border_users_l border_users_b border_users_r">
							<div align="left" >	<input type='' name='product_nr' value='{$data.product_nr}'></div>
							{if $validStatus.summary eq 'false'}
							{if $validStatus.product_nr eq 'failed'}
							
								{*ERORR*}  
									<div style="width:80px;margin:2px 0px 0px 5px;" align="left"><img src='design/user/img/alert1.gif' height=16  style="float:left;">
									<font style="float:left;vertical-align:bottom;color:red;margin:1px 0px 0px 5px;">Error!</font></div>
								{*/ERORR*}
							{elseif $validStatus.product_nr eq 'alredyExist'}
								<div style="width:220;margin:2px 0px 0px 5px;" ><img src='design/user/img/alert1.gif' height=16  style="float:left;">
							    <font style="float:left;vertical-align:bottom;color:red;margin:1px 0px 0px 5px;">Entered name is alredy in use!</font></div>
							{/if}
							{/if}
						
							</td>
						</tr>
						
						<tr>
							<td class="border_users_l border_users_b" height="20">
									Name :
							</td>
							<td class="border_users_l border_users_b border_users_r">
								<div align="left" ><input type='text' name='name' value='{$data.name}'></div>
							
							{if $validStatus.summary eq 'false'}
							{if $validStatus.name eq 'failed'}
						
								{*ERORR*}
									<div style="width:80px;margin:2px 0px 0px 5px;" align="left"><img src='design/user/img/alert1.gif' height=16  style="float:left;">
									<font style="float:left;vertical-align:bottom;color:red;margin:1px 0px 0px 5px;">Error!</font></div>
								{*/ERORR*}
							{/if}
							{/if}
								
							</td>
						</tr>
						
						
						<!--<tr>
							<td class="poloscenter" height="20">
								<hr>3. Inventory :
							</td>
							<td class="pcenter">
							<div align="left" >	

				<select name="selectInventory" id="selectInventory"  onChange="getInventoryShortInfo(this)">
						<option value='0' {if $inventory[i].inventory_id eq $data.inventory_id}selected="selected"{/if}></option>
					{section name=i loop=$inventory}
						<option value='{$inventory[i].inventory_id}' {if $inventory[i].inventory_id eq $data.inventory_id}selected="selected"{/if}> {$inventory[i].name} </option>
					{/section}
				</select>
				
				</div>
							
							
						
							</td>
						</tr>
						
						
						<tr>
							<td class="poloscenter" height="20">
								Description :<hr>
							</td>
							<td class="pcenter">
							<div align="left" >	<input type='text' name='inventoryDescription' id='inventoryDescription' value='{$data.inventory_desc}'></div>
						
							{if $validStatus.summary eq 'false'}
							{if $validStatus.inventory_desc eq 'failed'}
							
								{*ERORR*}
									<div style="width:80px;margin:2px 0px 0px 5px;" align="left"><img src='design/user/img/alert1.gif' height=16  style="float:left;">
									<font style="float:left;vertical-align:bottom;color:red;margin:1px 0px 0px 5px;">Error!</font></div>
								{*/ERORR*}
							{/if}
							{/if}
							
							</td>
						</tr>-->


						<tr>
							<td class="border_users_l border_users_b" height="20">
									VOCLX :
							</td>
							<td class="border_users_l border_users_b border_users_r">
							<div align="left" >	<input type='text' name='voclx' id='voclx' value='{$data.voclx}'></div>
						
							{if $validStatus.summary eq 'false'}
							{if $validStatus.voclx eq 'failed'}
								{*ERORR*}
									<div style="width:80px;margin:2px 0px 0px 5px;" align="left"><img src='design/user/img/alert1.gif' height=16  style="float:left;">
									<font style="float:left;vertical-align:bottom;color:red;margin:1px 0px 0px 5px;">Error!</font></div>
								{*/ERORR*}
							{/if}
							{/if}
							
							</td>
						</tr>
						
						<tr>
							<td class="border_users_l border_users_b" height="20">
									VOCWX :
							</td>
							<td class="border_users_l border_users_b border_users_r">
							<div align="left" >	<input type='text' name='vocwx' id='vocwx' value='{$data.vocwx}'></div>
							
							{if $validStatus.summary eq 'false'}
							{if $validStatus.vocwx eq 'failed'}
						
								{*ERORR*}
									<div style="width:80px;margin:2px 0px 0px 5px;" align="left"><img src='design/user/img/alert1.gif' height=16  style="float:left;">
									<font style="float:left;vertical-align:bottom;color:red;margin:1px 0px 0px 5px;">Error!</font></div>
								{*/ERORR*}
							{/if}
							{/if}
						
							</td>
						</tr>
						
						<tr>
							<td class="border_users_l border_users_b" height="20">
								Percent Volatile by Weight :
							</td>
							<td class="border_users_l border_users_b border_users_r">
								<div align="left" ><input type="text" name="percent_volatile_weight" value="{$data.percent_volatile_weight}"></div>
								{if $validStatus.summary eq 'false'}
								{if $validStatus.percent_volatile_weight eq 'failed'}
								{*ERORR*}
									<div style="width:80px;margin:2px 0px 0px 5px;" align="left"><img src='design/user/img/alert1.gif' height=16  style="float:left;">
									<font style="float:left;vertical-align:bottom;color:red;margin:1px 0px 0px 5px;">Error!</font></div>
								{*/ERORR*}
								{/if}
								{/if}   
							</td>
						</tr>
						<tr>
							<td class="border_users_l border_users_b" height="20">
								Percent Volatile by Volume :
							</td>
							<td class="border_users_l border_users_b border_users_rpcenter">
								<div align="left" ><input type="text" name="percent_volatile_volume" value="{$data.percent_volatile_volume}"></div>
								{if $validStatus.summary eq 'false'}
								{if $validStatus.percent_volatile_volume eq 'failed'}
								{*ERORR*}
									<div style="width:80px;margin:2px 0px 0px 5px;" align="left"><img src='design/user/img/alert1.gif' height=16  style="float:left;">
									<font style="float:left;vertical-align:bottom;color:red;margin:1px 0px 0px 5px;">Error!</font></div>
								{*/ERORR*}
								{/if}
								{/if}  
							</td>
						</tr>
						
						<tr>
							<td class="border_users_l border_users_b" height="20">
									Density:
							</td>
							<td class="border_users_l border_users_b border_users_r">
							<div align="left" >	
								<input type='text' name='density' value='{$data.density}'>
								<select id="selectDensityType" name="selectDensityType" style="width:108px">
									{section name=i loop=$densityDetails}	
										<option value='{$densityDetails[i].id}' {if $densityDetails[i].id eq $densityDefault}selected='selected'{/if}>{$densityDetails[i].numerator}/{$densityDetails[i].denominator}</option>										
									{/section}
								</select>
							</div>
							
							{if $validStatus.summary eq 'false'}
							{if $validStatus.density eq 'failed'}
						
								{*ERORR*}
									<div style="width:80px;margin:2px 0px 0px 5px;" align="left"><img src='design/user/img/alert1.gif' height=16  style="float:left;">
									<font style="float:left;vertical-align:bottom;color:red;margin:1px 0px 0px 5px;">Error!</font></div>
								{*/ERORR*}
							{/if}
							{/if}
						
							</td>
						</tr>
							
							
												
								
						<tr>
							<td class="border_users_l border_users_b" height="20">
									Coating:
							</td>
							<td class="border_users_l border_users_b border_users_r">
							<div align="left" >	
							
				<select name="selectCoat" id="selectCoat">
					{section name=i loop=$coat}
						<option value='{$coat[i].coat_id}' {if $coat[i].coat_id eq $data.coating_id}selected="selected"{/if}> {$coat[i].description} </option>
					{/section}
				</select>
				
				</div>
							{if $validStatus.summary eq 'false'}
							{if $validStatus.coating_id eq 'failed'}
						
								{*ERORR*}
									<div style="width:80px;margin:2px 0px 0px 5px;" align="left"><img src='design/user/img/alert1.gif' height=16  style="float:left;">
									<font style="float:left;vertical-align:bottom;color:red;margin:1px 0px 0px 5px;">Error!</font></div>
								{*/ERORR*}
							{/if}
							{/if}
							</td>
						</tr>
						
						
						<tr>
							<td class="border_users_l border_users_b" height="20">
									Specialty Coating :
							</td>
							<td class="border_users_l border_users_b border_users_r">
							<div align="left" >				
								<input type="checkbox" name="specialty_coating" value="yes" {if $data.specialty_coating=="yes"}checked="yes"{/if}>
							</div>
							</td>
						</tr>
						
						<tr>
							<td class="border_users_l border_users_b" height="20">
									Aerosol :
							</td>
							<td class="border_users_l border_users_b border_users_r">
							<div align="left" >					
								<input type="checkbox" name="aerosol" value="yes"{if $data.aerosol=="yes"}checked="yes"{/if}>
							</div>
							</td>
						</tr>
						
						<tr>
							<td class="border_users_l border_users_b" height="20">
									Specific gravity:
							</td>
							<td class="border_users_l border_users_b border_users_r">
							<div align="left" >	
								<input type='text' name='specific_gravity' value='{$data.specific_gravity}'>
								<select id="selectGravityType" name="selectGravityType" style="width:108px">
									{section name=i loop=$densityDetails}	
										<option value='{$densityDetails[i].id}' {if $densityDetails[i].id eq $data.specific_gravity_unit_id}selected='selected'{/if}>{$densityDetails[i].numerator}/{$densityDetails[i].denominator}</option>										
									{/section}
								</select>
							</div>
							
							{if $validStatus.summary eq 'false'}
							{if $validStatus.specific_gravity eq 'failed'}
						
								{*ERORR*}
									<div style="width:80px;margin:2px 0px 0px 5px;" align="left"><img src='design/user/img/alert1.gif' height=16  style="float:left;">
									<font style="float:left;vertical-align:bottom;color:red;margin:1px 0px 0px 5px;">Error!</font></div>
								{*/ERORR*}
							{/if}
							{/if}
						
							</td>
						</tr>
						
						
						<tr>
							<td class="border_users_l border_users_b" height="20">
									Boiling range :
							</td>
							<td class="border_users_l border_users_b border_users_r">
							<div align="left" > from <input type='text' name='boiling_range_from' value='{$data.boiling_range_from}' style='width:50px;'> to <input type='text' name='boiling_range_to' value='{$data.boiling_range_to}' style='width:50px;'></div>
							
							{if $validStatus.summary eq 'false'}
							{if $validStatus.boiling_range_from eq 'failed' || $validStatus.boiling_range_to eq 'failed'}
						
								{*ERORR*}
									<div style="width:80px;margin:2px 0px 0px 5px;" align="left"><img src='design/user/img/alert1.gif' height=16  style="float:left;">
									<font style="float:left;vertical-align:bottom;color:red;margin:1px 0px 0px 5px;">Error!</font></div>
								{*/ERORR*}
							{/if}
							{/if}
							</td>
						</tr>
						
						<tr>
							<td class="border_users_l border_users_b" height="20">
									Hazardous:
							</td>
							<td class="border_users_l border_users_b border_users_r">
							<div align="left" >
								<div id="chemicalClassString">
									&nbsp;{section name=i loop=$data.chemicalClasses}{$data.chemicalClasses[i].name};&nbsp;{/section}
								</div>
								<div>							
									<a href="#" onclick="$('#hazardousPopup').dialog('open');return false;">edit</a>
								</div>
								<div id="hiddenChemicalClasses">
									{section name=i loop=$data.chemicalClasses}
										<input type="hidden" name="chemicalClass_{$smarty.section.i.index}" value="{$data.chemicalClasses[i].id}">
										{section name=j loop=$data.chemicalClasses[i].rules}
											<input type="hidden" name="chemicalRule_{$smarty.section.i.index}_{$smarty.section.j.index}" value="{$data.chemicalClasses[i].rules[j]}">
										{/section}
									{/section} 	
								</div>
							
							</div>
							</td>
						</tr>
						
						
						<tr>
							<td class="border_users_l border_users_b" height="20">
									Supplier :
							</td>
							<td class="border_users_l border_users_b border_users_r">
							<div align="left" >	
				
				<select name="selectSupplier" id="selectSupplier">
					{section name=i loop=$supplier}
						<option value='{$supplier[i].supplier_id}' {if $supplier[i].supplier_id eq $data.supplier_id}selected="selected"{/if}> {$supplier[i].supplier_desc} </option>
					{/section}
				</select>
				
				</div>
							{if $validStatus.summary eq 'false'}
							{if $validStatus.supplier_id eq 'failed'}
						
								{*ERORR*}
									<div style="width:80px;margin:2px 0px 0px 5px;" align="left"><img src='design/user/img/alert1.gif' height=16  style="float:left;">
									<font style="float:left;vertical-align:bottom;color:red;margin:1px 0px 0px 5px;">Error!</font></div>
								{*/ERORR*}
							{/if}
							{/if}
							</td>
						</tr>
												
						<tr class="users_u_top_size users_top_lightgray" >
							<td>
								Add new compound								
							</td>
							<td>	
								&nbsp;							
								{if $validStatus.summary eq 'false'}
								{if $validStatus.isComponents eq 'failed'}														
                        			{*ERORR*}
										<div class="error_img"><span class="error_text">Error! Please add compounds</span></div>
									{*/ERORR*}
								{/if}
								{/if} 
							</td>	
						</tr>
						
						<tr>
							<td class="border_users_l border_users_b" height="20">
								Compound :
							</td>
							<td class="border_users_l border_users_b border_users_r">
							<div align="left" >	
								<select name="selectComponent" id="selectComponent" class="addInventory" onChange="getComponentDetails(this)">
									{section name=i loop=$component}
										<option value='{$component[i].component_id}' {if $component[i].component_id eq $data.component_id}selected="selected"{/if}> {$component[i].description} </option>
									{/section}
								</select>
								{*<input type='button' class='Button' id='AddRemoveSearch' value='Add Compound Search'>*}
							</div>
							</td>
						</tr>
						
						
						<tr>
							<td class="border_users_l border_users_b" height="20">
								Case Number :
							</td>
							<td class="border_users_l border_users_b border_users_r">
							<div align="left" >	 <input type='text' name='componentCas' id='componentCas' readonly value='{$data.cas}'> </div>
							{if $validStatus.summary eq 'false'}
							{if $validStatus.cas eq 'failed'}
								{*ERORR*}
									<div style="width:80px;margin:2px 0px 0px 5px;" align="left"><img src='design/user/img/alert1.gif' height=16  style="float:left;">
									<font style="float:left;vertical-align:bottom;color:red;margin:1px 0px 0px 5px;">Error!</font></div>
								{*/ERORR*}
							{/if}
							{/if}
							</td>
						</tr>
						
						<tr>
							<td class="border_users_l border_users_b" height="20">
								Description :
							</td>
							<td class="border_users_l border_users_b border_users_r">
							<div align="left" >	 <input type='text' name='componentDescription' id='componentDescription' readonly value='{$data.comp_desc}'> </div>
													{if $validStatus.summary eq 'false'}
														{if $validStatus.componentDescription eq 'failed'}
								
								{*ERORR*}
									<div style="width:80px;margin:2px 0px 0px 5px;" align="left"><img src='design/user/img/alert1.gif' height=16  style="float:left;">
									<font style="float:left;vertical-align:bottom;color:red;margin:1px 0px 0px 5px;">Error!</font></div>
								{*/ERORR*}
							{/if}
							{/if}
							</td>
						</tr>
						
						<tr>
							<td class="border_users_l border_users_b" height="20">
									Temp VP :
							</td>
							<td class="border_users_l border_users_b border_users_r">
							<div align="left" >	<input type='text' name='temp_vp' id='temp_vp' value='{$data.temp_vp}'></div>
							
							{if $validStatus.summary eq 'false'}
							{if $validStatus.temp_vp eq 'failed'}
									<div style="width:80px;margin:2px 0px 0px 5px;" align="left"><img src='design/user/img/alert1.gif' height=16  style="float:left;">
									<font style="float:left;vertical-align:bottom;color:red;margin:1px 0px 0px 5px;">Error!</font></div>
							{/if}
							{/if}
						
							</td>
						</tr>
						
						<tr>
							<td class="border_users_l border_users_b" height="20">
								Substrate :
							</td>
							<td class="border_users_l border_users_b border_users_r">
							<div align="left" >	
								<select name="selectSubstrate" id="selectSubstrate">
									<option value='0' {if $substrate[i].substrate_id eq $data.substrate_id}selected="selected"{/if}></option>
									{section name=i loop=$substrate}
										<option value='{$substrate[i].substrate_id}' {if $substrate[i].substrate_id eq $data.substrate_id}selected="selected"{/if}> {$substrate[i].substrate_desc}</option>
									{/section}
								</select>
							</div>
							</td>
						</tr>
						
						<tr>
							<td class="border_users_l border_users_b" height="20">
								Rule :
							</td>
							<td class="border_users_l border_users_b border_users_r">
							<div align="left" >	
								<select name="selectRule" id="selectRule">
									<option value='0' {if $rule[i].rule_id eq $data.rule_id}selected="selected"{/if}></option>
									{section name=i loop=$rule}
										<option value='{$rule[i].rule_id}' {if $rule[i].rule_id eq $data.rule_id}selected="selected"{/if}> {$rule[i].rule_nr} </option>
									{/section}
								</select>
							</div>
							</td>
						</tr>
						
						<tr>
							<td class="border_users_l border_users_b" height="20">
								 MM HG :
							</td>
							<td class="border_users_l border_users_b border_users_r">
							<div align="left" >	<input type='text' name='mm_hg' id='mm_hg' value='{$data.mm_hg}'></div>
							
							{if $validStatus.summary eq 'false'}
							{if $validStatus.mm_hg eq 'failed'}
									<div style="width:80px;margin:2px 0px 0px 5px;" align="left"><img src='design/user/img/alert1.gif' height=16  style="float:left;">
									<font style="float:left;vertical-align:bottom;color:red;margin:1px 0px 0px 5px;">Error!</font></div>
							{/if}
							{/if}
						
							</td>
						</tr>
						
						<tr>
							<td class="border_users_l border_users_b" height="20">
									Weight :
							</td>
							<td class="border_users_l border_users_b border_users_r">
							<div align="left" >	<input type='text' name='weight' id='weight' value='{$data.weight}'></div>
							
							{if $validStatus.summary eq 'false'}
							{if $validStatus.weight eq 'failed'}
									<div style="width:80px;margin:2px 0px 0px 5px;" align="left"><img src='design/user/img/alert1.gif' height=16  style="float:left;">
									<font style="float:left;vertical-align:bottom;color:red;margin:1px 0px 0px 5px;">Error!</font></div>
							{/if}
							{/if}
						
							</td>
						</tr>
						
						<!-- TYPE: VOC/PM -->
						<tr>
							<td class="border_users_l border_users_b" height="20">
									Type :
							</td>
							<td class="border_users_l border_users_b border_users_r">
							<div align="left" >
								<select name="type" id="type">
									<option value='VOC' {if 'VOC' eq $data.type}selected="selected"{/if}> VOC </option>
									<option value='PM' {if 'PM' eq $data.type}selected="selected"{/if}> PM </option>								
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
		<input type='submit' name='save' class="button" value='Add compound to product'>
		<input type='submit' name='save' class="button" value='Save'>
		<input type='button' name='cancel' class="button" value='Cancel' 
			{if $request.action=='edit'} onclick='location.href="admin.php?action=viewDetails&category=product&id={$request.id}"'{/if}
			{if $request.action=='addItem'} onclick='location.href="admin.php?action=browseCategory&category=tables&bookmark=product"'{/if}>
		<span style="padding-right:50">&nbsp;</span>
	</div>
		
			
{if $componentCount > 0}		
<div style="padding:7px;">
	<table class="users" align="center" cellpadding="0" cellspacing="0">
        <tr class="users_u_top_size users_top" >
			<td class="users_u_top" width="27%" height="30" > 
				Select
			</td>
			<td class="users_u_top_r">
				Compound Name
			</td>
		</tr>
	 
		{section name=i loop=$componentCount}						
		<tr>
			<td class="border_users_l border_users_b" height="20">
				<input type="checkbox"  checked="checked" value="{$compsAdded[i].component_id}" name="component_id_{$smarty.section.i.index}" onclick="return CheckCB(this);">
			</td>

			<td class="border_users_l border_users_b border_users_r">         
			    <div style="width:100%;">
					{$compsAdded[i].comp_cas}
				</div >		
			</td>
		</tr>
{/section}						
						
		<tr>
             <td height="20" class="users_u_bottom">
             	 &nbsp;
             </td>
             <td height="20" class="users_u_bottom_r">
                 &nbsp;
             </td>
        </tr>
	</table>				
		
{/if}			
		{section name=i loop=$componentCount}
			{*<input type='hidden' name='component_id_{$smarty.section.i.index}' value='{$compsAdded[i].component_id}'>*}
			<input type='hidden' name='comp_cas_{$smarty.section.i.index}' value='{$compsAdded[i].comp_cas}'>
			<input type='hidden' name='temp_vp_{$smarty.section.i.index}' value='{$compsAdded[i].temp_vp}'>
			<input type='hidden' name='substrate_{$smarty.section.i.index}' value='{$compsAdded[i].substrate_id}'>
			<input type='hidden' name='rule_id_{$smarty.section.i.index}' value='{$compsAdded[i].rule_id}'>
			<input type='hidden' name='mm_hg_{$smarty.section.i.index}' value='{$compsAdded[i].mm_hg}'>
			<input type='hidden' name='weight_{$smarty.section.i.index}' value='{$compsAdded[i].weight}'>
			<input type='hidden' name='type_{$smarty.section.i.index}' value='{$compsAdded[i].type}'>
			
		{/section}	
		
		<input type='hidden' name='componentCount' value='{$componentCount}'>
		
		{*  <input type='hidden' name='action' value='{$currentOperation}'>
		<input type='hidden' name='itemID' value='product'>
		<input type='hidden' name='categoryID' value='class'>
		
		{if $currentOperation eq "updateItem"}
			<input type="hidden" name="id" value="{$ID}">
			<input type="hidden" name="hazardous_class_id" value="{$data.hazardous_class_id}">
			<input type="hidden" name="componentgroup_nr" value="{$data.componentgroup_nr}">
		{/if}*}
		
</form>
</div>


{*JQUERY POPUP SETTINGS*}
<link href="modules/js/jquery-ui-1.8.2.custom/css/smoothness/jquery-ui-1.8.2.custom.css" rel="stylesheet" type="text/css"/>

<script type="text/javascript" src="modules/js/jquery-ui-1.8.2.custom/development-bundle/external/jquery.bgiframe-2.1.1.js"></script>
<script type="text/javascript" src="modules/js/jquery-ui-1.8.2.custom/development-bundle/ui/jquery.ui.core.js"></script>
<script type="text/javascript" src="modules/js/jquery-ui-1.8.2.custom/development-bundle/ui/jquery.ui.widget.js"></script>
<script type="text/javascript" src="modules/js/jquery-ui-1.8.2.custom/development-bundle/ui/jquery.ui.mouse.js"></script>
<script type="text/javascript" src="modules/js/jquery-ui-1.8.2.custom/development-bundle/ui/jquery.ui.draggable.js"></script>
<script type="text/javascript" src="modules/js/jquery-ui-1.8.2.custom/development-bundle/ui/jquery.ui.position.js"></script>
<script type="text/javascript" src="modules/js/jquery-ui-1.8.2.custom/development-bundle/ui/jquery.ui.resizable.js"></script>
<script type="text/javascript" src="modules/js/jquery-ui-1.8.2.custom/development-bundle/ui/jquery.ui.dialog.js"></script>
{*END OF SETTINGS*}		
		

{*SELECT_HAZARDOUS_CLASS_POPUP*}	
<div id="hazardousPopup" title="Choose chemical classes" style="background-color:#e3e9f8; padding:25px; font-size:150%; text-align:center;display:none;">		
		 <table id="chemClassList" width="750px" cellpadding="0" cellspacing="0" class="popup_table" align="center" >		
			<tr>			
				 <td class="control_list" colspan="3" style="border-bottom:0px solid #fff;padding-left:0px">						
					Select: 
					<a onclick="CheckAll(this)" name="allChemicalClasses" class="id_company1" >All</a>									
				 	/
					<a onclick="unCheckAll(this)" name="allChemicalClasses" class="id_company1">None</a>										
				</td>
			</tr>		
					
			<tr class="table_popup_rule">
				<td>
					Select
				</td>
				<td>
					Name
				</td>
				<td>
					Description
				</td>
				<td>
					Rule
				</td>
			</tr>
		
			{section name=i loop=$chemicalClassesList}
			<tr>
				<td align="center" style="width:150px">				
					<input id="checkBox_{$smarty.section.i.index}" type="checkbox"  value="{$chemicalClassesList[i].id}" 
					{assign var='checked' value=false}
						{section name=j loop=$data.chemicalClasses}
							{if $chemicalClassesList[i].id == $data.chemicalClasses[j].id}
								checked
								{assign var='checked' value=true}
								{assign var='selectedRules' value=$data.chemicalClasses[j].rules}
								{break}
							{/if}
						{/section}
					onClick="addRuleSelector({$smarty.section.i.index});"></td>
				</td>
				<td id="chemicalClassName_{$smarty.section.i.index}">
					{$chemicalClassesList[i].name}&nbsp;
				</td>
				<td>
					{$chemicalClassesList[i].description}&nbsp;
				</td>
				<td>
					<div align="left" id="rules_{$smarty.section.i.index}" >
						{if $checked}
							<input type="button" value="-" onClick="deleteLastRuleSelector({$smarty.section.i.index});" />
							<input type="button" value="+" onClick="addRuleSelector({$smarty.section.i.index});" />
							{if $selectedRules|@count > 0}	
							{section name=f loop=$selectedRules}
								<select name="chemicalRule_{$smarty.section.i.index}_{$smarty.section.f.index}" id="chemicalRule_{$smarty.section.i.index}_{$smarty.section.f.index}">
									<option value='0'></option>
									{section name=k loop=$rule}
										<option value='{$rule[k].rule_id}' 
											{if $selectedRules[f] == $rule[k].rule_id}
												selected
												{break}
											{/if}> 
											{$rule[k].rule_nr}
										</option>
									{/section}
								</select>
							{/section}
							<input type="hidden" id="rulesCount_{$smarty.section.i.index}" name="rulesCount_{$smarty.section.i.index}" value="{$selectedRules|@count}" />
							{else}
								<select name="chemicalRule_{$smarty.section.i.index}_0" id="chemicalRule_{$smarty.section.i.index}_0">
									<option value='0'></option>
									{section name=k loop=$rule}
										<option value='{$rule[k].rule_id}'>
											{$rule[k].rule_nr}
										</option>
									{/section}
								</select>
								<input type="hidden" id="rulesCount_{$smarty.section.i.index}" name="rulesCount_{$smarty.section.i.index}" value="1" />
							{/if}
						{else}
							&nbsp;
						{/if}
					</div>
				</td>
			</tr>
			{/section}
				
		</table>	
		<div align="left" id="rules_default" style="display:none;" >
			<select name="chemicalRule" id="chemicalRule">
				<option value='0'></option>
				{section name=k loop=$rule}
					<option value='{$rule[k].rule_id}'>
						{$rule[k].rule_nr}
					</option>
				{/section}
			</select>
		</div>
</div>


<div id="notifyContainer">
	{if $color eq "green"}
		{include file="tpls:tpls/notify/greenNotify.tpl" text=$message}
	{/if}
	{if $color eq "orange"}
		{include file="tpls:tpls/notify/orangeNotify.tpl" text=$message}
	{/if}
	{if $color eq "blue"}
		{include file="tpls:tpls/notify/blueNotify.tpl" text=$message}
	{/if}
</div>

<div style="padding:7px;">

	<form id="addEquipmentForm" name="addEquipment">		
        
		<table class="users" align="center" cellpadding="0" cellspacing="0">
			<tr class="users_header_orange">
				<td height="30" width="30%">
					<div class="users_header_orange_l"><div><span ><b>{if $request.action eq "addItem"}Adding for a new equipment{else}Editing equipment{/if}</b></span></div></div>
				</td>
				<td><div class="users_header_orange_r"><div>&nbsp;</div></div>				
				</td>								
			</tr>				
						
			<tr>
				<td class="border_users_l border_users_b" width="15%" height="20">
					Description
				</td>
				<td class="border_users_l border_users_b border_users_r">
					<div align="left" >	<input type='text' name='equip_desc' value='{$data.equip_desc}'></div>												
								{*ERORR*}
									<div id="error_equip_desc" style="width:80px;margin:2px 0px 0px 5px; display:none;" align="left"><img src='design/user/img/alert1.gif' height=16  style="float:left;">
									<font style="float:left;vertical-align:bottom;color:red;margin:1px 0px 0px 5px;">Error!</font></div>
								{*/ERORR*}														
				</td>
			</tr>
									
			<tr>
				<td class="border_users_l border_users_b" height="20">
					Permit
				</td>
				<td class="border_users_l border_users_b border_users_r">
					<div align="left" >	<input type='text' name='permit' value='{$data.permit}'></div>				
								{*ERORR*}
									<div id="error_permit" style="width:80px;margin:2px 0px 0px 5px; display:none;" align="left"><img src='design/user/img/alert1.gif' height=16  style="float:left;">
									<font style="float:left;vertical-align:bottom;color:red;margin:1px 0px 0px 5px;">Error!</font></div>
								{*/ERORR*}							
				</td>
			</tr>
		
			<tr>
				<td class="border_users_l border_users_b" height="20">
					Expire {*if $data.date_type=='d-m-Y g:iA'}(dd-mm-yyyy){else}(mm/dd/yyyy){/if*}({$data.expire->getFormatInfo()})				
				</td>
				<td class="border_users_l border_users_b border_users_r">
					<div align="left" >
						<input type="text" name="expire_date" id="calendar1" class="calendarFocus" value='{$data.expire->formatOutput()}'/>							
					</div>												
								{*ERORR*}
									<div id="error_expire_date"style="width:80px;margin:2px 0px 0px 5px; display:none;" align="left"><img src='design/user/img/alert1.gif' height=16  style="float:left;">
									<font style="float:left;vertical-align:bottom;color:red;margin:1px 0px 0px 5px;">Error!</font></div>
								{*/ERORR*}			
				</td>
			</tr>
			
			<tr>
				<td class="border_users_l border_users_b" height="20">
					Daily
				</td>
				<td class="border_users_l border_users_b border_users_r">
					<div align="left" >	<input type='text' name='daily' id='daily' value='{$data.daily}'"></div>															
								{*ERORR*}
									<div id="error_daily" style="width:80px;margin:2px 0px 0px 5px; display:none;" align="left"><img src='design/user/img/alert1.gif' height=16  style="float:left;">
									<font style="float:left;vertical-align:bottom;color:red;margin:1px 0px 0px 5px;">Error!</font></div>
								{*/ERORR*}				
				</td>
			</tr>
			
			<tr>
				<td class="border_users_l border_users_b" height="20">
					Department track :
				</td>
				<td class="border_users_l border_users_b border_users_r">
					<div align="left" >					
						<input type="checkbox" name="dept_track" value="yes" {if $data.dept_track!="no"}checked="yes"{/if}">
					</div>
				</td>
			</tr>
						
			<tr>
				<td class="border_users_l border_users_b" height="20">
					Facility track :
				</td>
				<td class="border_users_l border_users_b border_users_r">
					<div align="left" >					
						<input type="checkbox" name="facility_track" value="yes" {if $data.facility_track!=="no"}checked="yes"{/if}">
					</div>
				</td>
			</tr>
			
			{if $show.inventory}
				{include file="tpls:inventory/design/addEquipment.tpl" inventory=$inventoryList inventoryDet=$inventoryDet data=$data}
			{/if}
			<tr>
				<td class="border_users_l border_users_b" height="20">
					MODEL No.:
				</td>
				<td class="border_users_l border_users_b border_users_r">
					<div align="left" >	<input type='text' name='model_number' id='model_number' value='{$data.model_number}'"></div>
				</td>
			</tr>
			<tr>
				<td class="border_users_l border_users_b" height="20">
					SERIAL No.:
				</td>
				<td class="border_users_l border_users_b border_users_r">
					<div align="left" >	<input type='text' name='serial_number' id='serial_number' value='{$data.serial_number}'"></div>
				</td>
			</tr>
			<tr>
				<td colspan="2">
					{*ADD FILTER*}
					<table class="users" cellpadding="0" cellspacing="0" align="center">
						<tr class="users_header_orange">
                                                        <td height="30" width="30%">
                                                                <div class="users_header_orange_l"><div><span ><b>Add Filters</b></span></div></div>
                                                        </td>
                                                        <td><div class="users_header_orange_r"><div>&nbsp;</div></div>				
                                                        </td>								
                                                </tr>

						<tr>
							<td class="border_users_l border_users_b border_users_r" width="30%">
								Name :
							</td>
							<td class="border_users_r border_users_b">								
								<div class="floatleft">							
									<input type='text' id='equipment_filter_name' name='equipment_filter_name' value=''>
								</div>		
								{*ERORR*}
									<div id="error_equipment_filter_name" style="width:80px;margin:2px 0px 0px 5px; display:none;" align="left"><img src='design/user/img/alert1.gif' height=16  style="float:left;">
									<font class="error_text" style="float:left;vertical-align:bottom;color:red;margin:1px 0px 0px 5px;">Error!</font></div>
								{*/ERORR*}
							</td>
						</tr>
						<tr>
							<td class="border_users_l border_users_b border_users_r" width="30%">
								height size :
							</td>
							<td class="border_users_r border_users_b">								
								<div class="floatleft">							
									<input type='text' id='equipment_height_size' name='equipment_height_size' value=''>
								</div>	
								{*ERORR*}
									<div id="error_equipment_height_size" style="width:80px;margin:2px 0px 0px 5px; display:none;" align="left"><img src='design/user/img/alert1.gif' height=16  style="float:left;">
									<font class="error_text" style="float:left;vertical-align:bottom;color:red;margin:1px 0px 0px 5px;">Error!</font></div>
								{*/ERORR*}
							</td>
						</tr>
						<tr>
							<td class="border_users_l border_users_b border_users_r" width="30%">
								width size :
							</td>
							<td class="border_users_r border_users_b">								
								<div class="floatleft">							
									<input type='text' id='equipment_width_size' name='equipment_width_size' value=''>
								</div>	
								{*ERORR*}
									<div id="error_equipment_width_size" style="width:80px;margin:2px 0px 0px 5px; display:none;" align="left"><img src='design/user/img/alert1.gif' height=16  style="float:left;">
									<font class="error_text" style="float:left;vertical-align:bottom;color:red;margin:1px 0px 0px 5px;">Error!</font></div>
								{*/ERORR*}
							</td>
						</tr>
						<tr>
							<td class="border_users_l border_users_b border_users_r" width="30%">
								length size :
							</td>
							<td class="border_users_r border_users_b">								
								<div class="floatleft">							
									<input type='text' id='equipment_length_size' name='equipment_length_size' value=''>
								</div>		
								{*ERORR*}
									<div id="error_equipment_length_size" style="width:80px;margin:2px 0px 0px 5px; display:none;" align="left"><img src='design/user/img/alert1.gif' height=16  style="float:left;">
									<font class="error_text" style="float:left;vertical-align:bottom;color:red;margin:1px 0px 0px 5px;">Error!</font></div>
								{*/ERORR*}
							</td>
						</tr>
						<tr>
							<td class="border_users_l border_users_b border_users_r">
								Filter Type :
							</td>
							<td class="border_users_r border_users_b">
								<div class="floatleft">	

								{*FILTER TYPE LIST*}	
								<select name="selectFilterType" id="selectFilterType" class="addInventory">
									{if $equipmentFilterType}				
										{section name=i loop=$equipmentFilterType}										
												<option value="{$equipmentFilterType[i]->equipment_filter_type_id}"> {$equipmentFilterType[i]->name}</option>										
										{/section}																		
									{else}
										<option value='0'> no filter types </option>
									{/if}
								</select>	
								</div>
							</td>
						</tr>

						<tr>
							<td class="border_users_l border_users_b border_users_r">
								Quantity :
							</td>
							<td class="border_users_r border_users_b">
							<div class="floatleft" >
								<input type='text' id="equipment_filter_quantity" name='equipment_filter_quantity' value='{$data.quantity}'>
							{*ERORR*}
							</div>
								<div id="error_equipment_filter_quantity" style="width:80px;margin:2px 0px 0px 5px; display:none;" align="left"><img src='design/user/img/alert1.gif' height=16  style="float:left;">
								<font class="error_text" style="float:left;vertical-align:bottom;color:red;margin:1px 0px 0px 5px;">Error!</font></div>
							{*/ERORR*}

							</td>
						</tr>
						<tr>
							<td>
							</td>
							<td>
								<div align="left" class="buttonpadd">									
								<img id="addProductPreloader" src='images/ajax-loader.gif' height=16  style="display:none;float:left;">								
								<input type='button' name='addProFilter' class="button" value='Add filter' onclick='addFilter2List();'>
								</div>
							</td>
						</tr>
					</table>
					{*/ADD FILTER*}						
				</td>
			</tr>
			<tr>
				<td colspan="2">
					{*ADD LIGHTING*}
					<table class="users" cellpadding="0" cellspacing="0" align="center">
						<tr class="users_header_orange">
                                                        <td height="30" width="30%">
                                                                <div class="users_header_orange_l"><div><span ><b>Add Lightings</b></span></div></div>
                                                        </td>
                                                        <td><div class="users_header_orange_r"><div>&nbsp;</div></div>				
                                                        </td>								
                                                </tr>

						<tr>
							<td class="border_users_l border_users_b border_users_r" width="30%">
								Name :
							</td>
							<td class="border_users_r border_users_b">								
								<div class="floatleft">							
									<input type='text' id='equipment_lighting_name' name='equipment_lighting_name' value=''>
								</div>	
								{*ERORR*}
									<div id="error_equipment_lighting_name" style="width:80px;margin:2px 0px 0px 5px; display:none;" align="left"><img src='design/user/img/alert1.gif' height=16  style="float:left;">
									<font class="error_text" style="float:left;vertical-align:bottom;color:red;margin:1px 0px 0px 5px;">Error!</font></div>
								{*/ERORR*}
							</td>
						</tr>
						<tr>
							<td class="border_users_l border_users_b border_users_r">
								Bulb type :
							</td>
							<td class="border_users_r border_users_b">
								<div class="floatleft">	

								{*Bulb type*}	
								<select name="selectBulbType" id="selectBulbType" class="addInventory">
									{if $lightingBulbType}				
										{section name=i loop=$lightingBulbType}										
												<option value='{$lightingBulbType[i]->equipment_lighting_bulb_type_id}'> {$lightingBulbType[i]->name}</option>										
										{/section}																			
									{else}
										<option value='0'> no Bulb Type </option>
									{/if}
								</select>	
								</div>
							</td>
						</tr>
						<tr>
							<td class="border_users_l border_users_b border_users_r" width="30%">
								Size :
							</td>
							<td class="border_users_r border_users_b">								
								<div class="floatleft">							
									<input type='text' id='equipment_lighting_size' name='equipment_lighting_size' value=''>
								</div>	
								{*ERORR*}
									<div id="error_equipment_lighting_size" style="width:80px;margin:2px 0px 0px 5px; display:none;" align="left"><img src='design/user/img/alert1.gif' height=16  style="float:left;">
									<font class="error_text" style="float:left;vertical-align:bottom;color:red;margin:1px 0px 0px 5px;">Error!</font></div>
								{*/ERORR*}
							</td>
						</tr>
						<tr>
							<td class="border_users_l border_users_b border_users_r" width="30%">
								Voltage :
							</td>
							<td class="border_users_r border_users_b">								
								<div class="floatleft">							
									<input type='text' id='equipment_lighting_voltage' name='equipment_lighting_voltage' value=''>
								</div>		
								{*ERORR*}
									<div id="error_equipment_lighting_voltage" style="width:80px;margin:2px 0px 0px 5px; display:none;" align="left"><img src='design/user/img/alert1.gif' height=16  style="float:left;">
									<font class="error_text" style="float:left;vertical-align:bottom;color:red;margin:1px 0px 0px 5px;">Error!</font></div>
								{*/ERORR*}
							</td>
						</tr>
						<tr>
							<td class="border_users_l border_users_b border_users_r" width="30%">
								Wattage :
							</td>
							<td class="border_users_r border_users_b">								
								<div class="floatleft">							
									<input type='text' id='equipment_lighting_wattage' name='equipment_lighting_wattage' value=''>
								</div>	
								{*ERORR*}
									<div id="error_equipment_lighting_wattage" style="width:80px;margin:2px 0px 0px 5px; display:none;" align="left"><img src='design/user/img/alert1.gif' height=16  style="float:left;">
									<font class="error_text" style="float:left;vertical-align:bottom;color:red;margin:1px 0px 0px 5px;">Error!</font></div>
								{*/ERORR*}
							</td>
						</tr>

						<tr>
							<td class="border_users_l border_users_b border_users_r">
								Color :
							</td>
							<td class="border_users_r border_users_b">
								<div class="floatleft">	

								{*Bulb type*}	
								<select name="selectLightingColor" id="selectLightingColor" class="addInventory">
									{if $lightingColor}				
										{section name=i loop=$lightingColor}										
												<option value='{$lightingColor[i]->equipment_lighting_color_id}'> {$lightingColor[i]->name}</option>										
										{/section}																		
									{else}
										<option value='0'> no Color </option>
									{/if}
								</select>	
								</div>
							</td>
						</tr>
						<tr>
							<td>
							</td>
							<td>
								<div align="left" class="buttonpadd">									
								<img id="addProductPreloader" src='images/ajax-loader.gif' height=16  style="display:none;float:left;">								
								<input type='button' name='addLighting' class="button" value='Add lighting' onclick='addLighting2List();'>
								</div>
							</td>
						</tr>
					</table>	
					{*/ADD LIGHTING*}
				</td>
				
			</tr>
            <tr>
             	 <td height="20" class="users_u_bottom">
                 </td>
                 <td height="20" class="users_u_bottom_r">
                 </td>
            </tr>
		
		</table>
		</br>
		
		<div id="filterContentDiv" style="display: {if !$equipmentFiltersList} none {else} table {/if}; width: 90%;"> 
		<table class="users" align="center" cellspacing="0" cellpadding="0">
		<thead>
			<tr class="users_u_top_size users_top_lightgray">
				<td  class="border_users_l"   width="10%" > Select</td>
				<td>Name</td>
				<td>height size</td>
				<td>width size</td>
				<td>length size</td>
				<td>Filter Type</td>
				<td class="border_users_r">Quantity</td>
			</tr>		
		</thead>

		<tbody id="filterContent" >
			{if $equipmentFiltersList}
				{section name=i loop=$equipmentFiltersList}		
					<tr id="filter_row_{$equipmentFiltersList[i]->equipment_filter_id}" class="border_users_l border_users_b">
						<td  class=border_users_l   width=10% ><input type=checkbox id=check_filter_{$smarty.section.i.index} value="{$equipmentFiltersList[i]->equipment_filter_id}"></td>
						<td> <input type='text' id='equipment_filter_name_{$equipmentFiltersList[i]->equipment_filter_id}' name='equipment_filter_name_{$equipmentFiltersList[i]->equipment_filter_id}' value='{$equipmentFiltersList[i]->name}'>	
						</td>
						<td><input type='text' id='equipment_height_size_{$equipmentFiltersList[i]->equipment_filter_id}' name='equipment_height_size_{$equipmentFiltersList[i]->equipment_filter_id}' value='{$equipmentFiltersList[i]->height_size}'>
						</td>
						<td> <input type='text' id='equipment_width_size_{$equipmentFiltersList[i]->equipment_filter_id}' name='equipment_width_size_{$equipmentFiltersList[i]->equipment_filter_id}' value='{$equipmentFiltersList[i]->width_size}'>
						</td>	
						<td> <input type='text' id='equipment_length_size_{$equipmentFiltersList[i]->equipment_filter_id}' name='equipment_length_size_{$equipmentFiltersList[i]->equipment_filter_id}' value='{$equipmentFiltersList[i]->length_size}'>	
						</td>
						<td id='equipmentFilterType_td_{$equipmentFiltersList[i]->equipment_filter_id}'>
							{*FILTER TYPE LIST*}	
							<select name="selectFilterType_{$equipmentFiltersList[i]->equipment_filter_id}" id="selectFilterType_{$equipmentFiltersList[i]->equipment_filter_id}" class="addInventory">
								{if $equipmentFilterType}				
									{section name=k loop=$equipmentFilterType}										
											<option value="{$equipmentFilterType[k]->equipment_filter_type_id}" {if $equipmentFiltersList[i]->equipment_filter_type_id == $equipmentFilterType[k]->equipment_filter_type_id} SELECTED {/if}> {$equipmentFilterType[k]->name}</option>										
									{/section}																		
								{else}
									<option value='0'> no filter types </option>
								{/if}
							</select>	
						</td>
						<td><input type='text' id='equipment_filter_quantity_{$equipmentFiltersList[i]->equipment_filter_id}' name='equipment_filter_quantity_{$equipmentFiltersList[i]->equipment_filter_id}' value='{$equipmentFiltersList[i]->qty}'></td>
					</tr>		
				{/section}

			{/if}

		</tbody>

		<tfoot>
			<tr class="">
				<td class="users_u_bottom" height="20">
                                    <a href="#" onclick="selectAllFilters(true); return false;">All</a>
                                    <a href="#" onclick="selectAllFilters(false);return false;">None</a>
                                </td>
                                <td colspan="6" class="users_u_bottom_r">
                                    <a href="#" onclick="clearSelectedFilters(); return false">Remove selected filters from the list</a>
                                </td>
			</tr>
		</tfoot>

	</table>
	</div>
	</br>
	<div id="lightingContentDiv" style="display: {if !$equipmentLightingsList} none {else} table {/if}; width: 90%;"> 
	<table class="users" align="center" cellspacing="0" cellpadding="0">
	<thead>
		<tr class="users_u_top_size users_top_lightgray">
			<td  class="border_users_l"   width="10%" > Select</td>
			<td>Name</td>
			<td>Bulb type</td>
			<td>Size</td>
			<td>Voltage</td>
			<td>Wattage</td>
			<td class="border_users_r">Color</td>
		</tr>		
	</thead>

	<tbody id="lightingContent" >
		{if $equipmentLightingsList}
				{section name=i loop=$equipmentLightingsList}		
					<tr id="lighting_row_{$equipmentLightingsList[i]->equipment_lighting_id}" class="border_users_l border_users_b">
						<td  class=border_users_l   width=10% ><input type=checkbox id=check_lighting_{$smarty.section.i.index} value="{$equipmentLightingsList[i]->equipment_lighting_id}"></td>
						<td> <input type='text' id='equipment_lighting_name_{$equipmentLightingsList[i]->equipment_lighting_id}' name='equipment_lighting_name_{$equipmentLightingsList[i]->equipment_lighting_id}' value='{$equipmentLightingsList[i]->name}'></td>
						<td id='equipmentBulbType_td_{$equipmentLightingsList[i]->equipment_lighting_id}'>
							{*FILTER TYPE LIST*}	
							<select name="selectBulbType_{$equipmentLightingsList[i]->equipment_lighting_id}" id="selectBulbType_{$equipmentLightingsList[i]->equipment_lighting_id}" class="addInventory">
								{if $lightingBulbType}				
									{section name=k loop=$lightingBulbType}										
											<option value="{$lightingBulbType[k]->equipment_lighting_bulb_type_id}" {if $equipmentLightingsList[i]->bulb_type == $lightingBulbType[k]->equipment_lighting_bulb_type_id} SELECTED {/if}> {$lightingBulbType[k]->name}</option>										
									{/section}																		
								{else}
									<option value='0'> no bulb types </option>
								{/if}
							</select>	
						</td>
						<td> <input type='text' id='equipment_lighting_size_{$equipmentLightingsList[i]->equipment_lighting_id}' name='equipment_lighting_size_{$equipmentLightingsList[i]->equipment_lighting_id}' value='{$equipmentLightingsList[i]->size}'></td>
						<td> <input type='text' id='equipment_lighting_voltage_{$equipmentLightingsList[i]->equipment_lighting_id}' name='equipment_lighting_voltage_{$equipmentLightingsList[i]->equipment_lighting_id}' value='{$equipmentLightingsList[i]->voltage}'></td>
						<td> <input type='text' id='equipment_lighting_wattage_{$equipmentLightingsList[i]->equipment_lighting_id}' name='equipment_lighting_wattage_{$equipmentLightingsList[i]->equipment_lighting_id}' value='{$equipmentLightingsList[i]->wattage}'></td>
						<td id='equipmentLightingColor_td_{$equipmentLightingsList[i]->equipment_lighting_id}'>
							{*FILTER TYPE LIST*}	
							<select name="selectLightingColor_{$equipmentLightingsList[i]->equipment_lighting_id}" id="selectLightingColor_{$equipmentLightingsList[i]->equipment_lighting_id}" class="addInventory">
								{if $lightingColor}				
									{section name=k loop=$lightingColor}										
											<option value="{$lightingColor[k]->equipment_lighting_color_id}" {if $equipmentLightingsList[i]->color == $lightingColor[k]->equipment_lighting_color_id} SELECTED {/if}> {$lightingColor[k]->name}</option>										
									{/section}																		
								{else}
									<option value='0'> no filter types </option>
								{/if}
							</select>	
						</td>
					</tr>		
				{/section}	
			{/if}
	</tbody>

	<tfoot>
                <tr class="">
                        <td class="users_u_bottom" height="20">
                            <a href="#" onclick="selectAllLightings(true); return false;">All</a>
                            <a href="#" onclick="selectAllLightings(false);return false;">None</a>
                        </td>
                        <td colspan="6" class="users_u_bottom_r">
                            <a href="#" onclick="clearSelectedLightings(); return false">Remove selected lightings from the list</a>
                        </td>
                </tr>
        </tfoot>

</table>
</div>
	{*BUTTONS*}	
	<div align="right" class="margin5">
		<input type='button' name='cancel' class="button" value='Cancel' 
				{if $request.action eq "addItem"} onClick="location.href='?action=browseCategory&category=department&id={$request.id}&bookmark=equipment'"
				{elseif $request.action eq "edit"} onClick="location.href='?action=browseCategory&category=department&id={$data.department_id}&bookmark=equipment'"
				{/if}
			>
		<input type='button' name='save' class="button" value='Save' onClick="saveEquipmentDetails();">		
	</div>
	
	
	{*HIDDEN*}
	<input type='hidden' name='action' value='{$request.action}'>	
	{if $request.action eq "addItem"}
		<input type='hidden' name='department_id' value='{$request.departmentID}'>
	{/if}	
	{if $request.action eq "edit"}
		<input type="hidden" name="id" value="{$request.id}">
		<input type='hidden' name='department_id' value='{$data.department_id}'>
	{/if}
		
</form>


	<script type='text/javascript'>
	  var dateType='{$data.date_type}';
	  {literal}
	  $(document).ready(function () { 
        
        	//if (dateType=='d-m-Y g:iA')
		//	{
        		//popUpCal.dateFormat = 'DMY-';					 
				$('#calendar1').datepicker({ dateFormat: '{/literal}{$data.expire->getFromTypeController('getFormatForCalendar')}{*dd-mm-yy*}{literal}' }); 
		//	}		
        //    else
		//	{
        //    	 $('#calendar1').datepicker({ dateFormat: 'mm/dd/yy' });            
            	//popUpCal.dateFormat = 'MDY/';
		//	}						    		   	
      });
	  {/literal}
    </script>


</div>

{include file="tpls:tpls/pleaseWait.tpl" text=$pleaseWaitReason}		

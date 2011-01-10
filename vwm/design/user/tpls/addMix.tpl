<div id="notify">	
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

<div style="padding:7px;" >

	<form method='POST' action='{$sendFormAction}'>		
	
		<table class="users" align="center" cellpadding="0" cellspacing="0">
			<tr class="users_u_top_size users_top">
				<td class="users_u_top" width="30%">
					<span >{if $currentOperation eq "addItem"}Adding for a new usage{else}Editing usage{/if}</span>
				</td>
				<td class="users_u_top_r">
					&nbsp;
				</td>				
			</tr>		

			<tr height="">

{*MIXDETAILS*}		
							<td class="border_users_r border_users_l border_users_b" height="20">
								Usage description:
							</td>
							<td class="border_users_r border_users_b">
							<div class="floatleft" >	<input type='text' name='description' value='{$data.description}'></div>
							
							{if $validStatus.summary eq 'false'}
							{if $validStatus.description eq 'failed'}
			     				{*ERROR*}					
                        		<div class="error_img"><span class="error_text">Error!</span></div>
							    {*/ERROR*}
						    {elseif $validStatus.description eq 'alredyExist'}
								<div class="error_img"><span class="error_text">Entered name is already in use!</span></div>
							{/if}
						    {/if}
								
							</td>
							
						</tr>												
						
						<tr>
							<td class="border_users_r border_users_l border_users_b" height="20">
								Exempt Rule:
							</td>
							<td class="border_users_r border_users_b">
							<div align="left" ><input type="text" name="exemptRule" value="{$data.exemptRule}"></div>								
							</td>
						</tr>
																	
						<tr>
							<td class="border_users_l border_users_b border_users_r" height="20">
								Mix Date (mm-dd-yyyy):
							</td>
							<td class="border_users_r border_users_b">
							<div align="left" ><input type="text" name="creationTime" value="{$data.creationTime}">
								{if $validStatus.summary eq 'false'}
								{if $validStatus.creationTime eq 'failed'}
						
								{*ERORR*}
								<div class="error_img"><span class="error_text">Error!</span></div>
								{*/ERORR*}
							{/if}
							{/if}
							</div>								
							</td>
						</tr>
						<tr>
							<td class="border_users_l border_users_b border_users_r" height="20">
								AP method:
							</td>
							<td class="border_users_b border_users_r">
								<div class="floatleft">	
									<select name="selectAPMethod" id="selectAPMethod">
									{section name=i loop=$APMethod}										
											<option value='{$APMethod[i].apmethod_id}' {if $APMethod[i].apmethod_id eq $data.apmethod_id}selected="selected"{/if}> {$APMethod[i].description}</option>										
									{/section}
									</select>									
								</div>								
							</td>
						</tr>
					</table>
{*/MIXDETAILS*}



{*WASTE*}
{if $show.waste_streams === true}
					{include file="tpls:waste_streams/design/wasteStreams.tpl"}
{else}						
					<table class="users" cellpadding="0" cellspacing="0" align="center">
						<tr class="users_u_top_size users_top_lightgray" >
							<td colspan="2">Set waste</td>
						</tr>												
						<tr>
							<td class="border_users_l border_users_b border_users_r" width="30%" height="20">
								Waste value:
							</td>
							<td class="border_users_r border_users_b">
								<div align="left" >											
								<input type="text" id="wasteValue" name="wasteValue" value="{$data.waste.value}">
									{if $validStatus.summary eq 'false'}
									{if $validStatus.waste.value eq 'failed'}
									{*ERORR*}
										<div style="width:680px;margin:2px 0px 0px 5px;" align="left"><img src='design/user/img/alert1.gif' height=16  style="float:left;">
										<font style="float:left;vertical-align:bottom;color:red;margin:1px 0px 0px 5px;">Error! Please enter valid waste value. VOC was calculated with waste = 0. Waste value must be a positive number.</font></div>
									{*/ERORR*}
									{/if}
									{if $validStatus.waste.percent eq 'failed'}
									{*ERORR*}
										<div style="width:680px;margin:2px 0px 0px 5px;" align="left"><img src='design/user/img/alert1.gif' height=16  style="float:left;">
										<font style="float:left;vertical-align:bottom;color:red;margin:1px 0px 0px 5px;">Error! Please enter valid waste value. VOC was calculated with waste = 0. Waste value must be less than products total value.</font></div>
									{*/ERORR*}
									{/if}
									{if $validStatus.waste.convert eq 'failed'}
									{*ERORR*}
										<div style="width:680px;margin:2px 0px 0px 5px;" align="left"><img src='design/user/img/alert1.gif' height=16  style="float:left;">
										<font style="float:left;vertical-align:bottom;color:red;margin:1px 0px 0px 5px;">Error! Can't calculate waste for mix. Please enter valid waste value in % or set density for all products used in mix. VOC was calculated with waste = 0.</font></div>
									{*/ERORR*}
									{/if}	
									{/if}
								</div>								
							</td>
						</tr>
						<tr>
							<td class="border_users_l border_users_b border_users_r" height="20">
								Waste unit type :
							</td>

							<td class="border_users_r border_users_b">
								<div class="floatleft">	
									<select name="selectWasteUnittypeClass" id="selectWasteUnittypeClass" onchange="getUnittypes(document.getElementById('selectWasteUnittypeClass'), {$companyID}, {$companyEx})" >									 										
										{section name=j loop=$typeEx}
										{if 'USALiquid' eq $typeEx[j]}<option value='USALiquid' {if 'USALiquid' eq $data.waste.unittypeClass}selected="selected"{/if}>USA liquid</option>{/if}
										{if 'USADry' eq $typeEx[j]}<option value='USADry' {if 'USADry' eq $data.waste.unittypeClass}selected="selected"{/if}>USA dry</option>{/if}
										{if 'USAWght' eq $typeEx[j]}<option value='USAWght' {if 'USAWght' eq $data.waste.unittypeClass}selected="selected"{/if}>USA weight</option>{/if}										
										{if 'MetricVlm' eq $typeEx[j]}<option value='MetricVlm' {if 'MetricVlm' eq $data.waste.unittypeClass}selected="selected"{/if}>Metric volume</option>{/if}
										{if 'MetricWght' eq $typeEx[j]}<option value='MetricWght' {if 'MetricWght' eq $data.waste.unittypeClass}selected="selected"{/if}>Metric weight</option>{/if}		
										{/section}
										<option value='percent' {if 'percent' eq $data.waste.unittypeClass}selected="selected"{/if}>%</option>										
									</select>
									<input type="hidden" id="company" value="{$companyID}">
									<input type="hidden" id="companyEx" value="{$companyEx}">
								</div>
								<div class="floatleft padd_left">	
									<select name="selectWasteUnittype" id="selectWasteUnittype" >									
										{section name=i loop=$data.waste.unitTypeList}	
											<option value='{$data.waste.unitTypeList[i].unittype_id}' {if $data.waste.unitTypeList[i].unittype_id eq $data.waste.unittypeID}selected="selected"{/if}>{$data.waste.unitTypeList[i].description}</option>										
										{/section}									
									</select>									
								</div>
								
								{*ajax-preloader*}
								<div id="selectWasteUnittypePreloader" class="floatleft padd_left" style="display:none">
									<img src='images/ajax-loader.gif' height=16  style="float:left;">
								</div>
							</td>
						</tr>		
					</table>
{/if}
{*/WASTE*}

{*ADDPRODUCT*}
										
					
					
					<table class="users" cellpadding="0" cellspacing="0" align="center">
						<tr class="users_u_top_size users_top_lightgray" >
							<td colspan="2">Add product</td>
						</tr>
						
						<tr>
							<td class="border_users_l border_users_b border_users_r" width="30%">
								Equipment :
							</td>
							<td class="border_users_r border_users_b">								
								<div class="floatleft">	
								
								<select name="selectEquipment" id="selectEquipment">
									{section name=i loop=$equipment}										
										<option value='{$equipment[i].equipment_id}' {if $equipment[i].equipment_id eq $data.equipment_id}selected="selected"{/if}> {$equipment[i].equip_desc} </option>
									{/section}
								</select>							
									
								</div>
								
								<div class="floatleft padd_left">									
								<select name="rule" id="rule">
									{section name=i loop=$rules}
										<option value='{$rules[i].rule_id}' {if $rules[i].rule_id eq $data.rule}selected="selected"{/if}> {$rules[i].rule_nr} - {$rules[i].rule_desc}</option>
									{/section}
								</select>									
								</div>
									{if $validStatus.summary eq 'false'}
									{if $validStatus.equipment eq 'noEquipment'}
									{*ERORR*}										
										<div style="width:80px;margin:2px 0px 0px 5px;" align="left"><img src='design/user/img/alert1.gif' height=16  style="float:left;">
										<font style="float:left;vertical-align:bottom;color:red;margin:1px 0px 0px 5px;">Error!</font></div>
									{*/ERORR*}
									{/if}
									{/if}								
							</td>
						</tr>
													
							
						<tr>
							<td class="border_users_l border_users_b border_users_r">
								Product :
							</td>
							<td class="border_users_r border_users_b">
								<div class="floatleft">	
								
							{*	<select name="selectProduct" id="selectProduct" class="addInventory">
									{if $products}
										{section name=i loop=$products}
											{assign var=isAdded value=0}
											{section name=j loop=$productsAdded} 
		                            			{if $productsAdded[j].product_id eq $products[i].product_id}
		                            				{assign var=isAdded value=1}
		                            			{/if}
		                            		{/section}
                            				{if $isAdded eq 0}
											<option value='{$products[i].product_id}' {if $products[i].product_id eq $data.product_id}selected="selected"{/if}> {$products[i].formattedProduct} </option>
											{/if}
										{/section}
									{else}
										<option value='0'> no products </option>
									{/if}
								</select>*}
								
								{*NICE PRODUCT LIST*}	
								<select name="selectProduct" id="selectProduct" class="addInventory">
									{if $products}				
										{foreach from=$products item=productsArr key=supplier}															
										<optgroup label="{$supplier}">
											{section name=i loop=$productsArr}
												{assign var=isAdded value=0}
												{section name=j loop=$productsAdded} 
		                            				{if $productsAdded[j].product_id eq $productsArr[i].product_id}
		                            					{assign var=isAdded value=1}
		                            				{/if}
		                            			{/section}
                            					{if $isAdded eq 0}                            				
													<option value='{$productsArr[i].product_id}' {if $productsArr[i].product_id eq $data.product_id}selected="selected"{/if}> {$productsArr[i].formattedProduct} </option>
												{/if}
											{/section}
										</optgroup>
										{/foreach}																			
									{else}
										<option value='0'> no products </option>
									{/if}
								</select>	
								</div>
								{if $validStatus.summary eq 'false'}
								{if $validStatus.products eq 'noProducts'}
									{*ERORR*}										
										<div class="error_img"><span class="error_text">No products in the mix!</span></div>
									{*/ERORR*}
								{/if}
								{/if}
							</td>
						</tr>
						
						<tr>
							<td class="border_users_l border_users_b border_users_r">
								Quantity :
							</td>
							<td class="border_users_r border_users_b">
							<div class="floatleft" ><input type='text' id="quantity" name='quantity' value='{$data.quantity}'></div>
							
							{if $validStatus.summary eq 'false'}
							{if $validStatus.quantity eq 'failed'}
						
								{*ERORR*}								
									<div class="error_img"><span class="error_text">Error!</span></div>
								{*/ERORR*}
							{/if}
							{/if}
						
							</td>
						</tr>
						
						<tr>
							<td class="border_users_l border_users_b border_users_r">
								Unit type :
							</td>
							<td class="border_users_r border_users_b">
								<div class="floatleft">	
									<select name="selectUnittypeClass" id="selectUnittypeClass" onchange="getUnittypes(this, {$companyID}, {$companyEx})">									 										
										{section name=j loop=$typeEx}
											{if 'USALiquid' eq $typeEx[j]}<option value='USALiquid' {if 'USALiquid' eq $data.unitTypeClass}selected="selected"{/if}>USA liquid</option>{/if}
											{if 'USADry' eq $typeEx[j]}<option value='USADry' {if 'USADry' eq $data.unitTypeClass}selected="selected"{/if}>USA dry</option>{/if}
											{if 'USAWght' eq $typeEx[j]}<option value='USAWght' {if 'USAWght' eq $data.unitTypeClass}selected="selected"{/if}>USA weight</option>{/if}										
											{if 'MetricVlm' eq $typeEx[j]}<option value='MetricVlm' {if 'MetricVlm' eq $data.unitTypeClass}selected="selected"{/if}>Metric volume</option>{/if}
											{if 'MetricWght' eq $typeEx[j]}<option value='MetricWght' {if 'MetricWght' eq $data.unitTypeClass}selected="selected"{/if}>Metric weight</option>{/if}		
										{/section}
									</select>
								</div>
								<div class="floatleft padd_left">	
									<select name="selectUnittype" id="selectUnittype">
									{section name=i loop=$unittype}										
											<option value='{$unittype[i].unittype_id}' {if $unittype[i].unittype_id eq $data.unittype}selected="selected"{/if}> {$unittype[i].description}</option>										
									{/section}
									</select>
								</div>
								
								{if $validStatus.summary eq 'false'}
								{if $validStatus.conflict eq 'density2volume'}
								{*ERORR*}										
									<div class="error_img"><span class="error_text">Failed to convert weight unit to volume because product density is underfined! You can set density for this product or use volume units.</span></div>
								{*/ERORR*}
								{elseif $validStatus.conflict eq 'density2weight'}
								{*ERORR*}										
									<div class="error_img"><span class="error_text">Failed to convert volume unit to weight because product density is underfined! You can set density for this product or use weight units.</span></div>
								{*/ERORR*}								
								{/if}
								{if $validStatus.description eq 'weight2volumeConflict'}			
								{*ERORR*}
									<div class="error_img"><span class="error_text">Failed to convert weight unit to volume because product density is underfined! You can set density for this product or use volume units.</span></div>
								{*/ERORR*}
								{/if}
								{if $validStatus.description eq 'volume2weightConflict'}			
								{*ERORR*}
									<div class="error_img"><span class="error_text">Failed to convert volume unit to weight because product density is underfined! You can set density for this product or use weight units.</span></div>
								{*/ERORR*}
								{/if}
								{if $validStatus.description eq 'wasteCalc'}			
								{*ERORR*}
									<div class="error_img"><span class="error_text">Failed to calculate waste for mix because product density is underfined! You can set density for this product or use weight units. You can set waste value in %.</span></div>
								{*/ERORR*}
								{/if}
								{/if}
								
								{*ajax-preloader*}
								<div id="selectUnittypePreloader" class="floatleft padd_left" style="display:none">
									<img src='images/ajax-loader.gif' height=16  style="float:left;">
								</div>
								
							</td>
						</tr>
						
						<tr>
							<td class="border_users_l border_users_b border_users_r">
								Product description :
							</td>
							<td class="border_users_r border_users_b">
							<div class="floatleft">	<input type='text' id='product_desc' value='{$data.product_desc}' readonly></div>							
							{*ajax-preloader*}
							<div id="product_descPreloader" class="floatleft padd_left" style="display:none">
								<img src='images/ajax-loader.gif' height=16  style="float:left;">
							</div>
							</td>
						</tr>
						
						<tr>
							<td class="border_users_l border_users_b border_users_r">
								Coating type :
							</td>
							<td class="border_users_r border_users_b">
							<div class="floatleft">	<input type='text' id='coating' value='{$data.coating}' readonly></div>
							{*ajax-preloader*}
							<div id="coatingPreloader" class="floatleft padd_left" style="display:none">
								<img src='images/ajax-loader.gif' height=16  style="float:left;">
							</div>							
							</td>
						</tr>
					</table>
{*/ADDPRODUCT*}

{*MIXLIMITS*}					
					<table class="users"  width="100%" cellpadding="0" cellspacing="0" align="center">
						<tr class="users_u_top_size users_top_lightgray" >
							<td colspan="2">Emissions</td>
						</tr>
						<tr>
							<td class="border_users_l border_users_b border_users_r" height="20" width="30%">
								VOC:
							</td>
							<td class="border_users_r border_users_b">
							<div align="left" >{$data.voc}</div>
								<input type="hidden" name="voc" value="{$data.voc}">
							</td>
						</tr>
						
						<tr>
							<td class="border_users_l border_users_b border_users_r" height="20">
								VOCLX:
							</td>
							<td class="border_users_r border_users_b">
							<div align="left" >{$data.voclx}</div>
								<input type="hidden" name="voclx" value="{$data.voclx}">
							</td>
						</tr>
						
						<tr>
							<td class="border_users_l border_users_b border_users_r" height="20">
								VOCWX:
							</td>
							<td class="border_users_r border_users_b">
							<div align="left" >{$data.vocwx}</div>
								<input type="hidden" name="vocwx" value="{$data.vocwx}">
							</td>
						</tr>
						<tr>													
							<td class="border_users_l border_users_b border_users_r" height="20">
								Daily limit exceeded:
							</td>
							<td class="border_users_r border_users_b">
								<div align="left">
									{if $dailyLimitExceeded == true}<b>YES!!!</b>{else}no{/if} 	
								</div>
								
							</td>
						</tr>
						
						<tr>
							<td class="border_users_l border_users_b border_users_r" height="20">
								Department limit exceeded:
							</td>
							<td class="border_users_r border_users_b">
								<div align="left">
									{if $departmentLimitExceeded == true}<b>YES!!!</b>{else}no{/if} 	
								</div>
								
							</td>
						</tr>
						
						<tr>
							<td class="border_users_l border_users_b border_users_r" height="20">
								Facility limit exceeded:
							</td>
							<td class="border_users_r border_users_b">
								<div align="left">
									{if $facilityLimitExceeded == true}<b>YES!!!</b>{else}no{/if} 	
								</div>
								
							</td>
						</tr>																												
						
						<tr>
							<td class="border_users_l border_users_b border_users_r" height="20">
								Facility annual limit exceeded:
							</td>
							<td class="border_users_r border_users_b">
								<div align="left">
									{if $facilityAnnualLimitExceeded == true}<b>YES!!!</b>{else}no{/if} 	
								</div>
								
							</td>
						</tr>
						<tr>
							<td class="border_users_l border_users_b border_users_r" height="20">
								Department annual limit exceeded:
							</td>
							<td class="border_users_r border_users_b">
								<div align="left">
									{if $departmentAnnualLimitExceeded == true}<b>YES!!!</b>{else}no{/if} 	
								</div>
								
							</td>
						</tr>
			</table>
{*/MIXLIMITS*}		
			
		<div align="right" class="buttonpadd">									
		<img id="addProductPreloader" src='images/ajax-loader.gif' height=16  style="display:none;float:left;">								
		<input type='button' name='cancel' class="button" value='Cancel'>
		<input type='button' name='addProduct' class="button" value='Add product to list' onclick='addProduct2List();'>
		<input type='submit' name='save' class="button" value='Save'>
		</div>
		
				
<div class="padd7">
<table class="users" align="center" cellspacing="0" cellpadding="0" id="addedProducts">
	<thead>
	<tr class="users_u_top_size users_top_lightgray">
		<td  class="border_users_l"   width="10%" > Select</td>
		<td>Supplier</td>
		<td>Product NR</td>
		<td>Description</td>
		<td>Quantity</td>
		<td class="border_users_r">Unit type</td>
	</tr>		
	</thead>
	
	<tbody>
		
	</tbody>
	
	<tfoot>
	<tr class="">
			<td class="users_u_bottom" height="20"></td><td colspan="5" class="users_u_bottom_r"></td>
	</tr>
	</tfoot>
																		
</table>			
				
		
		
		{if $request.action eq "addItem"}
			{section name=i loop=$productCount}			
			<input type='hidden' name='quantity_{$smarty.section.i.index}' value='{$productsAdded[i].quantity}'>
			<input type='hidden' name='unittype_{$smarty.section.i.index}' value='{$productsAdded[i].unittype}'>
			{/section}
		{/if}		
			<input type='hidden' name='productCount' value='{$productCount}'>									
		{if $request.action eq "addItem"}
			<input type='hidden' name='department_id' value='{$request.departmentID}'>
		{/if}	
		{if $request.action eq "edit"}
			<input type="hidden" name="id" value="{$request.id}">
		{/if}
		</form>
</div>


<textarea id="dock" style="display:none;">
	
	<tr class="" height="10px">
		<td class="border_users_r border_users_b border_users_l">
			<input type="checkbox" checked="checked" value="" name="product[]">
		</td>
			
		<td class="border_users_r border_users_b" width="10%">
			<div style="width:100%;" name="supplier"></div >
        </td>

		<td class="border_users_r border_users_b" width="10%">        
			<div style="width:100%;" name="productNR"></div>	
		</td>
			
		<td class="border_users_r border_users_b" width="15%">
			<div style="width:100%;" name="description"></div >
        </td>

		<td class="border_users_r border_users_b" width="10%">
			<div style="width:100%;">			            
				<input type='text' name='quantityOfAddedProduct' value=''>							
			</div >
		</td>

		<td class="border_users_r border_users_b">	
			<div style="width:100%;" name="unittypeOfAddedProduct">																						
			</div>			
		</td>
	</tr>
</textarea>
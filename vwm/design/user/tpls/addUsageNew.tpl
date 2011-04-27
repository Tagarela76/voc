	{if $color eq "green"}
		{include file="tpls:tpls/notify/greenNotify.tpl" text=$message}
	{/if}
	{if $color eq "orange"}
		{include file="tpls:tpls/notify/orangeNotify.tpl" text=$message}
	{/if}
	{if $color eq "blue"}
		{include file="tpls:tpls/notify/blueNotify.tpl" text=$message}
	{/if}
	
<script type="text/javascript">

var companyId="{$companyID}";
var companyEx="{$companyEx}";

{if $show.waste_streams != true}



var waste = new Object();

var noMWS = true;
{else}
var noMWS = false;
{/if}

{if $smarty.request.departmentID}
var departmentID = {$smarty.request.departmentID};
{else}
var departmentID = {$data->department_id};
{/if}

var unittypes = new Array();

{section name=i loop=$unittype}	
	un = new Array({$unittype[i].unittype_id},'{$unittype[i].description}');
	unittypes.push(un);									
{/section}

{if $smarty.request.action == 'edit'}
var editForm = true;
var mixID = '{$smarty.request.id}';
var mixDescription = '{$data->description}';
{else}
var editForm = false;
{/if}

{literal}
$(function()
{
{/literal}
	//Products load
	
	{foreach from=$data->products item=p}
		//products.addProduct({$p->product_id}, {$p->quantity}, {$p->unit_type});
		addProduct({$p->product_id}, {$p->quantity}, {$p->unit_type}, '{$p->unittypeDetails.unittypeClass}');
		
	{/foreach}
		
{literal}	


}
);

function createSelectUnittypeClass(id) {
	sel = $("<select>").attr("id",id);

	{/literal}
	
	//sel.attr('onchange','getUnittypes(this, {$companyID}, {$companyEx}); checkUnittypeWeightWarning();');

	{section name=j loop=$typeEx}
		{if 'USALiquid' eq $typeEx[j]}sel.append("<option value='USALiquid' {if 'USALiquid' eq $data->waste->unitTypeClass}selected='selected'{/if}>USA liquid</option>");{/if}
		{if 'USADry' eq $typeEx[j]}sel.append("<option value='USADry' {if 'USADry' eq $data->waste->unitTypeClass}selected='selected'{/if}>USA dry</option>");{/if}
		{if 'USAWght' eq $typeEx[j]}sel.append("<option value='USAWght' {if 'USAWght' eq $data->waste->unitTypeClass}selected='selected'{/if}>USA weight</option>");{/if}										
		{if 'MetricVlm' eq $typeEx[j]}sel.append("<option value='MetricVlm' {if 'MetricVlm' eq $data->waste->waste->unitTypeClass}selected='selected'{/if}>Metric volume</option>");{/if}
		{if 'MetricWght' eq $typeEx[j]}sel.append("<option value='MetricWght' {if 'MetricWght' eq $data->waste->unitTypeClass}selected='selected'{/if}>Metric weight</option>");{/if}		
	{/section}
	{literal}

	return sel;
}
{/literal}
</script>
<script type="text/javascript" src="modules/js/jquery-ui-1.8.2.custom/jquery-plugins/numeric/jquery.numeric.js"></script>
<script type="text/javascript" src="modules/js/jquery-ui-1.8.2.custom/jquery-plugins/json/jquery.json-2.2.min.js"></script>

	


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
							<div class="floatleft" >	
								<input type='text' id="mixDescription" name='description' value='{$data->description}'></div>
								<div class="error_img"  id="mixDescriptionErrorAlreadyInUse" style="display:none;"><span class="error_text" >Entered name is already in use!</span></div>
								<div class="error_img"  id="mixDescriptionError" style="display:none;"><span class="error_text" >Error!</span></div>
							</td>
							
						</tr>												
						
						<tr>
							<td class="border_users_r border_users_l border_users_b" height="20">
								Exempt Rule: (not necessary)
							</td>
							<td class="border_users_r border_users_b">
							<div align="left" ><input type="text" name="exemptRule" value="{$data->exempt_rule}" id="exemptRule"></div>								
							</td>
						</tr>
																	
						<tr>
							<td class="border_users_l border_users_b border_users_r" height="20">
								Mix Date (mm-dd-yyyy):
							</td>
							<td class="border_users_r border_users_b">
							<div align="left" ><input type="text" name="creationTime" id="calendar1" value="{$data->creation_time}">
							<div id="creationTimeError" style="display:none;" class="error_img"><span class="error_text">Error!</span></div>
								{if $validStatus.summary eq 'false'}
								{if $validStatus.creationTime eq 'failed'}
						
								{*ERORR*}
								<div class="error_img"><span class="error_text">Error!</span></div>
								{*/ERORR*}
							{/if}
							{/if}
							</div>
							{literal}
							<script type="text/javascript">
							    function clearInputBox(item){
							        item.value = "";
							    }
							    
							    $(document).ready(function(){	
									 $('#calendar1').datepicker({ dateFormat: 'mm-dd-yy' }); 
							    });
							   
							</script>
							{/literal}								
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
											<option value='{$APMethod[i].apmethod_id}' {if $APMethod[i].apmethod_id eq $data->apmethod_id}selected="selected"{/if}> {$APMethod[i].description}</option>										
									{/section}
									</select>									
								</div>								
							</td>
						</tr>
						
						<tr>
							<td class="border_users_l border_users_b border_users_r" height="20">
								Equipment :
							</td>
							<td class="border_users_r border_users_b">								
								<div class="floatleft">	
								<select name="selectEquipment" id="selectEquipment">
									{section name=i loop=$equipment}										
										<option value='{$equipment[i].equipment_id}' {if $equipment[i].equipment_id eq $data->equipment}selected="selected"{/if}> {$equipment[i].equip_desc} </option>
									{/section}
								</select>							
									
								</div>
								
								<div class="floatleft padd_left">									
								<select name="rule" id="rule">
									{section name=i loop=$rules}
										<option value='{$rules[i].rule_id}' {if $rules[i].rule_id eq $data->rule_id}selected="selected"{/if}> {$rules[i].rule_nr} - {$rules[i].rule_desc}</option>
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
					</table>
{*/MIXDETAILS*}



{*WASTE*}
{if $show.waste_streams === true}
					{include file="tpls:waste_streams/design/wasteStreams.tpl"}
{else}				
<!--   <a id="generateMix" href="#" onclick="generateLink(); return false;">Generate Link</a> --> 
<a id="addMix" href="" style="display:none;" target="_blank">Add Mix</a>

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
								<input type="text" id="wasteValue" name="wasteValue" value="{$data->waste.value}">
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
										{if 'USALiquid' eq $typeEx[j]}<option value='USALiquid' {if 'USALiquid' eq $data->waste.unittypeClass}selected="selected"{/if}>USA liquid</option>{/if}
										{if 'USADry' eq $typeEx[j]}<option value='USADry' {if 'USADry' eq $data->waste.unittypeClass}selected="selected"{/if}>USA dry</option>{/if}
										{if 'USAWght' eq $typeEx[j]}<option value='USAWght' {if 'USAWght' eq $data->waste.unittypeClass}selected="selected"{/if}>USA weight</option>{/if}										
										{if 'MetricVlm' eq $typeEx[j]}<option value='MetricVlm' {if 'MetricVlm' eq $data->waste.unittypeClass}selected="selected"{/if}>Metric volume</option>{/if}
										{if 'MetricWght' eq $typeEx[j]}<option value='MetricWght' {if 'MetricWght' eq $data->waste.unittypeClass}selected="selected"{/if}>Metric weight</option>{/if}		
										{/section}
										<!-- 'percent' eq $data->waste.unittypeClass or  -->
										<option value='percent' {if $data->waste.unittypeClass == '%'}selected="selected"{/if}>%</option>										
									</select>
									<input type="hidden" id="company" value="{$companyID}">
									<input type="hidden" id="companyEx" value="{$companyEx}">
								</div>
								<div class="floatleft padd_left">	
									<select name="selectWasteUnittype" id="selectWasteUnittype" >									
										{section name=i loop=$data->waste.unitTypeList}	
											<option value='{$data->waste.unitTypeList[i].unittype_id}' {if $data->waste.unitTypeList[i].unittype_id eq $data->waste.unittypeID}selected="selected"{/if}>{$data->waste.unitTypeList[i].description}</option>										
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
										
	
					
					<table class="users" cellpadding="0" cellspacing="0" align="center" >
						<tr class="users_u_top_size users_top_lightgray" >
							<td colspan="2">Add product</td>
						</tr>												
													
							
						<tr>
							<td class="border_users_l border_users_b border_users_r" width="30%">
								Product :
							</td>
							<td class="border_users_r border_users_b">
								<div class="floatleft">	
								
								{*NICE PRODUCT LIST*}	
								<select name="selectProduct" id="selectProduct" class="addInventory">
									<!-- <option selected="selected" >Select Product</option> -->
									{if $products}				
										{foreach from=$products item=productsArr key=supplier}															
										<optgroup label="{$supplier}">
											{section name=i loop=$productsArr}
												{assign var=isAdded value=0}
												{section name=j loop=$productsAdded} 
		                            				{if $productsAdded[j]->product_id eq $productsArr[i].product_id}
		                            					{assign var=isAdded value=1}
		                            				{/if}
		                            			{/section}
                            					{if $isAdded eq 0}                            				
													<option value='{$productsArr[i].product_id}' {if $productsArr[i].product_id eq $data->product_id}selected="selected"{/if}> {$productsArr[i].formattedProduct} </option>
												{else}
													<option value='{$productsArr[i].product_id}' disabled="disabled"> {$productsArr[i].formattedProduct} </option>
												{/if}
											{/section}
										</optgroup>
										{/foreach}																			
									{else}
										<option value='0'> no products </option>
									{/if}
								</select>
								{*NICE PRODUCT LIST*}
										
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
							<div class="floatleft" ><input id="quantity" type='text' name='quantity' value='{$data->quantity}'></div>
							<script type="text/javascript">
										$("#quantity").numeric();
							</script>
							
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
									<select name="selectUnittypeClass" id="selectUnittypeClass" onchange="getUnittypes(this, {$companyID}, {$companyEx}); checkUnittypeWeightWarning();">	
															 										
										{section name=j loop=$typeEx}
											{if 'USALiquid' eq $typeEx[j]}<option value='USALiquid' {if 'USALiquid' eq $data->waste->unitTypeClass}selected="selected"{/if}>USA liquid</option>{/if}
											{if 'USADry' eq $typeEx[j]}<option value='USADry' {if 'USADry' eq $data->waste->unitTypeClass}selected="selected"{/if}>USA dry</option>{/if}
											{if 'USAWght' eq $typeEx[j]}<option value='USAWght' {if 'USAWght' eq $data->waste->unitTypeClass}selected="selected"{/if}>USA weight</option>{/if}										
											{if 'MetricVlm' eq $typeEx[j]}<option value='MetricVlm' {if 'MetricVlm' eq $data->waste->waste->unitTypeClass}selected="selected"{/if}>Metric volume</option>{/if}
											{if 'MetricWght' eq $typeEx[j]}<option value='MetricWght' {if 'MetricWght' eq $data->waste->unitTypeClass}selected="selected"{/if}>Metric weight</option>{/if}		
										{/section}
									</select>
								</div>
								<div class="floatleft padd_left">
									<select name="selectUnittype" id="selectUnittype" onchange="checkUnittypeWeightWarning();">
									
									{section name=i loop=$unittype}										
											<option value='{$unittype[i].unittype_id}' {if $unittype[i].unittype_id eq $data->waste->unittypeID}selected="selected"{/if}> {$unittype[i].description}</option>										
									{/section}
									</select>
								</div>
								<div class="error_img" id="errorProductWeight" style="display:none;"><span class="error_text">Failed to convert weight unit to volume because product density is underfined! You can set density for this product or use volume units.</span></div>
								
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
							<div class="floatleft"> <!-- 	<input type='text' id='product_desc' value='{$data->description}' readonly> -->
								<span id="product_desc"></span>
							</div>							
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
							<div class="floatleft"> <!-- 	<input type='text' id='coating' value='{$data->coating}' readonly> -->
								<span id="coating"></span>
							</div>
							{*ajax-preloader*}
							<div id="coatingPreloader" class="floatleft padd_left" style="display:none">
								<img src='images/ajax-loader.gif' height=16  style="float:left;">
							</div>							
							</td>
						</tr>
						<tr>
							<td class="border_users_l border_users_b border_users_r">
								&nbsp;<div class="error_img" id="errorAddProduct" style="display:none;"><span class="error_text"></span></div>
							</td>
							<td class="border_users_r border_users_b">
								<div align="left" class="buttonpadd">
									<input type='button' class="button" value='Add product to list' onclick="addProduct2List()">
									
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
							<div align="left" id="VOC">{$data->voc}</div>
								<input type="hidden" name="voc" value="{$data->voc}">
							</td>
						</tr>
						{*
						<tr>
							<td class="border_users_l border_users_b border_users_r" height="20">
								VOCLX:
							</td>
							<td class="border_users_r border_users_b">
							<div align="left" >{$data->voclx}</div>
								<input type="hidden" name="voclx" value="{$data->voclx}">
							</td>
						</tr>
						
						<tr>
							<td class="border_users_l border_users_b border_users_r" height="20">
								VOCWX:
							</td>
							<td class="border_users_r border_users_b">
							<div align="left" >{$data->vocwx}</div>
								<input type="hidden" name="vocwx" value="{$data->vocwx}">
							</td>
						</tr>*}
						<tr>													
							<td class="border_users_l border_users_b border_users_r" height="20">
								Daily limit exceeded:
							</td>
							<td class="border_users_r border_users_b">
								<div align="left" id="dailyLimitExceeded">
									{if $dailyLimitExceeded == true}<b>YES!!!</b>{else}no{/if} 	
								</div>
								
							</td>
						</tr>
						
						<tr>
							<td class="border_users_l border_users_b border_users_r" height="20">
								Department limit exceeded:
							</td>
							<td class="border_users_r border_users_b">
								<div align="left" id="departmentLimitExceeded">
									{if $departmentLimitExceeded == true}<b>YES!!!</b>{else}no{/if} 	
								</div>
								
							</td>
						</tr>
						
						<tr>
							<td class="border_users_l border_users_b border_users_r" height="20">
								Facility limit exceeded:
							</td>
							<td class="border_users_r border_users_b">
								<div align="left" id="facilityLimitExceeded">
									{if $facilityLimitExceeded == true}<b>YES!!!</b>{else}no{/if} 	
								</div>
								
							</td>
						</tr>																												
						
						<tr>
							<td class="border_users_l border_users_b border_users_r" height="20">
								Facility annual limit exceeded:
							</td>
							<td class="border_users_r border_users_b">
								<div align="left" id="facilityAnnualLimitExceeded">
									{if $facilityAnnualLimitExceeded == true}<b>YES!!!</b>{else}no{/if} 	
								</div>
								
							</td>
						</tr>
						<tr>
							<td class="border_users_l border_users_b border_users_r" height="20">
								Department annual limit exceeded:
							</td>
							<td class="border_users_r border_users_b">
								<div align="left" id="departmentAnnualLimitExceeded">
									{if $departmentAnnualLimitExceeded == true}<b>YES!!!</b>{else}no{/if} 	
								</div>
								
							</td>
						</tr>
			</table>
{*/MIXLIMITS*}		
			
		<div align="right" class="buttonpadd">
		<!--  <input type='submit' name='save' class="button" value='Add product to list'>-->
		{if $request.action eq "edit"}
			<input type='button' id="btnSave" name='save' class="button" value='Save' onclick="addMix()"/>
		{else}
			<input type='button' id="btnSave" name='save' class="button" value='Add' onclick="addMix()"/>
		{/if}
		
		</div>
		
		
	
<div class="padd7" style="display:none;" id="addProductsContainer">
<table class="users" align="center" cellspacing="0" cellpadding="0" id="addedProducts" >
<tbody>
	<tr class="users_u_top_size users_top_lightgray">
		<td  class="border_users_l"   width="10%" > Select</td>
		<td>Supplier</td>
		<td>Product NR</td>
		<td>Description</td>
		<td>Quantity</td>
		<td class="border_users_r">Unit type</td>
	</tr>	
</tbody>	
<tfoot>
	<tr class="">
		<td class="users_u_bottom" height="20">Select:
			<a href="#" onclick="selectAllProducts(true); return false;">All</a>
			<a href="#" onclick="selectAllProducts(false);return false;">None</a>
		</td>
		<td colspan="6" class="users_u_bottom_r">
			
			<a href="#" onclick="clearSelectedProducts(); return false">Remove selected products from the list</a>
			{if $debug}
			<a href="#" onclick="alert(products.toJson()); return false;">Display Products</a>
			{/if}
		</td>
	</tr>
</tfoot>														
</table>			
		
	
		
		
		{if $request.action eq "addItem"}
			{section name=i loop=$productCount}			
			<input type='hidden' name='quantity_{$smarty.section.i.index}' value='{$productsAdded[i]->quantity}'>
			<input type='hidden' name='unittype_{$smarty.section.i.index}' value='{$productsAdded[i]->unittype}'>
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
{if $validStatus.summary eq 'true'}
	{literal}
		<script type="text/javascript">
		//window.document.onload = getUnittypes(document.getElementById('selectWasteUnittypeClass'), document.getElementById('company').value, document.getElementById('companyEx').value);
				window.document.onload = getUnittypes(document.getElementById('selectUnittypeClass'), document.getElementById('company').value, document.getElementById('companyEx').value);
	
		//		var count = document.getElementById('productCount').value;
		//		for(i=0; i<count; i++) {
		//			window.document.onload = getUnittypes(document.getElementById('selectUnittypeClass_'+i), document.getElementById('company').value, document.getElementById('companyEx').value);
		//		}
		</script>
	{/literal}
{/if}
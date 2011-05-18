
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

<script type="text/javascript">
{if $edit}
var edit = true;

{literal}
$(function() {
	/*Add smarty products to list via JS*/
	{/literal}
	{foreach from=$pfp->products item=product}
	
		addProduct({$product->product_id},{$product->getRatio()});
	
	{/foreach}
	{literal}
});
{/literal}

var pfp_descr = '{$pfp->getDescription()}';
{else}
var edit = false;
var pfp_descr = "";
{/if}

</script>

<div style="padding:7px;">

	<form id="addPFPForm" name="addPFPForm" action='{$sendFormAction}' method="post">		
        
		<table class="users" align="center" cellpadding="0" cellspacing="0">
			<tr class="users_header_orange">
				<td height="30" width="30%">
				
					<div class="users_header_orange_l"><div><span >{if $smarty.request.action eq "addPFPItem"}Adding for a new pre formulated products{else}Editing pre formulated products{/if}</span></div></div>
				</td>
				<td>
					<div class="users_header_orange_r"><div>&nbsp;</div></div>				
				</td>								
			</tr>				
						
			<tr>
				<td class="border_users_l border_users_b" width="15%" height="20">
					Description
				</td>
				<td class="border_users_l border_users_b border_users_r">
					<div align="left" >	
						<input type='text' name='pfp_description' id="pfp_description" value='{if $edit}{$pfp->getDescription()}{/if}'>
					</div>												
					<div id="descr_error" class="error_img" style="display:none;">
						<span class="error_text">Entered name is already in use!</span>
					</div>											
				</td>
			</tr>
			<!--  <tr>
              	 <td height="20" class="users_u_bottom">
                 </td>
                 <td height="20" class="users_u_bottom_r">
                 </td>
            </tr>-->
		
		</table>
		
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
								
								<input type="button" value="Add product to list" id="addProduct" />
								
								<div id="products_error" class="error_img" style="display:none;">
									<span class="error_text"></span>
								</div>	
							</td>
						</tr>
						<tr>
							<td class="border_users_l border_users_b border_users_r" >
								Ratio:
							</td>
							<td class="border_users_r border_users_b">
								<input type="text" name="ratio" id="ratio" value="1" />
							</td>
						</tr>
					</table>

{*ADDPRODUCTS*}					
<div class="padd7" id="addProductsContainer">
<table class="users" align="center" cellspacing="0" cellpadding="0" id="addedProducts" >
<tbody>
	<tr class="users_u_top_size users_top_lightgray">
		<td  class="border_users_l"   width="10%" > Select</td>
		<td>Supplier</td>
		<td>Product NR</td>
		<td>Description</td>
		<td class="border_users_r">Ratio</td>
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
			<input id="productCount" type='hidden' name='productCount' value='{if $productCount}{$productCount}{else}0{/if}'>									
		{if $request.action eq "addPFPItem"}
			<input type='hidden' name='department_id' id="department_id" value='{$request.departmentID}'>
		{/if}	
		{if $request.action eq "editPFP"}
			<input type="hidden" name="id" value="{$request.id}">
			<input type='hidden' id='department_id' name='department_id' value='{$request.departmentID}'>
		{/if}
		

</div>
{*ADDPRODUCTS*}

	{*BUTTONS*}	
	<div align="right" class="margin5">
		<input type='button' name='cancel' class="button" value='Cancel' 
				{if $request.action eq "addItem"} onClick="location.href='?action=browseCategory&category=department&id={$request.id}&bookmark=accessory'"
				{elseif $request.action eq "edit"} onClick="location.href='?action=browseCategory&category=department&id={$request.departmentID}&bookmark=accessory'"
				{/if}
		>
		<input type='button' name='save' id="save" class="button" value='Save'>		
	</div>
	
	
	{*HIDDEN*}
	<input type='hidden' name='action' value='{$request.action}'>	
	{if $request.action eq "addItem"}
		<input type='hidden' id='department_id' name='department_id' value='{$request.id}'>
	{/if}	
	{if $request.action eq "edit"}
		<input type='hidden' id='department_id' name='department_id' value='{$request.departmentID}'>
	{/if}
		
	</form>
</div>
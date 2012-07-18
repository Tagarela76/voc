
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
					{if $reassignError}
					<div id="descr_error" class="error_img" style="display:block;">
						<span class="error_text">Entered name is already in use!</span>
					</div>
					{/if}
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
												<option value='{$productsArr[i].product_id}' {if $productsArr[i].disabled}disabled="disabled"{/if}> {$productsArr[i].formattedProduct} </option>
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
		<td width="10%">Primary</td>
		<td>Supplier</td>
		<td>Product NR</td>
		<td>Description</td>
		<td class="border_users_r">Ratio</td>
	</tr>	
	{assign var=count value=0}
    {assign var="pfpProduct" value=$pfp->getProducts()}
	{foreach from=$pfpProduct item=product}
	
		<tr id="product_row_{$count}">
			<td class="border_users_r border_users_b border_users_l">
				<input type='checkbox' value='{$count}' CHECKED>
			</td>
			
			<td class="border_users_r border_users_b">
				<input type='radio' name='pfp_primary' value='{$product->product_id}' {if $product->isPrimary()}checked="checked"{/if}{if $product->isRange()} disabled="disabled"{/if}>
			</td>
			
			<td class="border_users_r border_users_b">
				<span>{$product->supplier}</span>
			</td>
			
			<td class="border_users_r border_users_b">
				<span>{$product->product_nr}</span>
			</td>
			
			<td class="border_users_r border_users_b">
				<span>{$product->name}</span>
			</td>
			
			<td class="border_users_r border_users_b">
				{if $product->isRange()}
					{assign var="split_range" value="-"|explode:$product->getRangeRatio()}
					{*From <input type="text" /> to <input type="text" /> %*}
					From 
					<select style="width: 50px;" name="product_{$count}_ratio_from">
					{section name=foo loop=100 start=1 step=1}
						<option {if $smarty.section.foo.index eq $split_range[0]} selected="selected"{/if}>{$smarty.section.foo.index}</option>
					{/section}
					</select>
					 to 
					<select style="width: 50px;" name="product_{$count}_ratio_to">
					{section name=foo loop=100 start=1 step=1}
						<option {if $smarty.section.foo.index eq $split_range[1]} selected="selected"{/if}>{$smarty.section.foo.index}</option>
					{/section}
					</select>
					 %
				{else}
					<input type="text" name="product_{$count}_ratio" id="product_{$count}_ratio" value="{$product->getRatio()}" />
				{/if}
			</td>
			
			<input type='hidden' name="product_{$count}_id" id="product_{$count}_id" value="{$product->product_id}" />
		</tr>
		
		
	{assign var=count value=$count+1}
	{/foreach}
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
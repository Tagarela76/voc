
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
	
		//addProduct({$product->getId()},{$product->getRatio()});
	
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
{if $show == false}
	<table class="report_issue" cellspacing="0" cellpadding="0" align="center">
        <tr>
            <td valign="top" class="report_issue_top">
            </td>
        </tr>
        <tr>
            <td class="report_issue_center" align="center" style="vertical-align: middle;">
                <h2>Addition of PFP temporarily unavailable</h2>
            </td>
        </tr>
		<tr>
			<td class="report_issue_center" align="center" style="vertical-align: middle;">
				<input type="button" class="button" value="<< Back" onclick="location.href='admin.php?action=browseCategory&category=pfps&bookmark=pfpLibrary&subBookmark={$request.subBookmark}'"/>
			</td>
		</tr>
        <tr>
            <td valign="top" class="report_issue_bottom">
            </td>
        </tr>
    </table>
{else}	
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
			
			<tr>
				<td class="border_users_l border_users_b" width="15%" height="20">
					Companies
				</td>
				<td class="border_users_l border_users_b border_users_r">
					<div id="companiesPFPList" align="left" >	
						{foreach from=$companyListPFP item=company name=fooList}
							{if $smarty.foreach.fooList.index < $companyListPFP|@count-1}
								&nbsp;{$company},
							{else}
								&nbsp;{$company}
							{/if}	
						{/foreach}
					</div>
					<div>							
						&nbsp;<a href="#" onclick="$('#companiesPopup').dialog('open');return false;">edit</a>
					</div>
					<div id="hiddenCompanies">
						{foreach from=$companyListPFP item=company key=k name=foo}
							<input type="hidden" name="company_{$smarty.foreach.foo.index}" value="{$k}">
						{/foreach}	
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
		
<!--		<table class="users" cellpadding="0" cellspacing="0" align="center" >
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
									 {*<option selected="selected" >Select Product</option>*}
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
					</table>-->

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
	{foreach from=$pfp->products item=product}
	
		<tr id="product_row_{$count}">
			<td class="border_users_r border_users_b border_users_l">
				<input type='checkbox' value='{$count}' CHECKED>
			</td>
			
			<td class="border_users_r border_users_b">
				<input type='radio' name='pfp_primary' value='{$product->product_id}' {if $product->isPrimary()}checked="checked"{/if}>
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
				<input type="text" name="product_{$count}_ratio" id="product_{$count}_ratio" value="{$product->getRatio()}" />
			</td>
			
			<input type='hidden' name="product_{$count}_id" id="product_{$count}_id" value="{$product->product_id}" />
		</tr>
		
		
	{assign var=count value=$count+1}
	{/foreach}
</tbody>	
{*<tfoot>
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
</tfoot>*}													
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
				{if $request.action eq "addItem"} onClick="location.href='admin.php?action=browseCategory&category=pfps&bookmark={$request.category}&subBookmark={$request.subBookmark}'"
				{elseif $request.action eq "edit"} onClick="location.href='?action=viewDetails&category=pfpLibrary&bookmark={$request.category}&subBookmark={$request.subBookmark}&id={$request.id}'"
				{/if}
		>
		{if $request.action !== "addItem"}<input type='button' name='save' id="save" class="button" value='Save'>{/if}
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
{/if}
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

{*SELECT_INDUSTRY_TYPES_CLASS_POPUP*}	
<div id="companiesPopup" title="Choose companies" style="background-color:#e3e9f8; padding:25px; font-size:150%; text-align:center;display:none;">		
		 <table id="companiesList" width="750px" cellpadding="0" cellspacing="0" class="popup_table" align="center" >		
			<tr>			
				 <td class="control_list" colspan="2" style="border-bottom:0px solid #fff;padding-left:0px">						
					Select: 
					<a onclick="CheckAll(this)" name="allTypesClasses" class="id_company1" >All</a>									
				 	/
					<a onclick="unCheckAll(this)" name="allTypesClasses" class="id_company1">None</a>										
				</td>
			</tr>		
					
			<tr class="table_popup_rule">
				<td>
					Select
				</td>
				<td>
					Company Name
				</td>
			</tr>
			
			{foreach from=$companyList item=company key=k}
				<tr>
					<td align="center" style="width:150px">				
						<input type="checkbox"  value="{$company.id}"
							   {foreach from=$companyListPFP item=pfpItem key=j}
								   {if $company.id eq $j} checked {/if}
							   {/foreach}
						/>
					</td>
					<td id="category_{$company.id}">
						{$company.name}&nbsp;
					</td>
				</tr>	
				<tr>
					<td colspan="2"></td>
				</tr>
			{/foreach}
		</table>	
</div>
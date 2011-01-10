{*ajax-preloader*}
<div style="height:16px;text-align:center;">
	<div id="preloader" style="display:none">
		<img src='images/ajax-loader.gif'>
	</div>
</div>

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
	{if $parentCategory == 'facility'}
    <form method='POST' action='?action={$request.action}&category=inventory&id={$request.id}&facilityID={$request.facilityID}&tab={$inventory->getType()}'>    	
    {else $parentCategory == 'department'}
	<form method='POST' action='?action={$request.action}&category=inventory&id={$request.id}&departmentID={$request.departmentID}&tab={$inventory->getType()}'>
	{/if}
	
        <table class="users" align="center" cellpadding="0" cellspacing="0">
            <tr class="users_u_top_size users_top">
                <td class="users_u_top" width="30%">
                    <span>{if $request.action eq "addItem"}Adding for a new inventory{else}Editing inventory{/if}</span>
                </td>
                <td class="users_u_top_r">
                </td>
            </tr>
            <tr>
                <td class="border_users_r border_users_l border_users_b" height="20">
                    1.	Inventory Name :
                </td>
                <td class="border_users_r border_users_b">
                {if !($parentCategory == 'department' && $inventory->getType() == Inventory::PAINT_MATERIAL)}
                    <div class="floatleft">
                        <input type='text' name='inventory_name' value='{$inventory->getName()}'>
                    </div> {if $validStatus.summary eq 'false'}
                    {if $validStatus.inventory_name eq 'failed'}
                    {*ERORR*}
                    <div class="error_img">
                        <span class="error_text">Error!</span>
                    </div>
                    {*/ERORR*}
                    {elseif $validStatus.inventory_name eq 'alredyExist'}
                    <div class="error_img">
                        <span class="error_text">Entered name is alredy in use!</span>
                    </div>
                    {/if}
                    {/if}
				{else}
					{$inventory->getName()}
				{/if}
                </td>
            </tr>
            <tr>
                <td class="border_users_r border_users_l border_users_b" height="20">
                    2.	Description :
                </td>
                <td class="border_users_r border_users_b">
                {if !($parentCategory == 'department' && $inventory->getType() == Inventory::PAINT_MATERIAL)}
                    <div align="left">
                        <input type='text' name='inventory_desc' value='{$inventory->getDescription()}'>
                    </div>
					{if $validStatus.summary eq 'false'}
                    {if $validStatus.inventory_desc eq 'failed'}
                    {*ERORR*}
                    <div class="error_img">
                        <span class="error_text">Error!</span>
                    </div>
                    {*/ERORR*}
                    {/if}
                    {/if}
				{else}
					{$inventory->getDescription()}
				{/if}
                </td>
            </tr>
			{if !($parentCategory == 'department' && $inventory->getType() == Inventory::PAINT_MATERIAL)}
            <tr>
                <td class="border_users_l border_users_b border_users_r" height="20">
                    {if $tab eq 'material'}
                    Add new product:
                    {/if}
                    {if $tab eq 'accessory'}
                    Add new accessory:
                    {/if}
                </td>
                <td class="border_users_r border_users_b">
                    <div align="left">                    
                        <select name="selectProduct" id="selectProduct" class="addInventory" tab={$tab}>
                            {if $tab eq 'material'}
                            	{*section name=i loop=$product}
                            	    {assign var=isAdded value=0}
                            		{assign var=addedProduct value=$inventory->getProducts()}
                            		{section name=j loop=$addedProduct} 
                            			{if $addedProduct[j]->getProductID() eq $product[i].product_id}
                            				{assign var=isAdded value=1}
                            			{/if}
                            		{/section}
                            		{if $isAdded eq 0}
									<option value='{$product[i].product_id}' {if $product[i].product_id  eq $data.product_id}selected="selected"{/if}> {$product[i].formattedProduct}</option>
                            		{/if}
                            	{/section*}
                            	{if $product}				
										{foreach from=$product item=productsArr key=supplier}															
										<optgroup label="{$supplier}">
											{section name=i loop=$productsArr}
												{assign var=isAdded value=0}
												{assign var=addedProduct value=$inventory->getProducts()}
												{section name=j loop=$addedProduct} 												
		                            				{if $addedProduct[j]->getProductID() eq $productsArr[i].product_id}
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
                            {/if}
                            {if $tab eq 'accessory'}
                            	{section name=i loop=$accessory}
                            		{assign var=isAdded value=0}
                            		{assign var=addedProduct value=$inventory->getProducts()}
                            		{section name=j loop=$addedProduct} 
                            			{if $addedProduct[j]->getAccessoryID() eq $accessory[i].id}
                            				{assign var=isAdded value=1}
                            			{/if}
                            		{/section}
                            		{if $isAdded eq 0}
									<option value='{$accessory[i].id}'> {$accessory[i].name} </option>
									{/if}
                            	{/section}
                            {/if}
                        </select>            
                    
                    </div>   
                     {*ERORR*}
                    	<div style="display:none;" class="error_img" id='selectProdError' >
                        	<span  class="error_text">Error!</span>
                    	</div>
                    	{if $validStatus.product eq 'failed'}
                    	<div  class="error_img" id='emptyProdError' >
                        	<span  class="error_text">Error! Please add products to inventory!</span>
                    	</div>
                    	{/if}
                	{*/ERORR*}                    
                </td>            
                           
            </tr>		
			{/if}	
            <tr>
                <td colspan="2" bgcolor="" height="20" class="" style="padding:0px">
                    <table id="productTable" width="100%" align="center" cellspacing="0" cellpadding="0">                    
                    	<thead>
					{if $inventory->getType() == Inventory::PAINT_MATERIAL}	
                        <tr class="users_u_top_size users_top_lightgray">
                        {if $parentCategory == 'facility'}
                            <td class="border_users_l" width="5%">
                                Select
                           </td>
						{/if}
							<td class="">
                                ID
                            </td>
                            <td class="" width="10%">
                                Supplier
                            </td>
                            <td width="10%">
                                Product NR
                            </td>
                            <td>
                                Product Desc
                            </td>                            
                            <td>
                                O.S. Use
                            </td>
                            <td>
                                C.S. Use
                            </td>
                            <td>
                                Location of Storage
                            </td>
                            <td>
                                Location of Use
                            </td>
                            <td>
                                Inventory
                            </td>
                            <td>
                                Quantity
                            </td>
                            <td>
                                Unit
                            </td>
                            <td>
                                Inventory
                            </td>
                            <td>
                                To date left
                            </td>
                        </tr>					
					{elseif $inventory->getType() == Inventory::PAINT_ACCESSORY}					
						 <tr class="users_u_top_size users_top_lightgray">
                            <td class="border_users_l" width="5%">
                                Select
                            </td>
                            <td class="" width="10%">
                                {if $tab eq 'material'}Product ID{/if}
                                {if $tab eq 'accessory'}Accessory ID{/if}
                            </td>
                            <td width="10%">
                                {if $tab eq 'material'}Product Name{/if}
                                {if $tab eq 'accessory'}Accessory Name{/if}
                            </td>
                            <td>
                                Unit Amount
                            </td>                            
                            <td>
                                Unit Count
                            </td>
                            <td>
                                Unit Quantity
                            </td>
                            <td>
                                Total Quantity
                            </td>                           
                        </tr>
					{/if}
                        </thead>
                        <tbody id="productTableBody">                        
						{if $inventory->getProducts()|@count > 0}
                        	{foreach from=$inventory->getProducts() key=i item=product} 
                        		{include file="tpls:inventory/design/addInventoryRow.tpl"  i=$i product=$product}
                        	{/foreach}
            			{/if}            
                        </tbody> 
                    </table>
                </td>                
            </tr>
			
            <tr>
                <td class="users_u_bottom">
                </td>
                <td bgcolor="" height="20" class="users_u_bottom_r">
                </td>
            </tr>
        </table>
        <div align="right" class="margin7">
        	{if $parentCategory == 'facility'}
				<input type='button' class="button" value='Cancel' onclick="location.href='?action=browseCategory&category={$parentCategory}&id={$request.facilityID}&bookmark=inventory&tab={$inventory->getType()}'">
				
				<input type='button' id='addProdButton'  class="button" {if $tab eq 'material'}value='Add product to inventory'{/if}{if $tab eq 'accessory'}value='Add accessory to inventory'{/if} onclick="addProduct($('#selectProduct').val());">
				
			{else}
				<input type='button' class="button" value='Cancel' onclick="location.href='?action=browseCategory&category={$parentCategory}&id={$request.departmentID}&bookmark=inventory&tab={$inventory->getType()}'">
				{if $inventory->getType() == Inventory::PAINT_ACCESSORY}
					<input type='button' class="button" value='Add accessory to inventory' onclick="addProduct($('#selectProduct').val());">					
				{/if}				
			{/if}        	
            <input type='submit' class="button" value='Save'>
			{if $request.action eq "edit"}<input type="hidden" name="id" value="{$inventory->getID()}">{/if}
			<input id="inventoryType" type="hidden" name="inventoryType" value="{$inventory->getType()}">
        </div>									
    </form>
</div>

<div id="departmentForm" title="Select location of use">
	<form>
		<table border="0" class="popup_table" width=100% cellpadding=0 cellspacing=0>
			<thead>
				<tr class="table_popup_rule">
					<td>Select</td>
					<td>ID Number</td>
					<td>Department Name</td>
				</tr>
			</thead>
			<tbody>
				{foreach from=$departments item=department}
				<tr>
					<td><input id="depCheck_{$department.id}" type="checkbox" value="{$department.id}" name="department_id[]"></td>
					<td><label for="depCheck_{$department.id}">{$department.id}</label></td>
					<td><label id="name_{$department.id}" for="depCheck_{$department.id}">{$department.name}</label></td>
				</tr>
				{/foreach}
			</tbody>
		</table>		
	</form>
</div>
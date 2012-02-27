<form id="controlCategoriesList" method="get" action="">

<div align="center" class="control_panel_padd">	
<div class="control_panel" class="logbg" align="left">
<div class="control_panel_tl">
<div class="control_panel_tr">
<div class="control_panel_bl">
<div class="control_panel_br">
<div class="control_panel_center">

<table  cellpadding="0" cellspacing="0" class="controlCategoriesList" style="height:30px">
	<tr>
		<td>
		<div style="float:left; width:80px">
		
		{if ($request.category=="sales" && $request.bookmark == 'clients') || $request.category=="usersSupplier" }
			<div class="add_button button_alpha">
				<input type="submit" name="action" value="addItem">
			</div>
		{/if}
		
		</div>
		
		{if $itemsCount > 0 }{*&& $request.category != "issue"*}
		<div style="float:left; width:80px">
		<div class="delete_button button_alpha">
			<input type="submit" name="action" value="deleteItem" >
		</div>
		</div>
		{/if}
		
		
	{if $request.bookmark=="product"}

		
		</td>
									
	</tr>
	<tr>
		
		<td>
		<select name="companyID">
			<option value="All companies" {if $currentCompany == 0} selected {/if}>All companies {if $currentCompany == 0}(selected){/if}</option>
			{section name=i loop=$companyList}
				<option value="{$companyList[i].id}" {if $companyList[i].id == $currentCompany} selected {/if}>{$companyList[i].name} {if $companyList[i].id == $currentCompany}(selected){/if}</option>
			{/section}
		</select>
		<select name="supplierID">
			<option value="All suppliers" {if $currentSupplier == 0} selected {/if}>All suppliers {if $currentSupplier == 0}(selected){/if}</option>
			{section name=i loop=$supplierList}
				<option value="{$supplierList[i].supplier_id}" {if $supplierList[i].supplier_id == $currentSupplier} selected {/if}>{$supplierList[i].supplier_desc}{if $supplierList[i].supplier_id == $currentSupplier}(selected){/if}</option>
			{/section}			
		</select>
		<input type="button" class="button" name="subaction" value="Filter" onclick="submitFunc('browseCategory','Filter')">
		<br>
		{if $itemsCount > 0}
			{if $currentCompany == 0}
		<input type="button" class="button" name="subaction" value="Assign to company" onclick="submitFunc('browseCategory','Assign to company')">
		{else}
		<input type="button" class="button" name="subaction" value="Unassign product(s)" onclick="submitFunc('browseCategory','Unassign product(s)')" >{/if}
		{/if}
		
		
	{/if}		
			<div id='hiddens'>	
			{if $request.category eq 'tables'}				
				<input type="hidden" name="category" value="{$request.bookmark}">
			{elseif $request.category eq 'issue'}
				<input type="hidden" name="category" value="{$request.category}">
			{elseif $request.category == 'sales' and $request.bookmark == 'clients'}
				<input type="hidden" name="category" value="clients">
				<input type="hidden" name="bookmark" value="clients">
				<input type="hidden" name="supplierID" value="{$supplierID}">
				<!--  <input type="hidden" name="category" value="{$request.category}"> -->
				{if $smarty.request.subBookmark}
					<input type="hidden" name="subBookmark" value="{$smarty.request.subBookmark}">
				{/if}
				
			{else}
				<input type="hidden" name="category" value="{$request.category}">
				{if $request.bookmark}<input type="hidden" name="bookmark" value="{$request.bookmark}">{/if}
				<input type="hidden" name="supplierID" value="{$supplierID}">
			{/if}
				{if $itemsCount}
					<input type="hidden" name="itemsCount" value="{$itemsCount}">			
				{/if}			
				
			</div>
		</td>
	</tr>
</table>
</div></div></div></div></div></div></div>

 
{literal}	
	<script type='text/javascript'>
		function submitFunc(action,subaction)
		{				
			$('#hiddens').append('<input type="hidden" name="subaction" value="'+subaction+'">');
			$('#hiddens').append('<input type="hidden" name="action" value="'+action+'">');
			{/literal}
			{if $request.bookmark=="product"}
			{literal}
			$('#hiddens').append('<input type="hidden" name="sort" value="{$sort}">');			
				if (action == 'browseCategory') {
					$('#hiddens').append('<input type="hidden" name="bookmark" value="product">');					
					$('input[name="category"]').val('tables'); 
				}						
				{/literal}{/if}				
			{literal}	
			$('#controlCategoriesList').submit();
		}	
	</script>
{/literal}

<table align='center'>
	<tr>
		<td>
			<form id="selectCategotyForm" method="get">
				<input type="hidden" name="action" value="{$request.action}"/>
				<input type="hidden" name="category" value="{$request.category}"/>
				{if $request.bookmark}
				<input type="hidden" name="bookmark" value="pfpLibrary"/>
				{/if}
				<input type="hidden" name="subBookmark" value="{$request.subBookmark}"/>
				<input type="hidden" name="letterpage" value="{$request.letterpage}"/>
				
				<select class="addInventory" onchange="$('#selectCategotyForm').submit();" name="productCategory">
					<optgroup label="All">
						<option value="0" {if $request.productCategory == $productType.id}selected{/if}>All</option>

					</optgroup>
					
					{foreach from=$productTypeList item='productType' key="name"}
						<optgroup label="{$name}">
							<option value="{$productType.id}" {if $request.productCategory == $productType.id}selected{/if}>{$name}</option>
							{foreach from=$productType.subTypes item='subType' key="id"}		
								<option value="{$id}" {if $request.productCategory == $id}selected{/if}>{$name} - {$subType}</option>
							{/foreach}
						</optgroup>					
					{/foreach}				
				</select>			
			</form>
		</td>
	</tr>
</table>
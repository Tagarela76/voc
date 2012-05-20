<table align='center'>
	<tr>
		<td>
			<form id="selectCategotyForm" method="get">
				<input type="hidden" name="action" value="browseCategory"/>
				<input type="hidden" name="category" value="{$request.category}"/>
				<input type="hidden" name="bookmark" value="{$request.bookmark}"/>
				<input type="hidden" name="tab" value="{$request.tab}"/>
				<input type="hidden" name="id" value="{$request.id}"/>

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

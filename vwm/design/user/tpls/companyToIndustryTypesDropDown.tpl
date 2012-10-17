<table align='center'>
	<tr>
		<td>
			<form id="selectIndustrytypeForm" method="get">
				<input type="hidden" name="action" value="{$request.action}"/>
				<input type="hidden" name="category" value="{$request.category}"/>

				<select class="addInventory" onchange="$('#selectIndustrytypeForm').submit();" name="productCategory">
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

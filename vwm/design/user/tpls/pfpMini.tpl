<table class="pfpListItemSelected" style="width:100%;" name="pfp_details_products" cellpadding="0" cellspacing="0">
<tr>
	<td>Supplier</td>
	<td>Product NR</td>
	<td>Description</td>
	<td>Ratio</td>
</tr>
{assign var=i value=1}
{foreach from=$pfp->products item=product}
<tr {if $product->isPrimary()}class="pfpListItemSelectedPrimary"{/if}>
	<td>{$i}. {$product->supplier}</td>
	<td>{$product->product_nr}</td>
	<td>{$product->name}</td>
	<td>{$product->getRatio()}</td>
</tr>
{assign var=i value=$i+1}
{/foreach}
<tr>
	<td colspan="4" style="text-align:right;">
		<input type="button" value="Select Pre-formulated products" id="pfp_{$pfp->getId()}" />
	</td>
</tr>
</table>
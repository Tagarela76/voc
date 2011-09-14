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
<script type="text/javascript">
pfp_products_tmp = [];
{foreach from=$pfp->products item=p}
pr = new CProductObj({$p->product_id},0,0,0);
pr.ratio = {$p->getRatio()};
pr.isPrimary = {if $p->isPrimary()}true{else}false{/if};
pfp_products_tmp.push(pr);
{/foreach}
pfp_id = {$pfp->getId()};
pfp_descr = '{$pfp->getDescription()}';
{literal}
$("#pfp_"+pfp_id).click({ "pfp_products" : pfp_products_tmp, "pfp_id" : pfp_id, "pfp_descr" : pfp_descr}, function(e){

	addPFPProducts(e.data.pfp_products, e.data.pfp_id, e.data.pfp_descr);
});
{/literal}
//alert(pfp_products_tmp);
</script>

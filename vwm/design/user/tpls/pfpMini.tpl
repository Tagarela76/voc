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
	<td>
		{if $product->isRange()}
			{assign var="split_range" value="-"|explode:$product->getRangeRatio()}
			<select style="width: 45px;" class="selectRange" id="prod_range_{$i-1}{*$product->product_id*}">
				{section name=range start=$split_range[0] loop=$split_range[1]+1}
					<option>{$smarty.section.range.index}</option>
				{/section}
			</select> % from primary
		{else}
			{$product->getRatio()}
		{/if}
	</td>
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
	
pfp_products = [];
{assign var=jj value=0}
{foreach from=$pfp->products item=p}
	pr = new CProductObj({$p->product_id},0,0,0);
	{if $p->isRange()}
		pr.ratio = $("select.selectRange[id=prod_range_{$jj}]").val();
		pr.isRange = true;
	{else}
		pr.ratio = {$p->getRatio()};
		pr.isRange = false;
	{/if}
	pr.isPrimary = {if $p->isPrimary()}true;{else}false;{/if}
	pfp_products.push(pr);
{assign var=jj value=$jj+1}
{/foreach}
	
pfp_id = {$pfp->getId()};
pfp_descr = '{$pfp->getDescription()}';
{literal}

var ratioSelect = $("select.selectRange");
if (ratioSelect) {
	for (j=0;j<ratioSelect.length;j++) {
		$(ratioSelect[j]).change(function(e) {
			pfp_products[e.currentTarget.attributes.id.value.split('_').reverse()[0]].ratio = e.currentTarget.value;
		});
	}
}
	
$("#pfp_"+pfp_id).click({ "pfp_products" : pfp_products, "pfp_id" : pfp_id, "pfp_descr" : pfp_descr}, function(e){
	addPFPProducts(e.data.pfp_products, e.data.pfp_id, e.data.pfp_descr);
});
{/literal}
</script>

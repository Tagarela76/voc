<table class="pfpListItemSelected" style="width:100%;" name="pfp_details_products" cellpadding="0" cellspacing="0">
	<tr>
		<td>Supplier</td>
		<td>Product NR</td>
		<td>Description</td>
		{if $pfp->getIsProprietary() != 1}
			<td>Ratio</td>
		{/if}
	</tr>
{assign var=i value=1}
{assign var="pfpProducts" value=$pfp->getProducts()}
{foreach from=$pfpProducts item=product}
<tr {if $product->isPrimary()}class="pfpListItemSelectedPrimary"{/if}>
	<td>{$i}. {$product->supplier}</td>
	<td>{$product->product_nr}</td>
	<td>{$product->name}</td>
    {if $pfp->getIsProprietary() != 1}
	<td>

		{if $product->isRange() }
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
    {/if}
</tr>
{assign var=i value=$i+1}
{/foreach}

<tr>
	<td colspan="4" style="text-align:right;">
		<input type="button" value="Select Pre-formulated products" id="pfp_{$pfp->getId()}" />
	</td>
</tr>
</table>

{if $pfp->getIsProprietary() == 1}
{literal}

<script type="text/javascript">
    $(function()
    {

        page.pfpManager.productsOnPreview = [];

        {/literal}{foreach from=$pfpProducts item=product}{literal}
        var product = new PfpProduct();
        product.is_primary = '{/literal}{$product->isPrimary()}{literal}';
        product.supplier_name = '{/literal}{$product->supplier}{literal}';
        product.is_range = '{/literal}{$product->isRange()}{literal}';
        product.product_nr = '{/literal}{$product->product_nr}{literal}';
        product.product_id = '{/literal}{$product->product_id}{literal}';
        product.ratio = '{/literal}{$product->getRatio()}{literal}';

        page.pfpManager.productsOnPreview.push(product);
        {/literal}{/foreach}{literal}

        $("#pfp_"+"{/literal}{$pfp->getId()}{literal}").click(function(e) {
            page.pfpManager.onClickSelectPreformulatedProducts();
        });

    });
</script>

{/literal}
{else}
{*old way - fix in future*}
<script type="text/javascript">

pfp_products = [];
{assign var=jj value=0}
{assign var="pfpProducts" value=$pfp->getProducts()}
{foreach from=$pfpProducts item=p}
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
pfpIsProprieraty = {$pfp->getIsProprietary()}
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
	addPFPProducts(e.data.pfp_products, e.data.pfp_id, e.data.pfp_descr, pfpIsProprieraty);
});
{/literal}
</script>
{/if}

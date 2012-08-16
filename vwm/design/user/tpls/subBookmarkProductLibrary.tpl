{if $request.bookmark == "product"}
				
 <div>
{if $request.libraryType == "paintShop"}
	<a href="?action=browseCategory&category=facility&id=100&bookmark=product">All Products </a>
	<a href="?action=browseCategory&category=facility&id=100&bookmark=product&libraryType=paintShop" class="active_link">Paint Shop Products </a>
	<a href="?action=browseCategory&category=facility&id=100&bookmark=product&libraryType=bodyShop">Body Shop Products </a>
	<a href="?action=browseCategory&category=facility&id=100&bookmark=product&libraryType=detailingShop">Detailing Products </a>
{elseif $request.libraryType == "bodyShop"}
	<a href="?action=browseCategory&category=facility&id=100&bookmark=product">All Products </a>
	<a href="?action=browseCategory&category=facility&id=100&bookmark=product&libraryType=paintShop">Paint Shop Products </a>
	<a href="?action=browseCategory&category=facility&id=100&bookmark=product&libraryType=bodyShop" class="active_link">Body Shop Products </a>
	<a href="?action=browseCategory&category=facility&id=100&bookmark=product&libraryType=detailingShop">Detailing Products </a>
{elseif $request.libraryType == "detailingShop"}
	<a href="?action=browseCategory&category=facility&id=100&bookmark=product">All Products </a>
	<a href="?action=browseCategory&category=facility&id=100&bookmark=product&libraryType=paintShop">Paint Shop Products </a>
	<a href="?action=browseCategory&category=facility&id=100&bookmark=product&libraryType=bodyShop">Body Shop Products </a>
	<a href="?action=browseCategory&category=facility&id=100&bookmark=product&libraryType=detailingShop" class="active_link">Detailing Products </a>
{else}
	<a href="?action=browseCategory&category=facility&id=100&bookmark=product" class="active_link">All Products </a>
	<a href="?action=browseCategory&category=facility&id=100&bookmark=product&libraryType=paintShop">Paint Shop Products </a>
	<a href="?action=browseCategory&category=facility&id=100&bookmark=product&libraryType=bodyShop">Body Shop Products </a>
	<a href="?action=browseCategory&category=facility&id=100&bookmark=product&libraryType=detailingShop">Detailing Products </a>
{/if}
</div>	
{/if}
{if $request.bookmark == "product"}	

 <div>
	<a href="?action=browseCategory&category={$request.category}&id={$request.id}&bookmark=product" {if $request.libraryType == ""} class="active_link" {/if}>All Products </a>
	<a href="?action=browseCategory&category={$request.category}&id={$request.id}&bookmark=product&libraryType=paintShop" {if $request.libraryType == "paintShop"} class="active_link" {/if}>{$paintShopProductLabel|escape}</a>
	<a href="?action=browseCategory&category={$request.category}&id={$request.id}&bookmark=product&libraryType=bodyShop" {if $request.libraryType == "bodyShop"} class="active_link" {/if}>{$bodyShopProductLabel|escape}</a>
	<a href="?action=browseCategory&category={$request.category}&id={$request.id}&bookmark=product&libraryType=detailingShop" {if $request.libraryType == "detailingShop"} class="active_link" {/if}>{$detailingShopProductLabel|escape}</a>
    <a href="?action=browseCategory&category={$request.category}&id={$request.id}&bookmark=product&libraryType=fuelAndOils" {if $request.libraryType == "fuelAndOils"} class="active_link" {/if}>{$fuelAndOilsProductLabel|escape}</a>
    <a href="?action=browseCategory&category={$request.category}&id={$request.id}&bookmark=product&libraryType=powderCoating" {if $request.libraryType == "powderCoating"} class="active_link" {/if}>{$powderCoatingLabel|escape}</a>
 </div>	
{/if}
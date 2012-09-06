{if $request.bookmark == "product"}	

 <div>
	<a href="?action=browseCategory&category={$request.category}&id={$request.id}&bookmark=product" {if $request.libraryType == ""} class="active_link" {/if}>All Products </a>
	<a href="?action=browseCategory&category={$request.category}&id={$request.id}&bookmark=product&libraryType=paintShop" {if $request.libraryType == "paintShop"} class="active_link" {/if}>Paint Shop Products </a>
	<a href="?action=browseCategory&category={$request.category}&id={$request.id}&bookmark=product&libraryType=bodyShop" {if $request.libraryType == "bodyShop"} class="active_link" {/if}>Body Shop Products </a>
	<a href="?action=browseCategory&category={$request.category}&id={$request.id}&bookmark=product&libraryType=detailingShop" {if $request.libraryType == "detailingShop"} class="active_link" {/if}>Detailing Products </a>
 </div>	
{/if}
				{*if $request.category == "department" && $request.bookmark == "inventory"}

                <div>
    				{if $request.tab == Inventory::PAINT_MATERIAL}
						<a href="?action=browseCategory&category=department&id={$request.id}&bookmark=inventory&tab=material" {if $request.tab eq 'material'}class="active_link" {/if}>Products</a>
    					<a href="?action=browseCategory&category=department&id={$request.id}&bookmark=inventory&tab=accessory" {if $request.tab eq 'accessory'}class="active_link" {/if}>Orders</a>
					{elseif $request.tab == Inventory::PAINT_ACCESSORY}
						<a href="?action=browseCategory&category=department&id={$request.id}&bookmark=inventory&tab=material" {if $request.tab eq 'material'}class="active_link" {/if}>{$smarty.const.LABEL_PAINT_PRODUCT_BOOKMARK_DEP}</a>
    					<a href="?action=browseCategory&category=department&id={$request.id}&bookmark=inventory&tab=accessory" {if $request.tab eq 'accessory'}class="active_link" {/if}>{$smarty.const.LABEL_PAINT_ACCESSORY_BOOKMARK_DEP}</a>
					{/if}						
    			</div>{*}
				{if $request.category == "facility" && $request.bookmark == "inventory"}
				<div>

    					<a href="?action=browseCategory&category=facility&id={$request.id}&bookmark=inventory&tab=products" {if $request.tab eq 'products'}class="active_link" {/if}>Products</a>
    					<a href="?action=browseCategory&category=facility&id={$request.id}&bookmark=inventory&tab=orders" {if $request.tab eq 'orders'}class="active_link" {/if}>Orders</a>
    					<a href="?action=browseCategory&category=facility&id={$request.id}&bookmark=inventory&tab=discounts" {if $request.tab eq 'discounts'}class="active_link" {/if}>Discounts</a>
    					<a href="?action=browseCategory&category=facility&id={$request.id}&bookmark=inventory&tab=settings" {if $request.tab eq 'settings'}class="active_link" {/if}>Settings</a>
    			</div>
				
    			{/if}
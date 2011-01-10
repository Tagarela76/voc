				{if $request.category == "department" && $request.bookmark == "inventory"}
				
                <div>
    				{if $request.tab == Inventory::PAINT_MATERIAL}
						<a href="?action=browseCategory&category=department&id={$request.id}&bookmark=inventory&tab=material" {if $request.tab eq 'material'}class="active_link" {/if}>{$smarty.const.LABEL_PAINT_PRODUCT_BOOKMARK_DEP}</a>
    					<a href="?action=browseCategory&category=department&id={$request.id}&bookmark=inventory&tab=accessory" {if $request.tab eq 'accessory'}class="active_link" {/if}>{$smarty.const.LABEL_PAINT_ACCESSORY_BOOKMARK_DEP}</a>
					{elseif $request.tab == Inventory::PAINT_ACCESSORY}
						<a href="?action=browseCategory&category=department&id={$request.id}&bookmark=inventory&tab=material" {if $request.tab eq 'material'}class="active_link" {/if}>{$smarty.const.LABEL_PAINT_PRODUCT_BOOKMARK_DEP}</a>
    					<a href="?action=browseCategory&category=department&id={$request.id}&bookmark=inventory&tab=accessory" {if $request.tab eq 'accessory'}class="active_link" {/if}>{$smarty.const.LABEL_PAINT_ACCESSORY_BOOKMARK_DEP}</a>
					{/if}						
    			</div>
				{elseif $request.category == "facility" && $request.bookmark == "inventory"}
				<div>
					{if $request.tab == Inventory::PAINT_MATERIAL}
    					<a href="?action=browseCategory&category=facility&id={$request.id}&bookmark=inventory&tab=material" {if $request.tab eq 'material'}class="active_link" {/if}>{$smarty.const.LABEL_PAINT_PRODUCT_BOOKMARK}</a>
    					<a href="?action=browseCategory&category=facility&id={$request.id}&bookmark=inventory&tab=accessory" {if $request.tab eq 'accessory'}class="active_link" {/if}>{$smarty.const.LABEL_PAINT_ACCESSORY_BOOKMARK}</a>
					{elseif $request.tab == Inventory::PAINT_ACCESSORY}
						<a href="?action=browseCategory&category=facility&id={$request.id}&bookmark=inventory&tab=material" {if $request.tab eq 'material'}class="active_link" {/if}>{$smarty.const.LABEL_PAINT_PRODUCT_BOOKMARK}</a>
    					<a href="?action=browseCategory&category=facility&id={$request.id}&bookmark=inventory&tab=accessory" {if $request.tab eq 'accessory'}class="active_link" {/if}>{$smarty.const.LABEL_PAINT_ACCESSORY_BOOKMARK}</a>
					{/if}
    			</div>
				
    			{/if}
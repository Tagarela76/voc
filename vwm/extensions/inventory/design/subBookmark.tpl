
				{if $request.category == "facility" && $request.bookmark == "inventory"}
				<div>

    					<a href="?action=browseCategory&category=facility&id={$request.id}&bookmark=inventory&tab=products" {if $request.tab eq 'products'}class="active_link" {/if}>Products</a>
						<a href="?action=browseCategory&category=facility&id={$request.id}&bookmark=inventory&tab=gom" {if $request.tab eq 'gom'}class="active_link" {/if}>GOM</a>
    					<a href="?action=browseCategory&category=facility&id={$request.id}&bookmark=inventory&tab=orders" {if $request.tab eq 'orders'}class="active_link" {/if}>Orders</a>
    					<a href="?action=browseCategory&category=facility&id={$request.id}&bookmark=inventory&tab=discounts" {if $request.tab eq 'discounts'}class="active_link" {/if}>Discounts</a>
    					<a href="?action=browseCategory&category=facility&id={$request.id}&bookmark=inventory&tab=settings" {if $request.tab eq 'settings'}class="active_link" {/if}>Settings</a>
    		
				</div>
				
    			{/if}
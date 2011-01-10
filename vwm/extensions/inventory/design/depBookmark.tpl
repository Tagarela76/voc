<td>
    <a href="?action=browseCategory&category=department&id={$request.id}&bookmark=inventory&tab=material">
    	
		{if $request.bookmark != "inventory"}
        
		<div class="deactiveBookmark">
            <div class="deactiveBookmark_right">
                {$smarty.const.LABEL_INVENTORY_BOOKMARK_DEP}&nbsp;
            </div>        
		</div>
		
        {else}
		
        <div class="activeBookmark_violet">
            <div class="activeBookmark_violet_right">
                {$smarty.const.LABEL_INVENTORY_BOOKMARK_DEP}&nbsp;
            </div>
        </div>
        
		{/if}
		 
    </a>
</td>

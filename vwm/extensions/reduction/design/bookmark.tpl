<td>
    <a href="?action=browseCategory&category=facility&id={$request.id}&bookmark=reduction">
    	
		{if $request.bookmark != "reduction"}
        
		<div class="deactiveBookmark">
            <div class="deactiveBookmark_right">
                {$smarty.const.LABEL_REDUCTION_BOOKMARK}&nbsp;
            </div>
        </div>
		
        {else}
		
        <div class="activeBookmark_orange">
            <div class="activeBookmark_orange_right">
                {$smarty.const.LABEL_REDUCTION_BOOKMARK}&nbsp;
            </div>
        </div>
        
		{/if}
		 
    </a>
</td>

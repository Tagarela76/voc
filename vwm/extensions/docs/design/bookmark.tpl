<td>
    <a href="?action=browseCategory&category=facility&id={$request.id}&bookmark=docs">
    	
		{if $request.bookmark != "docs"}
		
        <div class="deactiveBookmark">
            <div class="deactiveBookmark_right">
                {$smarty.const.LABEL_DOC_BOOKMARK}&nbsp;
            </div>
        </div>
		
        {else}
		
        <div class="activeBookmark_green">
            <div class="activeBookmark_green_right">
                {$smarty.const.LABEL_DOC_BOOKMARK}&nbsp;
            </div>
        </div>
        
		{/if}
		
    </a>
</td>

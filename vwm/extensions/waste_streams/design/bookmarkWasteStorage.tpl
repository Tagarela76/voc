<td>
    <a href="?action=browseCategory&category=facility&id={$request.id}&bookmark=wastestorage&tab=active">
    	
		{if $request.bookmark != "wastestorage"}
        
		<div class="deactiveBookmark">
            <div class="deactiveBookmark_right">
                {$smarty.const.LABEL_WASTESTORAGE_BOOKMARK}&nbsp;
            </div>
        </div>
		
        {else}
		
        <div class="activeBookmark_green">
            <div class="activeBookmark_green_right">
                {$smarty.const.LABEL_WASTESTORAGE_BOOKMARK}&nbsp;
            </div>
        </div>
        
		{/if}
		 
    </a>
</td>

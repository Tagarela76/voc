<td>
    <a href="?action=browseCategory&category=facility&id={$request.id}&bookmark=carbonfootprint&tab=month">
    	
		{if $request.bookmark != "carbonfootprint"}
        
		<div class="deactiveBookmark">
            <div class="deactiveBookmark_right">
                {$smarty.const.LABEL_CARBONFOOTPRINT_BOOKMARK}&nbsp;
            </div>
        </div>
		
        {else}
		
        <div class="activeBookmark_yellowgreen">
            <div class="activeBookmark_yellowgreen_right">
                {$smarty.const.LABEL_CARBONFOOTPRINT_BOOKMARK}&nbsp;
            </div>
        </div>
        
		{/if}
		 
    </a>
</td>

<td>
    <a href="?action=browseCategory&category=facility&id={$request.id}&bookmark=solventplan&tab=month">
    	
		{if $request.bookmark != "solventplan"}
        
		<div class="deactiveBookmark">
            <div class="deactiveBookmark_right">
                {$smarty.const.LABEL_SOLVENTPLAN_BOOKMARK}&nbsp;
            </div>
        </div>
		
        {else}
		
        <div class="activeBookmark_ultraviolet">
            <div class="activeBookmark_ultraviolet_right">
                {$smarty.const.LABEL_SOLVENTPLAN_BOOKMARK}&nbsp;
            </div>
        </div>
        
		{/if}
		 
    </a>
</td>

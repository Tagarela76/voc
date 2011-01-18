<td>
    <a href="?action=browseCategory&category=facility&id={$request.id}&bookmark=regupdate&tab=review">
    	
    	{if $request.bookmark != "regupdate"}
        
		<div class="deactiveBookmark">
            <div class="deactiveBookmark_right">
                {$smarty.const.LABEL_REGUPDATE_BOOKMARK}&nbsp;
                {if $unreadedRegUpdatesCount}
                	({$unreadedRegUpdatesCount})
            	{/if}
            </div>
        </div>
		
        {else}
			<div class="activeBookmark_brown">
            <div class="activeBookmark_brown_right">
                {$smarty.const.LABEL_REGUPDATE_BOOKMARK}&nbsp;
                {if $unreadedRegUpdatesCount}
                	({$unreadedRegUpdatesCount})
            	{/if}
            </div>
        	</div>
		{/if}
    </a>
</td>
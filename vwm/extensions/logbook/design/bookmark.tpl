


<td>
    <a href="?action=browseCategory&category=facility&id={$request.id}&bookmark=logbook&tab=logbook">

		{if $request.bookmark != "logbook"}

		<div class="deactiveBookmark">
            <div class="deactiveBookmark_right">
                {$smarty.const.LABEL_LOGBOOK_BOOKMARK}&nbsp;
            </div>
        </div>

        {else}

        <div class="activeBookmark_brown">
            <div class="activeBookmark_brown_right">
                {$smarty.const.LABEL_LOGBOOK_BOOKMARK}&nbsp;
            </div>
        </div>

		{/if}

    </a>
</td>

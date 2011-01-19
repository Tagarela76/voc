{if $request.bookmark == 'regupdate'}
<a href="?action=browseCategory&category=facility&id={$request.id}&bookmark=regupdate&tab=review" name="active" id="active" {if $request.tab eq 'review'}class="active_link" {/if}>Under review {if $countForTabs.review}({$countForTabs.review}){/if}</a> 
<a href="?action=browseCategory&category=facility&id={$request.id}&bookmark=regupdate&tab=completed" name="removed"  id="removed" {if $request.tab eq 'completed'}class="active_link" {/if}>Completed in last 30 days {if $countForTabs.completed}({$countForTabs.completed}){/if}</a>
{/if}
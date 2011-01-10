{if $request.bookmark == 'wastestorage'}
<a href="?action=browseCategory&category=facility&id={$request.id}&bookmark=wastestorage&tab=active" name="active" id="active" {if $request.tab eq 'active'}class="active_link" {/if}>current</a> 
<a href="?action=browseCategory&category=facility&id={$request.id}&bookmark=wastestorage&tab=removed" name="removed"  id="removed" {if $request.tab eq 'removed'}class="active_link" {/if}>removed</a>
{/if}
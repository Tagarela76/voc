{if $request.category == "department" and $request.bookmark == "pfpLibrary"}
				
 <div>
{if $request.tab == "all"}
		<a href="?action=browseCategory&category=department&id={$request.id}&bookmark=pfpLibrary&tab=all" class="active_link">All Pre Formulated Products </a>
    	<a href="?action=browseCategory&category=department&id={$request.id}&bookmark=pfpLibrary&tab=my">My Pre Formulated Products </a>
{elseif $request.tab == "my"}
		<a href="?action=browseCategory&category=department&id={$request.id}&bookmark=pfpLibrary&tab=all">All Pre Formulated Products </a>
    	<a href="?action=browseCategory&category=department&id={$request.id}&bookmark=pfpLibrary&tab=my" class="active_link">My Pre Formulated Products </a>
{/if}						
</div>	
{/if}
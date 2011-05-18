{if $request.category == "department" and $request.bookmark == "mix"}
				
 <div>
{if $request.tab == "mixes"}
		<a href="?action=browseCategory&category=department&id={$request.id}&bookmark=mix&tab=mixes" class="active_link">Mixes</a>
    	<a href="?action=browseCategory&category=department&id={$request.id}&bookmark=mix&tab=pfp">Pre formulated products</a>
{elseif $request.tab == "pfp"}
		<a href="?action=browseCategory&category=department&id={$request.id}&bookmark=mix&tab=mixes" >Mixes</a>
    	<a href="?action=browseCategory&category=department&id={$request.id}&bookmark=mix&tab=pfp" class="active_link">Pre formulated products</a>
{/if}						
</div>	
{/if}
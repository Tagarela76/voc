{if $request.category == "department" and $request.bookmark == "nox"}
				
 <div>
{if $request.tab == "nox"}
		<a href="?action=browseCategory&category=department&id={$request.id}&bookmark=nox&tab=nox" class="active_link">NOx Emissions</a>
    	<a href="?action=browseCategory&category=department&id={$request.id}&bookmark=nox&tab=burner">Burners</a>
{elseif $request.tab == "burner"}
		<a href="?action=browseCategory&category=department&id={$request.id}&bookmark=nox&tab=nox" >NOx Emissions</a>
    	<a href="?action=browseCategory&category=department&id={$request.id}&bookmark=nox&tab=burner" class="active_link">Burners</a>
{/if}						
</div>	
{/if}
{if $request.bookmark == "nox"  && $request.category == 'facility'}	

 <div>
	<a href="?action=browseCategory&category={$request.category}&id={$request.id}&bookmark=nox&tab=nox" {if $request.tab == "nox"} class="active_link" {/if}>NOx Emissions</a> 
	<a href="?action=browseCategory&category={$request.category}&id={$request.id}&bookmark=nox&tab=burnerRatio" {if $request.tab == "burnerRatio"} class="active_link" {/if}>Burner Ratio's </a>
 </div>	
{/if}
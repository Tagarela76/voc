{if $request.bookmark == "docs" || $request.bookmark == "reminder"}	

 <div>
	<a href="?action=browseCategory&category={$request.category}&id={$request.id}&bookmark=docs" {if $request.bookmark == "docs"} class="active_link" {/if}>Documents</a> 
	<a href="?action=browseCategory&category={$request.category}&id={$request.id}&bookmark=reminder" {if $request.bookmark == "reminder"} class="active_link" {/if}>Reminders</a>
 </div>	
{/if}
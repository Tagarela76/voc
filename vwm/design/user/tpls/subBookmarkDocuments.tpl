{if $request.bookmark == "docs" || $request.bookmark == "reminder" || $request.bookmark == "reminderUsers"}	

 <div>
	<a href="?action=browseCategory&category={$request.category}&id={$request.id}&bookmark=docs" {if $request.bookmark == "docs"} class="active_link" {/if}>Documents</a> 
	<a href="?action=browseCategory&category={$request.category}&id={$request.id}&bookmark=reminder" {if $request.bookmark == "reminder"} class="active_link" {/if}>Reminders</a>
    <a href="?action=browseCategory&category={$request.category}&id={$request.id}&bookmark=reminderUsers" {if $request.bookmark == "reminderUsers"} class="active_link" {/if}>Reminders Users</a>
 </div>	
{/if}
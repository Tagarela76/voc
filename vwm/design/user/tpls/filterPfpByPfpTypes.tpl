<div style="text-align: center;">
	{if $pfpTypes|count > 0}
		<div class="link_bookmark">
		{if $selectedPfpType}
			<a href="{$allUrl}"> all </a>
		{else}
			<a href="{$allUrl}" class="active_link"> all </a>
		{/if}	

		{foreach from=$pfpTypes item=pfpType}
			{assign var='id' value=$smarty.request.id}
			{assign var='pfpTypeId' value=$pfpType->id}
			{assign var='url' value="?action=browseCategory&category=department&id=$id&bookmark=pfpLibrary&tab=all&pfpType=$pfpTypeId"}
			{if $pfpType->id == $selectedPfpType}
				<a href="{$url}" class="active_link"> {$pfpType->name} </a>
			{else}
				<a href="{$url}"> {$pfpType->name} </a> 

			{/if}

		{/foreach}    
		</div>
	{/if}    		
</div>
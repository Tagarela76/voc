{*INVENTORY MODULE*}	
	{if $show.inventory}
		{if $permissions.data.view}
			{include file="tpls:inventory/design/subBookmark.tpl"}                     	
		{/if}          	
	{/if}   
{*/INVENTORY MODULE*}


{*WASTE STREAM MODULE*}
	{if $show.waste_streams}
		{if $permissions.data.view}
			{include file="tpls:waste_streams/design/subBookmark.tpl"}                    
		{/if}						
	{/if}
{*/WASTE STREAM MODULE*}

{*UPDATE REGS MODULE*}
	{if $show.regupdate}
		{if $permissions.data.view}
			{include file="tpls:regupdate/design/subBookmark.tpl"} 
		{/if}
	{/if}
{*/UPDATE REGS MODULE*}

{*SOLVENT PLAN MODULE*}
	{if $show.reduction and $request.bookmark == "solventplan"}
		{if $permissions.data.view}
			{include file="tpls:reduction/design/subBookmarks.tpl"} 
		{/if}
	{/if}

{*/SOLVENT PLAN MODULE*}
                
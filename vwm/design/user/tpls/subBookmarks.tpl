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
                
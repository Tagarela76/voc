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

{*CARBON FOOTPRINT*}
{if $show.carbon_footprint and $request.bookmark == "carbonfootprint"}
	{if $permissions.data.view}
		{include file="tpls:carbon_footprint/design/subBookmarks.tpl"} 
	{/if}
{/if}
{*CARBON FOOTPRINT*}

{*MIX*}
{if $request.bookmark == 'mix' and $request.category == 'department'}
	<!--{include file="tpls:tpls/subBookmarkMix.tpl"} -->
{/if}
{*MIX*}

{*NOX*}
{if $request.bookmark == 'nox' and $request.category == 'department'}
	{include file="tpls:tpls/subBookmarkNox.tpl"}
{/if}
{*NOX*}

{*PFP*}
{if $request.bookmark == 'pfpLibrary' and $request.category == 'department'}
	{include file="tpls:tpls/subBookmarkPfpLibrary.tpl"}
{/if}
{*PFP*}
{*PRODUCT & MSDS LIBRARY*}
{if $request.bookmark == 'product'}
	{include file="tpls:tpls/subBookmarkProductLibrary.tpl"}
{/if}
{*PFP*}

{*NOX*}
{if $request.bookmark == 'nox' and $request.category == 'facility'} 
	{include file="tpls:tpls/subBookmarkNoxFacLevel.tpl"}
{/if}
{*NOX*}

{*DOCS*}
{if $request.bookmark == 'docs' || $request.bookmark == "reminder"} 
	{include file="tpls:tpls/subBookmarkDocuments.tpl"}
{/if}
{*DOCS*}

{*LOGBOOK*}
{if $request.bookmark == 'logbook'} 
	{include file="tpls:tpls/subBookmarkLogbook.tpl"}
{/if}
{*LOGBOOK*}

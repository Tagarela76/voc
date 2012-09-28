
<table cellspacing="0" cellpadding="0" width="100%" style="margin:3px 0 0 0">
    <tr>
        <td align="right">
            <table cellspacing="0" cellpadding="0"height="100%" class="bookmarks">
                <tr>
					
				{if $permissions.data.view}
                    <td>
                        <a href="?action=browseCategory&category=facility&id={$request.id}&bookmark=nox&tab=nox">{if $request.bookmark != "nox"}
                            <div class="deactiveBookmark">
                                <div class="deactiveBookmark_right">
                                    Nox Emissions
                                </div>
                            </div>
                            {else}
                            <div class="activeBookmark_violet">
                                <div class="activeBookmark_violet_right">
                                   NOx Emissions&nbsp;
                                </div>
                            </div>
                            {/if}
                        </a>
                    </td>
                {/if}		

                {*REGULATION UPDATES*}

                {if $show.regupdate}
                               	{if $permissions.data.view}
                		{include file="tpls:regupdate/design/bookmarkRegulationUpdates.tpl"}
                	{/if}
                {/if}
                {*/REGULATION UPDATES*}
                
				{*WASTE STREAM MODULE*}
				{if $show.waste_streams}
					{if $permissions.data.view}
						{include file="tpls:waste_streams/design/bookmarkWasteStorage.tpl"}                    
                    {/if}						
				{/if}
				{*/WASTE STREAM MODULE*}
				
				{*LOGBOOK*}
				{if $show.logbook}
					{if $permissions.data.view}
						{include file="tpls:logbook/design/bookmark.tpl"}                    
                    {/if}						
				{/if}
				{*/LOGBOOK*}
				
                {*CARBON FOOTPRINT*}	
                {if $show.carbon_footprint}
					{if $permissions.data.view}
						{include file="tpls:carbon_footprint/design/bookmark.tpl"}                    
                    {/if}						
				{/if}
                {*/CARBON FOOTPRINT*}
                
				{*REDUCTION MODULE*}	
                	{if $show.reduction}
						{  if $permissions.data.view}
							{include file="tpls:reduction/design/bookmark.tpl"} 
							{include file="tpls:reduction/design/bookmarkSolventPlan.tpl"}                    
                    	{  /if}						
					{/if}
                {*REDUCTION MODULE*}
					
                {*DOCUMENT RETENTION SYSTEM MODULE*}	
                {if $show.docs}
					{if $permissions.data.view}
						{include file="tpls:docs/design/bookmark.tpl"}                    
                    {/if}						
				{/if}
                {*/DOCUMENT RETENTION SYSTEM MODULE*}    
                
					
				{*INVENTORY MODULE*}	
				{if $show.inventory}
                {if $permissions.data.view}
			{include file="tpls:inventory/design/bookmark.tpl"}                     
                {/if}          
				{/if}   
                {*/INVENTORY MODULE*}
                
                {*SOLVENT PLAN MODULE*}
                {if $show.reports}
                	
                {/if}
                {*Show: {get_keys($show)}
                {foreach from=$show key=k item=i}
                	item: {$i}, key: {$k}<br/>
                {/foreach}*/
                {*/SOLVENT PLAN MODULE*}
				

                    {if $permissions.data.view}
                    <td>
                        <a href="?action=browseCategory&category=facility&id={$request.id}&bookmark=product">{if $request.bookmark != "product"}
                            <div class="deactiveBookmark">
                                <div class="deactiveBookmark_right">
                                    {$smarty.const.LABEL_PRODUCTS_BOOKMARK}&nbsp;
                                </div>
                            </div>
                            {else}
                            <div class="activeBookmark_green">
                                <div class="activeBookmark_green_right">
                                    {$smarty.const.LABEL_PRODUCTS_BOOKMARK}&nbsp;
                                </div>
                            </div>
                            {/if}
                        </a>
                    </td>
                    {/if}


                    <td>
                        <a href="?action=browseCategory&category=facility&id={$request.id}&bookmark=repairOrder">
						 {if $request.bookmark != "repairOrder"}
                            <div class="deactiveBookmark">
                                <div class="deactiveBookmark_right">
                                    Repair Order&nbsp;
                                </div>
                            </div>
                            {else}
                            <div class="activeBookmark">
                                <div class="activeBookmark_right">
                                    Repair Order&nbsp;
                                </div>
                            </div>
                            {/if}
                        </a>
                    </td>   
                    
                    {if $permissions.data.view}
				    <td>
                        <a href="?action=browseCategory&category=facility&id={$request.id}&bookmark=pfpTypes">
						 {if $request.bookmark != "pfpTypes"}
                            <div class="deactiveBookmark">
                                <div class="deactiveBookmark_right">
                                    PFP types&nbsp;
                                </div>
                            </div>
                            {else}
                            <div class="activeBookmark">
                                <div class="activeBookmark_right">
                                    PFP types&nbsp;
                                </div>
                            </div>
                            {/if}
                        </a>
                    </td>  
                    {/if}
                    
                    {if $permissions.data.view}
                    <td>
                        <a href="?action=browseCategory&category=facility&id={$request.id}&bookmark=department">{if $request.bookmark != "department"}
                            <div class="deactiveBookmark">
                                <div class="deactiveBookmark_right">
                                    {$smarty.const.LABEL_DEPARTMENT_BOOKMARK}&nbsp;
                                </div>
                            </div>
                            {else}
                            <div class="activeBookmark">
                                <div class="activeBookmark_right">
                                    {$smarty.const.LABEL_DEPARTMENT_BOOKMARK}&nbsp;
                                </div>
                            </div>
                            {/if}
                        </a>
                    </td>
                    {/if}    
    
                    <td width="15px">
                    </td>
                 {* active bottom</tr><tr height="19">
                    <td {if $request.bookmark  eq "docs"}  class="" {/if}>
                    </td>
                    <td {if $request.bookmark  eq "inventory"}  class="" {/if}>
                    </td>
                    <td {if $request.bookmark  eq "department"}  class="bookmark_bg_green" {/if}>
                    </td>  *}                  
                </tr>                                    
            </table>
</td>

   			 </tr>


              <tr>
                	<td colspan="2"
{if $request.bookmark  eq "department"}  class="bookmark_bg" {/if}
{if $request.bookmark  eq "product"}  class="bookmark_bg_green" {/if}
{if $request.bookmark  eq "inventory"}  class="bookmark_bg_violet" {/if}
{if $request.bookmark  eq "docs"}  class="bookmark_bg_green" {/if}
{if $request.bookmark  eq "reduction"}  class="bookmark_bg_orange" {/if}
{if $request.bookmark  eq "carbonfootprint"}  class="bookmark_bg_yellowgreen" {/if}
{if $request.bookmark  eq "logbook"}  class="bookmark_bg_brown" {/if}
{if $request.bookmark  eq "solventplan"}  class="bookmark_bg_ultraviolet" {/if}
{if $request.bookmark  eq "wastestorage"}  class="bookmark_bg_green" {/if}
{if $request.bookmark  eq "regupdate"}  class="bookmark_bg_brown" {/if} 
{if $request.bookmark  eq "emissionGraphs"}  class="bookmark_bg_green" {/if}
{if $request.bookmark  eq "nox"}  class="bookmark_bg_violet" {/if}
{if $request.bookmark  eq "repairOrder"}  class="bookmark_bg" {/if}	
{if $request.bookmark  eq "pfpTypes"}  class="bookmark_bg" {/if} 
{if $request.bookmark  eq "reminders"}  class="bookmark_bg_green" {/if}>
	<div align="right"  class="link_bookmark">{include file="tpls:tpls/subBookmarks.tpl"}&nbsp;</div>

        </td>

    </tr>
</table>

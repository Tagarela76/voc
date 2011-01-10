<table cellspacing="0" cellpadding="0" width="100%" style="margin:3px 0 0 0">
    <tr>
        <td align="right">
            <table cellspacing="0" cellpadding="0"height="100%" class="bookmarks">
                <tr>
                
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
                    <td {if $request.bookmark  eq "department"}  class="" {/if}>
                    </td>  *}                  
                </tr>                                    
            </table>
</td>

   			 </tr>
              <tr>
                	<td colspan="2"
{if $request.bookmark  eq "department"}  class="bookmark_bg" {/if}
{if $request.bookmark  eq "inventory"}  class="bookmark_bg_violet" {/if}
{if $request.bookmark  eq "docs"}  class="bookmark_bg_green" {/if}
{if $request.bookmark  eq "reduction"}  class="bookmark_bg_orange" {/if}
{if $request.bookmark  eq "carbonfootprint"}  class="bookmark_bg_yellowgreen" {/if}
{if $request.bookmark  eq "logbook"}  class="bookmark_bg_brown" {/if}
{if $request.bookmark  eq "solventplan"}  class="bookmark_bg_ultraviolet" {/if}
{if $request.bookmark  eq "wastestorage"}  class="bookmark_bg_green" {/if}>
	<div align="right"  class="link_bookmark">{include file="tpls:tpls/subBookmarks.tpl"}&nbsp;</div>

        </td>
    </tr>
</table>

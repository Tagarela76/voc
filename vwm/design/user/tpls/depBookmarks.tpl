<table cellspacing="0" cellpadding="0" width="100%" style="margin:3px 0 0 0">
    <tr>
        <td align="right">
            <table cellspacing="0" cellpadding="0"height="100%" class="bookmarks">
                <tr>
                    {if $permissions.data.view}
                    <td>
                        <a href="?action=browseCategory&category=department&id={$request.id}&bookmark=accessory">{if $request.bookmark != "accessory"}
                            <div class="deactiveBookmark">
                                <div class="deactiveBookmark_right">
                                    {$smarty.const.LABEL_ACCESSORY_BOOKMARK}&nbsp;
                                </div>
                            </div>
                            {else}
                            <div class="activeBookmark_green">
                                <div class="activeBookmark_green_right">
                                    {$smarty.const.LABEL_ACCESSORY_BOOKMARK}&nbsp;
                                </div>
                            </div>
                            {/if}
                        </a>
                    </td>
                    {/if}
                    {if $permissions.data.view}
                    <td>
                        <a href="?action=browseCategory&category=department&id={$request.id}&bookmark=product">{if $request.bookmark != "product"}
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
					
					{*INVENTORY MODULE*}	
					{if $show.inventory}
                    	{if $permissions.data.view}
							{include file="tpls:inventory/design/depBookmark.tpl"}                     
                    	{/if}          
					{/if}   
                	{*/INVENTORY MODULE*}
                                        
                    
                    {if $permissions.equipment.view}
                    <td>
                        <a href="?action=browseCategory&category=department&id={$request.id}&bookmark=equipment">{if $request.bookmark != "equipment"}
                            <div class="deactiveBookmark">
                                <div class="deactiveBookmark_right">
                                    {$smarty.const.LABEL_EQUIPMENT_BOOKMARK}
                                </div>
                            </div>
                            {else}
                            <div class = "activeBookmark">
                                <div class = "activeBookmark_right">
                                    {$smarty.const.LABEL_EQUIPMENT_BOOKMARK}
                                </div>
                            </div>
                            {/if}
                        </a>
                    </td>
                    {/if}

                    {if $permissions.data.view}
                    <td>
                        <a href="?action=browseCategory&category=department&id={$request.id}&bookmark=mix&tab=mixes">{if $request.bookmark != "mix"}
                            <div class="deactiveBookmark">
                                <div class="deactiveBookmark_right">
                                    {$smarty.const.LABEL_MIX_BOOKMARK}&nbsp;
                                </div>
                            </div>
                            {else}
                            <div class="activeBookmark_orange">
                                <div class="activeBookmark_orange_right">
                                    {$smarty.const.LABEL_MIX_BOOKMARK}&nbsp;
                                </div>
                            </div>
                            {/if}
                        </a>
                    </td>
                    {/if}
                    <td width="15px">
                    </td>
                   {* active bottom <td {if $request.bookmark  eq "product"}  class="active_bookmark_green_fon" {/if}>
                    </td>
                    <td {if $request.bookmark  eq "inventory"}  class="active_bookmark_violet_fon" {/if}>
                    </td>
                    <td {if $request.bookmark  eq "equipment"}  class="active_bookmark_fon" {/if}>
                    </td>
                    <td {if $request.bookmark  eq "mix"}  class="active_bookmark_orange_fon" {/if}>
                    </td>   *}                 
                </tr>								
            </table>
        </td>

   			 </tr>
              <tr>
                	<td colspan="2"
{if $request.bookmark  eq "accessory"}  class="bookmark_bg_green" {/if}
{if $request.bookmark  eq "equipment"}  class="bookmark_bg" {/if}
{if $request.bookmark  eq "inventory"}  class="bookmark_bg_violet" {/if}
{if $request.bookmark  eq "product"}  class="bookmark_bg_green" {/if}
{if $request.bookmark  eq "mix"}  class="bookmark_bg_orange" {/if}
{if $request.bookmark  eq "emissionGraphs"}  class="bookmark_bg_green" {/if} >
	
                		<div align="right"  class="link_bookmark">{include file="tpls:tpls/subBookmarks.tpl"}&nbsp;</div>
						
                	</td>
</table>

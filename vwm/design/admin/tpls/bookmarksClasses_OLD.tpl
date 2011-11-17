<link href="style.css" rel="stylesheet" type="text/css">
<table cellspacing="0" cellpadding="0" width="100%" style="margin:10px 0 0 0">
    <tr>
        <td align="right"
{if $request.bookmark  eq "apmethod"}  class="bookmark_fon" {/if}
{if $request.bookmark  eq "coat"}  class="bookmark_fon" {/if}
{if $request.bookmark  eq "product"}  class="bookmark_fon" {/if}
{if $request.bookmark  eq "industryType"}  class="bookmark_fon" {/if}
{if $request.bookmark  eq "industrySubType"}  class="bookmark_fon" {/if}
{if $request.bookmark  eq "components"}  class="bookmark_fon" {/if}
{if $request.bookmark  eq "agency"}  class="bookmark_fon" {/if}
{if $request.bookmark  eq "density"}  class="bookmark_fon" {/if}
{if $request.bookmark  eq "country"}  class="bookmark_fon" {/if}
{if $request.bookmark  eq "lol"}  class="bookmark_fon" {/if}
{if $request.bookmark  eq "msds"}  class="bookmark_fon" {/if}
{if $request.bookmark  eq "rule"}  class="bookmark_fon" {/if}
{if $request.bookmark  eq "substrate"}  class="bookmark_fon" {/if}
{if $request.bookmark  eq "supplier"}  class="bookmark_fon" {/if}
{if $request.bookmark  eq "type"}  class="bookmark_fon" {/if}
{if $request.bookmark  eq "unittype"}  class="bookmark_fon" {/if}
{if $request.bookmark  eq "formulas"}  class="bookmark_fon" {/if}
{if $request.bookmark  eq "emissionFactor"}  class="bookmark_fon" {/if}
{if $request.bookmark  eq "tabs"}  class="bookmark_fon" {/if} >
            <table cellspacing="0" cellpadding="0"height="100%" class="bookmarks_big" style="margin-left:10px">
                <tr>
                    <td>
                        <a href="admin.php?action=browseCategory&category={$request.category}&bookmark=apmethod">{if $request.bookmark != "apmethod"}
                            <div class="deactiveBookmark_big">
                                <div class="deactiveBookmark_right_big">
                                {else}
                                <div class = "activeBookmark_big">
                                    <div class = "activeBookmark_right_big">
                                        {/if}
                                        {$smarty.const.AI_LABEL_APMETHOD_BOOKMARK}
                                    </div>
                                </div>
                                </a>
                            </td>
                            <td>
                                <a href="admin.php?action=browseCategory&category={$request.category}&bookmark=coat">{if $request.bookmark != "coat"}
                                    <div class="deactiveBookmark">
                                        <div class="deactiveBookmark_right">
                                        {else}
                                        <div class = "activeBookmark">
                                            <div class = "activeBookmark_right">
                                                {/if}
                                                 {$smarty.const.AI_LABEL_COAT_BOOKMARK}
                                            </div>
                                        </div>
                                        </a>
                                    </td>
                                    <td>
                                        <a href="admin.php?action=browseCategory&category={$request.category}&bookmark=product">{if $request.bookmark != "product"}
                                            <div class="deactiveBookmark">
                                                <div class="deactiveBookmark_right">
                                                {else}
                                                <div class="activeBookmark">
                                                    <div class="activeBookmark_right">
                                                        {/if}
                                                       {$smarty.const.AI_LABEL_PRODUCT_BOOKMARK}
                                                    </div>
                                                </div>
                                                </a>
                                            </td>
											<td>
                                        <a href="admin.php?action=browseCategory&category={$request.category}&bookmark=industryType">{if $request.bookmark != "industryType"}
                                            <div class="deactiveBookmark">
                                                <div class="deactiveBookmark_right">
                                                {else}
                                                <div class="activeBookmark">
                                                    <div class="activeBookmark_right">
                                                        {/if}
                                                       {$smarty.const.AI_LABEL_INDUSTRY_TYPE_BOOKMARK}
                                                    </div>
                                                </div>
                                                </a>
                                            </td>
											<td>
                                        <a href="admin.php?action=browseCategory&category={$request.category}&bookmark=industrySubType">{if $request.bookmark != "industrySubType"}
                                            <div class="deactiveBookmark">
                                                <div class="deactiveBookmark_right">
                                                {else}
                                                <div class="activeBookmark">
                                                    <div class="activeBookmark_right">
                                                        {/if}
                                                       {$smarty.const.AI_LABEL_INDUSTRY_SUB_TYPE_BOOKMARK}
                                                    </div>
                                                </div>
                                                </a>
                                            </td>
                                            <td>
                                                <a href="admin.php?action=browseCategory&category={$request.category}&bookmark=components">{if $request.bookmark != "components"}
                                                    <div class="deactiveBookmark">
                                                        <div class="deactiveBookmark_right">
                                                        {else}
                                                        <div class = "activeBookmark">
                                                            <div class = "activeBookmark_right">
                                                                {/if}
                                                                {$smarty.const.AI_LABEL_COMPOUNDS_BOOKMARK}
                                                            </div>
                                                        </div>
                                                        </a>
                                                    </td>
                                                    <td>
                                                        <a href="admin.php?action=browseCategory&category={$request.category}&bookmark=agency">{if $request.bookmark != "agency"}
                                                            <div class="deactiveBookmark">
                                                                <div class="deactiveBookmark_right">
                                                                {else}
                                                                <div class = "activeBookmark">
                                                                    <div class = "activeBookmark_right">
                                                                        {/if}
                                                                        {$smarty.const.AI_LABEL_AGENCIES_BOOKMARK}
                                                                    </div>
                                                                </div>
                                                                </a>
                                                            </td>
                                                           {* <td>
                                                                <a href="admin.php?action=browseCategory&category={$request.category}&bookmark=density">{if $request.bookmark != "density"}
                                                                    <div class="deactiveBookmark">
                                                                        <div class="deactiveBookmark_right">
                                                                        {else}
                                                                        <div class = "activeBookmark">
                                                                            <div class = "activeBookmark_right">
                                                                                {/if}
                                                                                Density
                                                                            </div>
                                                                        </div>
                                                                        </a>
                                                                    </td>*}
                                                                    <td>
                                                                        <a href="admin.php?action=browseCategory&category={$request.category}&bookmark=country">{if $request.bookmark != "country"}
                                                                            <div class="deactiveBookmark">
                                                                                <div class="deactiveBookmark_right">
                                                                                {else}
                                                                                <div class = "activeBookmark">
                                                                                    <div class = "activeBookmark_right">
                                                                                        {/if}
                                                                                       {$smarty.const.AI_LABEL_COUNTRY_BOOKMARK}
                                                                                    </div>
                                                                                </div>
                                                                                </a>
                                                                            </td>
                                                                     {*       <td>
                                                                                <a href="admin.php?action=browseCategory&category={$request.category}&bookmark=lol">{if $request.bookmark != "lol"}
                                                                                    <div class="deactiveBookmark_big">
                                                                                        <div class="deactiveBookmark_right_big" nowrap="nowrap">
                                                                                        {else}
                                                                                        <div class = "activeBookmark_big">
                                                                                            <div class = "activeBookmark_right_big" nowrap="nowrap">
                                                                                                {/if}
                                                                                                List of Lists
                                                                                            </div>
                                                                                        </div>
                                                                                        </a>
                                                                                    </td>
                                                                                    <td>
                                                                                        <a href="admin.php?action=browseCategory&category={$request.category}&bookmark=msds">{if $request.bookmark != "msds"}
                                                                                            <div class="deactiveBookmark">
                                                                                                <div class="deactiveBookmark_right">
                                                                                                {else}
                                                                                                <div class = "activeBookmark">
                                                                                                    <div class = "activeBookmark_right">
                                                                                                        {/if}
                                                                                                        MSDS
                                                                                                    </div>
                                                                                                </div>
                                                                                                </a>
                                                                                            </td>*}
                                                                                            <td>
                                                                                                <a href="admin.php?action=browseCategory&category={$request.category}&bookmark=rule">{if $request.bookmark != "rule"}
                                                                                                    <div class="deactiveBookmark">
                                                                                                        <div class="deactiveBookmark_right">
                                                                                                        {else}
                                                                                                        <div class = "activeBookmark">
                                                                                                            <div class = "activeBookmark_right">
                                                                                                                {/if}
                                                                                                                {$smarty.const.AI_LABEL_RULE_BOOKMARK}
                                                                                                            </div>
                                                                                                        </div>
                                                                                                        </a>
                                                                                                    </td>
                                                                                                    <td>
                                                                                                        <a href="admin.php?action=browseCategory&category={$request.category}&bookmark=substrate">{if $request.bookmark != "substrate"}
                                                                                                            <div class="deactiveBookmark">
                                                                                                                <div class="deactiveBookmark_right">
                                                                                                                {else}
                                                                                                                <div class = "activeBookmark">
                                                                                                                    <div class = "activeBookmark_right">
                                                                                                                        {/if}
                                                                                                                         {$smarty.const.AI_LABEL_SUBSTRATE_BOOKMARK}
                                                                                                                    </div>
                                                                                                                </div>
                                                                                                                </a>
                                                                                                            </td>
                                                                                                            <td>
                                                                                                                <a href="admin.php?action=browseCategory&category={$request.category}&bookmark=supplier">{if $request.bookmark != "supplier"}
                                                                                                                    <div class="deactiveBookmark">
                                                                                                                        <div class="deactiveBookmark_right">
                                                                                                                        {else}
                                                                                                                        <div class = "activeBookmark">
                                                                                                                            <div class = "activeBookmark_right">
                                                                                                                                {/if}
                                                                                                                                {$smarty.const.AI_LABEL_SUPPLIER_BOOKMARK}
                                                                                                                            </div>
                                                                                                                        </div>
                                                                                                                        </a>
                                                                                                                    </td>
                                                                                                                  {*  <td>
                                                                                                                        <a href="admin.php?action=browseCategory&category={$request.category}&bookmark=type">{if $request.bookmark != "type"}
                                                                                                                            <div class="deactiveBookmark">
                                                                                                                                <div class="deactiveBookmark_right">
                                                                                                                                {else}
                                                                                                                                <div class = "activeBookmark">
                                                                                                                                    <div class = "activeBookmark_right">
                                                                                                                                        {/if}
                                                                                                                                        Type
                                                                                                                                    </div>
                                                                                                                                </div>
                                                                                                                                </a>
                                                                                                                            </td>
                                                                                                                            <td>
                                                                                                                                <a href="admin.php?action=browseCategory&category={$request.category}&bookmark=unittype">{if $request.bookmark != "unittype"}
                                                                                                                                    <div class="deactiveBookmark">
                                                                                                                                        <div class="deactiveBookmark_right">
                                                                                                                                        {else}
                                                                                                                                        <div class = "activeBookmark">
                                                                                                                                            <div class = "activeBookmark_right">
                                                                                                                                                {/if}
                                                                                                                                                Unittype
                                                                                                                                            </div>
                                                                                                                                        </div>
                                                                                                                                        </a>
                                                                                                                                    </td>
                                                                                                                                    <td>
                                                                                                                                        <a href="admin.php?action=browseCategory&category={$request.category}&bookmark=formulas">{if $request.bookmark != "formulas"}
                                                                                                                                            <div class="deactiveBookmark">
                                                                                                                                                <div class="deactiveBookmark_right">
                                                                                                                                                {else}
                                                                                                                                                <div class = "activeBookmark">
                                                                                                                                                    <div class = "activeBookmark_right">
                                                                                                                                                        {/if}
                                                                                                                                                        Formulas
                                                                                                                                                    </div>
                                                                                                                                                </div>
                                                                                                                                                </a>
                                                                                                                                            </td>*}
                                                                                                                            {if $showFootprint neq 'false'}                
                                                                                                                                <td>
                                                                                                                                	<a href="admin.php?action=browseCategory&category={$request.category}&bookmark=emissionFactor">
                                                                                                                                		{if $request.bookmark != "emissionFactor"}
                                                                                                                                    		<div class="deactiveBookmark_big">
	                                                                                                                                        	<div class="deactiveBookmark_right_big">
                                                                                                                                        {else}
    	                                                                                                                                    <div class = "activeBookmark_big">
        	                                                                                                                                    <div class = "activeBookmark_right_big">
                                                                                                                                        {/if}
                                                                                                                                                 {$smarty.const.AI_LABEL_EFACTOR_BOOKMARK}
                                                                                                                                            	</div>
                                                                                                                                        	</div>
                                                                                                                                    </a>
                                                                                                                               </td>
                                                                                                                           {/if}    
                                                                                                                           
                                                                                                                           <td>
                                                                                                                                	<a href="admin.php?action=browseCategory&category={$request.category}&bookmark=tabs">
                                                                                                                                		{if $request.bookmark != "tabs"}
                                                                                                                                    		<div class="deactiveBookmark">
	                                                                                                                                        	<div class="deactiveBookmark_right">
                                                                                                                                        {else}
    	                                                                                                                                    <div class = "activeBookmark">
        	                                                                                                                                    <div class = "activeBookmark_right">
                                                                                                                                        {/if}
                                                                                                                                                {$smarty.const.AI_LABEL_TABS_BOOKMARK}
                                                                                                                                            	</div>
                                                                                                                                        	</div>
                                                                                                                                    </a>
                                                                                                                               </td> 
                                                                                                                                            <td width="20px">
                                                                                                                                            </td>
                                                                                                                                            <td>
                                                                                                                                            </tr>
                                                                                                                                            <tr height="19">
                                                                                                                                            <td {if $request.bookmark  eq "apmethod"}  class="active_bookmark_fon" {/if} >
                                                                                                                                            </td>
                                                                                                                                        </td>
                                                                                                                                        <td {if $request.bookmark  eq "coat"}  class="active_bookmark_fon" {/if}>
                                                                                                                                        </td>
                                                                                                                                        <td {if $request.bookmark  eq "product"}  class="active_bookmark_fon" {/if}>
                                                                                                                                        </td>
																																		<td {if $request.bookmark  eq "industryType"}  class="active_bookmark_fon" {/if}>
                                                                                                                                        </td>
																																		<td {if $request.bookmark  eq "industrySubType"}  class="active_bookmark_fon" {/if}>
                                                                                                                                        </td>
                                                                                                                                        <td {if $request.bookmark  eq "components"}  class="active_bookmark_fon" {/if}>
                                                                                                                                        </td>
                                                                                                                                        <td {if $request.bookmark  eq "agency"}  class="active_bookmark_fon" {/if}>
                                                                                                                                        </td>
                                                                                                                                        
                                                                                                                                        {*<td {if $request.bookmark  eq "density"}  class="active_bookmark_fon" {/if}>*}
                                                                                                                                        
                                                                                                                                        </td>
                                                                                                                                        <td {if $request.bookmark  eq "country"}  class="active_bookmark_fon" {/if}>
                                                                                                                                        </td>
                                                                                                                                        
                                                                                                                                        {*<td {if $request.bookmark  eq "lol"}  class="active_bookmark_fon" {/if}>
                                                                                                                                        </td>
                                                                                                                                        <td {if $request.bookmark  eq "msds"}  class="active_bookmark_fon" {/if}>
                                                                                                                                        </td>*}
                                                                                                                                        
                                                                                                                                        <td {if $request.bookmark  eq "rule"}  class="active_bookmark_fon" {/if}>
                                                                                                                                        </td>
                                                                                                                                        <td {if $request.bookmark  eq "substrate"}  class="active_bookmark_fon" {/if}>
                                                                                                                                        </td>
                                                                                                                                        <td {if $request.bookmark  eq "supplier"}  class="active_bookmark_fon" {/if}>
                                                                                                                                        </td>
                                                                                                                                        
                                                                                                                                        {*<td {if $request.bookmark  eq "type"}  class="active_bookmark_fon" {/if}>
                                                                                                                                        </td>
                                                                                                                                        <td {if $request.bookmark  eq "unittype"}  class="active_bookmark_fon" {/if}>
                                                                                                                                        </td>
                                                                                                                                        <td {if $request.bookmark  eq "formulas"}  class="active_bookmark_fon" {/if}>
                                                                                                                                        </td>*}
                                                                                                                                        
                                                                                                                                        <td {if $request.bookmark  eq "emissionFactor"}  class="active_bookmark_fon" {/if}>
                                                                                                                                        </td>
                                                                                                                                        
                                                                                                                                        <td {if $request.bookmark  eq "tabs"}  class="active_bookmark_fon" {/if}>
                                                                                                                                        </td>
                                                                                                                                        </tr>
                                                                                                                                    </table>
																																	
                                                                                                                                    </td>
                                                                                                                                    <td
{if $request.bookmark  eq "apmethod"}  class="bookmark_fon" {/if}
{if $request.bookmark  eq "coat"}  class="bookmark_fon" {/if}
{if $request.bookmark  eq "product"}  class="bookmark_fon" {/if}
{if $request.bookmark  eq "industryType"}  class="bookmark_fon" {/if}
{if $request.bookmark  eq "industrySubType"}  class="bookmark_fon" {/if}
{if $request.bookmark  eq "components"}  class="bookmark_fon" {/if}
{if $request.bookmark  eq "agency"}  class="bookmark_fon" {/if}
{if $request.bookmark  eq "density"}  class="bookmark_fon" {/if}
{if $request.bookmark  eq "country"}  class="bookmark_fon" {/if}
{if $request.bookmark  eq "lol"}  class="bookmark_fon" {/if}
{if $request.bookmark  eq "msds"}  class="bookmark_fon" {/if}
{if $request.bookmark  eq "rule"}  class="bookmark_fon" {/if}
{if $request.bookmark  eq "substrate"}  class="bookmark_fon" {/if}
{if $request.bookmark  eq "supplier"}  class="bookmark_fon" {/if}
{if $request.bookmark  eq "type"}  class="bookmark_fon" {/if}
{if $request.bookmark  eq "unittype"}  class="bookmark_fon" {/if}
{if $request.bookmark  eq "formulas"}  class="bookmark_fon" {/if}
{if $request.bookmark  eq "emissionFactor"}  class="bookmark_fon" {/if}
{if $request.bookmark  eq "tabs"}  class="bookmark_fon" {/if} >
                                                                                                                                    </td>
                                                                                                                                </tr>
                                                                                                                                </table>
                                                                                                                                {}
<link href="style.css" rel="stylesheet" type="text/css">
<table cellspacing="0" cellpadding="0" width="100%" style="margin:10px 0 0 0">
    <tr>
        <td align="right"
{if $bookmarkType  eq "apmethod"}  class="bookmark_fon" {/if}
{if $bookmarkType  eq "coat"}  class="bookmark_fon" {/if}
{if $bookmarkType  eq "product"}  class="bookmark_fon" {/if}
{if $bookmarkType  eq "components"}  class="bookmark_fon" {/if}
{if $bookmarkType  eq "agency"}  class="bookmark_fon" {/if}
{if $bookmarkType  eq "density"}  class="bookmark_fon" {/if}
{if $bookmarkType  eq "country"}  class="bookmark_fon" {/if}
{if $bookmarkType  eq "lol"}  class="bookmark_fon" {/if}
{if $bookmarkType  eq "msds"}  class="bookmark_fon" {/if}
{if $bookmarkType  eq "rule"}  class="bookmark_fon" {/if}
{if $bookmarkType  eq "substrate"}  class="bookmark_fon" {/if}
{if $bookmarkType  eq "supplier"}  class="bookmark_fon" {/if}
{if $bookmarkType  eq "type"}  class="bookmark_fon" {/if}
{if $bookmarkType  eq "unittype"}  class="bookmark_fon" {/if}
{if $bookmarkType  eq "formulas"}  class="bookmark_fon" {/if}
{if $bookmarkType  eq "emissionFactor"}  class="bookmark_fon" {/if} >
            <table cellspacing="0" cellpadding="0"height="100%" class="bookmarks_big" style="margin-left:10px">
                <tr>
                    <td>
                        <a href="admin.php?action=browseCategory&categoryID=class&itemID=apmethod">{if $bookmarkType != "apmethod"}
                            <div class="deactiveBookmark_big">
                                <div class="deactiveBookmark_right_big">
                                {else}
                                <div class = "activeBookmark_big">
                                    <div class = "activeBookmark_right_big">
                                        {/if}
                                        AP Method
                                    </div>
                                </div>
                                </a>
                            </td>
                            <td>
                                <a href="admin.php?action=browseCategory&categoryID=class&itemID=coat">{if $bookmarkType != "coat"}
                                    <div class="deactiveBookmark">
                                        <div class="deactiveBookmark_right">
                                        {else}
                                        <div class = "activeBookmark">
                                            <div class = "activeBookmark_right">
                                                {/if}
                                                Coat
                                            </div>
                                        </div>
                                        </a>
                                    </td>
                                    <td>
                                        <a href="admin.php?action=browseCategory&categoryID=class&itemID=product">{if $bookmarkType != "product"}
                                            <div class="deactiveBookmark">
                                                <div class="deactiveBookmark_right">
                                                {else}
                                                <div class="activeBookmark">
                                                    <div class="activeBookmark_right">
                                                        {/if}
                                                        Product
                                                    </div>
                                                </div>
                                                </a>
                                            </td>
                                            <td>
                                                <a href="admin.php?action=browseCategory&categoryID=class&itemID=components">{if $bookmarkType != "components"}
                                                    <div class="deactiveBookmark">
                                                        <div class="deactiveBookmark_right">
                                                        {else}
                                                        <div class = "activeBookmark">
                                                            <div class = "activeBookmark_right">
                                                                {/if}
                                                                Compounds
                                                            </div>
                                                        </div>
                                                        </a>
                                                    </td>
                                                    <td>
                                                        <a href="admin.php?action=browseCategory&categoryID=class&itemID=agency">{if $bookmarkType != "agency"}
                                                            <div class="deactiveBookmark">
                                                                <div class="deactiveBookmark_right">
                                                                {else}
                                                                <div class = "activeBookmark">
                                                                    <div class = "activeBookmark_right">
                                                                        {/if}
                                                                        Agencies
                                                                    </div>
                                                                </div>
                                                                </a>
                                                            </td>
                                                           {* <td>
                                                                <a href="admin.php?action=browseCategory&categoryID=class&itemID=density">{if $bookmarkType != "density"}
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
                                                                        <a href="admin.php?action=browseCategory&categoryID=class&itemID=country">{if $bookmarkType != "country"}
                                                                            <div class="deactiveBookmark">
                                                                                <div class="deactiveBookmark_right">
                                                                                {else}
                                                                                <div class = "activeBookmark">
                                                                                    <div class = "activeBookmark_right">
                                                                                        {/if}
                                                                                        Country
                                                                                    </div>
                                                                                </div>
                                                                                </a>
                                                                            </td>
                                                                     {*       <td>
                                                                                <a href="admin.php?action=browseCategory&categoryID=class&itemID=lol">{if $bookmarkType != "lol"}
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
                                                                                        <a href="admin.php?action=browseCategory&categoryID=class&itemID=msds">{if $bookmarkType != "msds"}
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
                                                                                                <a href="admin.php?action=browseCategory&categoryID=class&itemID=rule">{if $bookmarkType != "rule"}
                                                                                                    <div class="deactiveBookmark">
                                                                                                        <div class="deactiveBookmark_right">
                                                                                                        {else}
                                                                                                        <div class = "activeBookmark">
                                                                                                            <div class = "activeBookmark_right">
                                                                                                                {/if}
                                                                                                                Rule
                                                                                                            </div>
                                                                                                        </div>
                                                                                                        </a>
                                                                                                    </td>
                                                                                                    <td>
                                                                                                        <a href="admin.php?action=browseCategory&categoryID=class&itemID=substrate">{if $bookmarkType != "substrate"}
                                                                                                            <div class="deactiveBookmark">
                                                                                                                <div class="deactiveBookmark_right">
                                                                                                                {else}
                                                                                                                <div class = "activeBookmark">
                                                                                                                    <div class = "activeBookmark_right">
                                                                                                                        {/if}
                                                                                                                        Substrate
                                                                                                                    </div>
                                                                                                                </div>
                                                                                                                </a>
                                                                                                            </td>
                                                                                                            <td>
                                                                                                                <a href="admin.php?action=browseCategory&categoryID=class&itemID=supplier">{if $bookmarkType != "supplier"}
                                                                                                                    <div class="deactiveBookmark">
                                                                                                                        <div class="deactiveBookmark_right">
                                                                                                                        {else}
                                                                                                                        <div class = "activeBookmark">
                                                                                                                            <div class = "activeBookmark_right">
                                                                                                                                {/if}
                                                                                                                                Supplier
                                                                                                                            </div>
                                                                                                                        </div>
                                                                                                                        </a>
                                                                                                                    </td>
                                                                                                                  {*  <td>
                                                                                                                        <a href="admin.php?action=browseCategory&categoryID=class&itemID=type">{if $bookmarkType != "type"}
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
                                                                                                                                <a href="admin.php?action=browseCategory&categoryID=class&itemID=unittype">{if $bookmarkType != "unittype"}
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
                                                                                                                                        <a href="admin.php?action=browseCategory&categoryID=class&itemID=formulas">{if $bookmarkType != "formulas"}
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
                                                                                                                                	<a href="admin.php?action=browseCategory&categoryID=class&itemID=emissionFactor">
                                                                                                                                		{if $bookmarkType != "emissionFactor"}
                                                                                                                                    		<div class="deactiveBookmark_big">
	                                                                                                                                        	<div class="deactiveBookmark_right_big">
                                                                                                                                        {else}
    	                                                                                                                                    <div class = "activeBookmark_big">
        	                                                                                                                                    <div class = "activeBookmark_right_big">
                                                                                                                                        {/if}
                                                                                                                                                E Factor
                                                                                                                                            	</div>
                                                                                                                                        	</div>
                                                                                                                                    </a>
                                                                                                                               </td>
                                                                                                                           {/if}     
                                                                                                                                            <td width="20px">
                                                                                                                                            </td>
                                                                                                                                            <td>
                                                                                                                                            </tr>
                                                                                                                                            <tr height="19">
                                                                                                                                            <td {if $bookmarkType  eq "apmethod"}  class="active_bookmark_fon" {/if} >
                                                                                                                                            </td>
                                                                                                                                        </td>
                                                                                                                                        <td {if $bookmarkType  eq "coat"}  class="active_bookmark_fon" {/if}>
                                                                                                                                        </td>
                                                                                                                                        <td {if $bookmarkType  eq "product"}  class="active_bookmark_fon" {/if}>
                                                                                                                                        </td>
                                                                                                                                        <td {if $bookmarkType  eq "components"}  class="active_bookmark_fon" {/if}>
                                                                                                                                        </td>
                                                                                                                                        <td {if $bookmarkType  eq "agency"}  class="active_bookmark_fon" {/if}>
                                                                                                                                        </td>
                                                                                                                                        
                                                                                                                                        {*<td {if $bookmarkType  eq "density"}  class="active_bookmark_fon" {/if}>*}
                                                                                                                                        
                                                                                                                                        </td>
                                                                                                                                        <td {if $bookmarkType  eq "country"}  class="active_bookmark_fon" {/if}>
                                                                                                                                        </td>
                                                                                                                                        
                                                                                                                                        {*<td {if $bookmarkType  eq "lol"}  class="active_bookmark_fon" {/if}>
                                                                                                                                        </td>
                                                                                                                                        <td {if $bookmarkType  eq "msds"}  class="active_bookmark_fon" {/if}>
                                                                                                                                        </td>*}
                                                                                                                                        
                                                                                                                                        <td {if $bookmarkType  eq "rule"}  class="active_bookmark_fon" {/if}>
                                                                                                                                        </td>
                                                                                                                                        <td {if $bookmarkType  eq "substrate"}  class="active_bookmark_fon" {/if}>
                                                                                                                                        </td>
                                                                                                                                        <td {if $bookmarkType  eq "supplier"}  class="active_bookmark_fon" {/if}>
                                                                                                                                        </td>
                                                                                                                                        
                                                                                                                                        {*<td {if $bookmarkType  eq "type"}  class="active_bookmark_fon" {/if}>
                                                                                                                                        </td>
                                                                                                                                        <td {if $bookmarkType  eq "unittype"}  class="active_bookmark_fon" {/if}>
                                                                                                                                        </td>
                                                                                                                                        <td {if $bookmarkType  eq "formulas"}  class="active_bookmark_fon" {/if}>
                                                                                                                                        </td>*}
                                                                                                                                        
                                                                                                                                        <td {if $bookmarkType  eq "emissionFactor"}  class="active_bookmark_fon" {/if}>
                                                                                                                                        </td>
                                                                                                                                        </tr>
                                                                                                                                    </table>
																																	
                                                                                                                                    </td>
                                                                                                                                    <td
{if $bookmarkType  eq "apmethod"}  class="bookmark_fon" {/if}
{if $bookmarkType  eq "coat"}  class="bookmark_fon" {/if}
{if $bookmarkType  eq "product"}  class="bookmark_fon" {/if}
{if $bookmarkType  eq "components"}  class="bookmark_fon" {/if}
{if $bookmarkType  eq "agency"}  class="bookmark_fon" {/if}
{if $bookmarkType  eq "density"}  class="bookmark_fon" {/if}
{if $bookmarkType  eq "country"}  class="bookmark_fon" {/if}
{if $bookmarkType  eq "lol"}  class="bookmark_fon" {/if}
{if $bookmarkType  eq "msds"}  class="bookmark_fon" {/if}
{if $bookmarkType  eq "rule"}  class="bookmark_fon" {/if}
{if $bookmarkType  eq "substrate"}  class="bookmark_fon" {/if}
{if $bookmarkType  eq "supplier"}  class="bookmark_fon" {/if}
{if $bookmarkType  eq "type"}  class="bookmark_fon" {/if}
{if $bookmarkType  eq "unittype"}  class="bookmark_fon" {/if}
{if $bookmarkType  eq "formulas"}  class="bookmark_fon" {/if}
{if $bookmarkType  eq "emissionFactor"}  class="bookmark_fon" {/if} >
                                                                                                                                    </td>
                                                                                                                                </tr>
                                                                                                                                </table>
                                                                                                                                {}
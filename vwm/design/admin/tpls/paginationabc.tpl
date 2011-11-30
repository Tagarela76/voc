{*WORKING*}
{if $pagination && $pagination->getPageCount() > 1}
{assign var='prev' value=$pagination->getCurrentPage()-2}
{assign var='next' value=$pagination->getCurrentPage()}
{if $pagination->getCurrentPage() <10}	
{assign var='prev1' value=$prev|truncate:1:""}
{assign var='next1' value=$next|truncate:1:""}
{else}
{assign var='prev1' value=$prev|truncate:2:""}
{assign var='next1' value=$next|truncate:2:""}
{/if}
<div class="br_10">
</div>
<table width="98%" align="center" >
<tr>

	<td align="center" class="pagination">	{*<a href="?action=browseCategory&category=pfps&bookmark=pfpLibrary&subBookmark=custom&page={$request.page}" >Custom</a>*}
		{if $pagination->getCurrentPage() != 1}
		<a href="{$pagination->url}&subBookmark=custom&page={$pagination->getCurrentPage()-1}{$abctabs[$prev1]}{if $sort}&sort={$sort}{/if}{if $request.page }&productCategory={$request.productCategory}{/if}" class="NextPrevious">Previous</a>					
		{/if}
		
		{if $pagination->getRangeFirstPage() > 1}
			<a href="{$pagination->url}{if $sort}&sort={$sort}{/if}">{$abctabs[0]|upper}</a>&nbsp;..
		{/if}
					
		{section name=i loop=$pagination->getPageCount()}
		{if $smarty.section.i.index+1 >= $pagination->getRangeFirstPage() && $smarty.section.i.index+1 <= $pagination->getRangeLastPage()}
			{if $pagination->getCurrentPage() eq $smarty.section.i.index+1}
				<b style="color:#4C505B;background:#E3E3E3;">{*$smarty.section.i.index+1*}{$abctabs[i]|upper}</b>
			{else}
				<a href="{$pagination->url}&subBookmark=custom&page={$smarty.section.i.index+1}{$abctabs[i]}{if $sort}&sort={$sort}{/if}{if $request.page }&productCategory={$request.productCategory}{/if}">{*$smarty.section.i.index+1*}{$abctabs[i]|upper}</a>
			{/if}
		{/if}			
		{/section}		
		
		{if $pagination->getRangeLastPage() < $pagination->getPageCount()}
			..&nbsp;<a href="{$pagination->url}&subBookmark=custom&page={$pagination->getPageCount()}{$abctabs[25]}{if $sort}&sort={$sort}{/if}{if $request.page }&productCategory={$request.productCategory}{/if}">{*$pagination->getPageCount()*}{$abctabs[25]|upper}</a>
		{/if}
		
		{if $pagination->getCurrentPage() != $pagination->getPageCount() && $pagination->getPageCount() != 0}			
			<a href="{$pagination->url}&subBookmark=custom&page={$pagination->getCurrentPage()+1}{$abctabs[$next1]}{if $sort}&sort={$sort}{/if}{if $request.page }&productCategory={$request.productCategory}{/if}" class="NextPrevious">Next</a>
		{/if}
	</td>	
</tr>
</table>
<div class="br_10">
</div>
{/if}				
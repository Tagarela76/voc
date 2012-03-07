{*WORKING*}
{if $pagination && $pagination->getPageCount() > 1}
<div class="br_10">
</div>
<table width="98%" align="center" >
<tr>
	<td align="center" class="pagination">	
		{if $pagination->getCurrentPage() != 1}
			<a href="{$pagination->url}&page={$pagination->getCurrentPage()-1}{if $sort}&sort={$sort}{/if}" class="NextPrevious">Previous</a>					
		{/if}
		
		{if $pagination->getRangeFirstPage() > 1}
			<a href="{$pagination->url}{if $sort}&sort={$sort}{/if}">1</a>&nbsp;..
		{/if}
					
		{section name=i loop=$pagination->getPageCount()}
		{if $smarty.section.i.index+1 >= $pagination->getRangeFirstPage() && $smarty.section.i.index+1 <= $pagination->getRangeLastPage()}
			{if $pagination->getCurrentPage() eq $smarty.section.i.index+1}
				<b style="color:#4C505B;background:#E3E3E3;">{$smarty.section.i.index+1}</b>
			{else}
				<a href="{$pagination->url}&page={$smarty.section.i.index+1}{if $sort}&sort={$sort}{/if}">{$smarty.section.i.index+1}</a>
			{/if}
		{/if}			
		{/section}		
		
		{if $pagination->getRangeLastPage() < $pagination->getPageCount()}
			..&nbsp;<a href="{$pagination->url}&page={$pagination->getPageCount()}{if $sort}&sort={$sort}{/if}">{$pagination->getPageCount()}</a>
		{/if}
		
		{if $pagination->getCurrentPage() != $pagination->getPageCount() && $pagination->getPageCount() != 0}			
			<a href="{$pagination->url}&page={$pagination->getCurrentPage()+1}{if $sort}&sort={$sort}{/if}" class="NextPrevious">Next</a>
		{/if}		
	</td>	
</tr>
</table>
<div class="br_10">
</div>
{/if}				
<table cellspacing="0" cellpadding="0" width="100%" style="margin:10px 0 0 0;">
	<tr>
	<td align="left" class="bookmark_fon">
<table cellspacing="0" cellpadding="0" height="100%" class="bookmarks_big" style="margin-left:20px;">
	<tr>
				{*ABC TABS}
				<li> 
					     <a href="admin.php?action=browseCategory&category={$request.category}&bookmark={$request.bookmark}&subBookmark=custom" style="text-decoration: none; color: #333333;">
							{if $request.subBookmark == 'custom'}
								<div  class = "activeBookmark">  <div class = "activeBookmark_right">
									{else}                    
										
										<div class="deactiveBookmark"><div class="deactiveBookmark_right">
										
							{/if}
							custom
							
							</div>
						</div></a>
				</li>	
								
				{section name=i loop=$abctabs}    
					<li> 
					     <a href="admin.php?action=browseCategory&category={$request.category}&bookmark={$request.bookmark}&subBookmark={$abctabs[i]}" style="text-decoration: none; color: #333333;">
							{if $request.subBookmark == $abctabs[i]}
								<div  class = "activeBookmark">  <div class = "activeBookmark_right">
									{else}                    
										
										<div class="deactiveBookmark"><div class="deactiveBookmark_right">
										
							{/if}
							{$abctabs[i]}
							
							</div>
						</div></a>
					</li>
				{/section}	
				{ABC TABS*}
				
				{*BEGIN LIST*}			
				{section name=i loop=$bookmarks}
					<td >
					     <a href="admin.php?action=browseCategory&category={$request.category}&bookmark={$request.bookmark}&subBookmark={$bookmarks[i].supplier_id}&letterpage={$request.letterpage}{if $request.productCategory }&productCategory={$request.productCategory}{/if}" style="text-decoration: none; color: #333333;">
							{if $request.subBookmark == $bookmarks[i].supplier_id}
								<div  class = "activeBookmark">  <div class = "activeBookmark_right">
									{else}                    
										
										<div class="deactiveBookmark"><div class="deactiveBookmark_right">
										
							{/if}
							{$bookmarks[i].supplier|lower}
							
							</div>
						</div></a>
					</td>
				{/section}	
				{*END LIST*}
				
				{*
				<li>  
					<a href="admin.php?action=browseCategory&category={$request.category}&bookmark=apmethod" style="text-decoration: none; color: #333333;">
						{if $request.bookmark != "apmethod"}	
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
				</li>
				*}

	</tr>
	<tr height="19">
		<td></td>
	</tr>
</table>																															
</td>
</tr>
<input type="hidden" name="hiddenSelectedBookmark" id="selectedBookmark" value="{$selectedBookmark}"/>
</table>

{include file="tpls:tpls/productTypesDropDown.tpl"}
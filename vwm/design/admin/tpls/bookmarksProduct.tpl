<table cellspacing="0" cellpadding="0" width="100%" style="margin:10px 0 0 0;">
	<tr>
	<td align="left" class="bookmark_fon">
<table cellspacing="0" cellpadding="0" height="100%" class="bookmarks_big" style="margin-left:20px;">
	<tr>

				{*BEGIN LIST*}
				{section name=i loop=$bookmarks}
					<td >
					     <a href="admin.php?action=browseCategory&category={$request.category}&subBookmark={$bookmarks[i].supplier_id}&letterpage={$request.letterpage}{if $request.productCategory }&productCategory={$request.productCategory}{/if}" style="text-decoration: none; color: #333333;">
							{if $request.subBookmark == $bookmarks[i].supplier_id}
								<div class = "activeBookmark_right_css">
									{else}

										<div class="deactiveBookmark_right_css">

							{/if}
							{$bookmarks[i].supplier|lower}{if $request.subBookmark == $bookmarks[i].supplier_id} &nbsp({$productCount}){/if}

							
						</div></a>
					</td>
				{/section}
				{*END LIST*}


	</tr>
	<tr height="19">
		<td></td>
	</tr>
</table>
</td>
</tr>
<input type="hidden" name="hiddenSelectedBookmark" id="selectedBookmark" value="{$selectedBookmark}"/>
</table>

{*include file="tpls:tpls/productTypesDropDown.tpl" category=$request.category bookmark=$request.bookmark*}

{*PAGINATION*}
		{include file="tpls:tpls/paginationabc.tpl"}
{*/PAGINATION*}
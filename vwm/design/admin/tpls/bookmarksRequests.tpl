<link href="style.css" rel="stylesheet" type="text/css">
<table cellspacing="0" cellpadding="0" width="100%" style="margin:10px 0 0 0">
    <tr>
        <td align="right"
		{if $request.bookmark eq "productRequest"}  class="bookmark_fon" {/if}
		{if $request.bookmark eq "userRequest"}  class="bookmark_fon" {/if}
		{if $request.bookmark eq "setupRequest"}  class="bookmark_fon" {/if}>
	<table cellspacing="0" cellpadding="0"height="100%" class="bookmarks_big" style="margin-left:10px">
		<tr>
			<td>
				<a href="admin.php?action=browseCategory&category={$request.category}&bookmark=productRequest">
					{if $request.bookmark != "productRequest"}
						<div class="deactiveBookmark_big">
							<div class="deactiveBookmark_right_big">
                            {else}
                                <div class = "activeBookmark_big">
									<div class = "activeBookmark_right_big">
									{/if}
									Product&nbsp;Request
								</div>
                            </div>
						</div>
					</div>
				</a>
			</td>
			<td>
				<a href="admin.php?action=browseCategory&category={$request.category}&bookmark=userRequest">
					{if $request.bookmark != "userRequest"}
						<div class="deactiveBookmark">
							<div class="deactiveBookmark_right">
							{else}
								<div class = "activeBookmark">
									<div class = "activeBookmark_right">
									{/if}
									User&nbsp;Request
								</div>
							</div>
						</div>
					</div>
				</a>
			</td>
			<td>
				<a href="admin.php?action=browseCategory&category={$request.category}&bookmark=setupRequest">
					{if $request.bookmark != "setupRequest"}
						<div class="deactiveBookmark">
							<div class="deactiveBookmark_right">
							{else}
								<div class = "activeBookmark">
									<div class = "activeBookmark_right">
									{/if}
									Setup&nbsp;Request
								</div>
							</div>
						</div>
					</div>
				</a>
			</td>
			<td width="20px">
			</td>
			
		</tr>
		<tr height="19">
			<td {if $request.bookmark  eq "productRequest"}  class="active_bookmark_fon" {/if} >
			</td>
			<td {if $request.bookmark  eq "userRequest"}  class="active_bookmark_fon" {/if}>
			</td>
			<td {if $request.bookmark  eq "setupRequest"}  class="active_bookmark_fon" {/if}>
			</td>
		</tr>
	</table>
</td>
<td
{if $request.bookmark  eq "productRequest"}  class="bookmark_fon" {/if}
{if $request.bookmark  eq "userRequest"}  class="bookmark_fon" {/if}
{if $request.bookmark  eq "setupRequest"}  class="bookmark_fon" {/if}>
</td>
</tr>
</table>
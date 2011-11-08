<link href="style.css" rel="stylesheet" type="text/css">
<table cellspacing="0" cellpadding="0" width="100%" style="margin:10px 0 0 0">
    <tr>
        <td align="right"
		{if $request.bookmark eq "userRequest"}  class="bookmark_fon" {/if}
		{if $request.bookmark eq "companyRequest"}  class="bookmark_fon" {/if}>
	<table cellspacing="0" cellpadding="0"height="100%" class="bookmarks_big" style="margin-left:10px">
		<tr>
			<td>
				<a href="sales.php?action=browseCategory&category={$request.category}&bookmark=userRequest">
					{if $request.bookmark != "userRequest"}
						<div class="deactiveBookmark_big">
							<div class="deactiveBookmark_right_big">
                            {else}
                                <div class = "activeBookmark_big">
									<div class = "activeBookmark_right_big">
									{/if}
									User&nbsp;Request
								</div>
                            </div>
						</div>
					</div>
				</a>
			</td>
			<td>
				<a href="sales.php?action=browseCategory&category={$request.category}&bookmark=companyRequest">
					{if $request.bookmark != "companyRequest"}
						<div class="deactiveBookmark">
							<div class="deactiveBookmark_right">
							{else}
								<div class = "activeBookmark">
									<div class = "activeBookmark_right">
									{/if}
									Company&nbsp;Request
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
			<td {if $request.bookmark  eq "userRequest"}  class="active_bookmark_fon" {/if} >
			</td>
			<td {if $request.bookmark  eq "companyRequest"}  class="active_bookmark_fon" {/if}>
			</td>
		</tr>
	</table>
</td>
<td
{if $request.bookmark  eq "companyRequest"}  class="bookmark_fon" {/if}
{if $request.bookmark  eq "userRequest"}  class="bookmark_fon" {/if}>
</td>
</tr>
</table>
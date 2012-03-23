<link href="style.css" rel="stylesheet" type="text/css">
<table cellspacing="0" cellpadding="0" width="100%" style="margin:10px 0 0 0">
    <tr>
        <td align="right" 
					{if $request.bookmark  eq "clients"}  class="active_bookmark_fon" {/if}
			
			{if $request.bookmark  eq "orders"}  class="active_bookmark_green_fon" {/if}
			
			{if $request.bookmark  eq "products"}  class="active_bookmark_violet_fon" {/if}
			
			{if $request.bookmark  eq "gom"}  class="active_bookmark_fon" {/if}
			>
	<table cellspacing="0" cellpadding="0"height="100%" class="bookmarks_big" style="margin-left:10px">
		<tr>
			<td>
				<a href="supplier.php?action=browseCategory&category={$request.category}&bookmark=clients{if $request.jobberID}&jobberID={$request.jobberID}{/if}{if $request.supplierID}&supplierID={$request.supplierID}{/if}">
					{if $request.bookmark != "clients"}
						<div class="deactiveBookmark">
							<div class="deactiveBookmark_right">
                            {else}
                                <div class = "activeBookmark_big">
									<div class = "activeBookmark_right_big">
									{/if}
									Clients
								</div>
                            </div>
						</div>
					</div>
				</a>
			</td>
			<td>
				<a href="supplier.php?action=browseCategory&category={$request.category}&bookmark=orders&tab=products{if $request.jobberID}&jobberID={$request.jobberID}{/if}{if $request.supplierID}&supplierID={$request.supplierID}{/if}">
					{if $request.bookmark != "orders"}
						<div class="deactiveBookmark">
							<div class="deactiveBookmark_right">
							{else}
								<div class = "activeBookmark_green">
									<div class = "activeBookmark_green_right">
									{/if}
									Orders
								</div>
							</div>
						</div>
					</div>
				</a>
			</td>
			<td>
				<a href="supplier.php?action=browseCategory&category={$request.category}&bookmark=products{if $request.jobberID}&jobberID={$request.jobberID}{/if}{if $request.supplierID}&supplierID={$request.supplierID}{/if}">
					{if $request.bookmark != "products"}
						<div class="deactiveBookmark">
							<div class="deactiveBookmark_right">
							{else}
								<div class = "activeBookmark_violet">
									<div class = "activeBookmark_violet_right">
									{/if}
									Products
								</div>
							</div>
						</div>
					</div>
				</a>
			</td>	
			
			<td>
				<a href="supplier.php?action=browseCategory&category={$request.category}&bookmark=gom{if $request.jobberID}&jobberID={$request.jobberID}{/if}{if $request.supplierID}&supplierID={$request.supplierID}{/if}">
					{if $request.bookmark != "gom"}
						<div class="deactiveBookmark">
							<div class="deactiveBookmark_right">
                            {else}
                                <div class = "activeBookmark_big">
									<div class = "activeBookmark_right_big">
									{/if}
									GOM
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
			<td {if $request.bookmark  eq "clients"}  class="active_bookmark_fon" {/if} >
			</td>
			<td {if $request.bookmark  eq "orders"}  class="active_bookmark_green_fon" {/if}>
			</td>
			<td {if $request.bookmark  eq "products"}  class="active_bookmark_violet_fon" {/if}>
			</td>			
			<td {if $request.bookmark  eq "gom"}  class="active_bookmark_fon" {/if}>
			</td>	
			<td width="20px">
			</td>
		</tr>
	</table>
</td>
<td {if $request.bookmark  eq "clients"}  class="active_bookmark_fon" {/if}
	{if $request.bookmark  eq "orders"}  class="active_bookmark_green_fon" {/if}
	{if $request.bookmark  eq "products"}  class="active_bookmark_violet_fon" {/if}
	{if $request.bookmark  eq "gom"}  class="active_bookmark_fon" {/if}>
</td>
</tr>
</table>
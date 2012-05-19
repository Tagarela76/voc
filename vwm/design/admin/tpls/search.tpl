{if $overrideAction}
	{assign var="formAction" value=$overrideAction}
{else}	
	{assign var="formAction" value="browseCategory"}
{/if}

<div style="text-align:center;">
	{*thank you, google*}
	<form>	
		<table align="center">
			<tbody>
				<tr>
					<td><input type="text" name="q" id="search" style="width:410px;border:1px solid #D3D3D3;height:20px" value="{$searchQuery}"/></td>
					<td><input type="submit" id="goSearch" class="button" value="Search{if $request.bookmark == "industryType" || 
																							$request.bookmark == "industrySubType"}{else}&nbsp;{$request.bookmark}{/if}"></td>
					<td>
						<input type="hidden" name="action" value="{$formAction}">
						<input type="hidden" name="category" value="{$request.category}">
						{if $request.bookmark}<input type="hidden" name="bookmark" value="{$request.bookmark}">{/if}
						{if $request.subBookmark}<input type="hidden" name="subBookmark" value="{$request.subBookmark}">{/if}
						{if $request.letterpage}<input type="hidden" name="letterpage" value="{$request.letterpage}"></input>
                                                {if ($request.category eq "salescontacts")&($request.subBookmark != "")}{/if}
                                                <input type="hidden" name="subBookmark" value="{$request.subBookmark}">   
                                                {/if}
												
						{if $request.productID}<input type="hidden" name="productID" value="{$request.productID}"></input>{/if}
						{if $request.productPage}<input type="hidden" name="productPage" value="{$request.productPage}"></input>{/if}
					</td>
				</tr>
			</tbody>									
		</table>		
		<div class="padd2"></div>
	</form>
</div>
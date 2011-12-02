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
						<input type="hidden" name="action" value="browseCategory">
						<input type="hidden" name="category" value="{$request.category}">
						<input type="hidden" name="bookmark" value="{$request.bookmark}">
						<input type="hidden" name="subBookmark" value="{$request.subBookmark}">
						<input type="hidden" name="letterpage" value="{$request.letterpage}"></input>
                                                {if ($request.category eq "salescontacts")&($request.subBookmark != "")}
                                                <input type="hidden" name="subBookmark" value="{$request.subBookmark}">   
                                                {/if}
					</td>
				</tr>
			</tbody>									
		</table>		
		<div class="padd2"></div>
	</form>
</div>
<div style="text-align:center;">
	{*thank you, google*}
	<form>	
		<table align="center">
			<tbody>
				<tr>
					<td><input type="text" name="q" id="search" style="width:410px;border:1px solid #D3D3D3;height:20px" value="{$searchQuery}"/></td>
					<td><input type="submit" id="goSearch" class="button" value="Search {$bookmarkType}"></td>
					<td>
						<input type="hidden" name="action" value="browseCategory">
						<input type="hidden" name="categoryID" value="{$categoryType}">
						<input type="hidden" name="itemID" value="{$bookmarkType}">
					</td>
				</tr>
			</tbody>									
		</table>		
		<div class="padd2"></div>
	</form>
</div>
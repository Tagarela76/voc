<div style="text-align:center;">
	{*thank you, google*}
	<form>
		<table align="center">
			<tbody>
				<tr>
					<td><input type="text" name="q" id="search" style="width:410px;border:1px solid #D3D3D3;height:20px" value="{$searchQuery}"/></td>
					<td><input type="submit" id="goSearch" class="button" value="Search"></td>
					<td>
						<input type="hidden" name="action" value="{$request.action}">
						<input type="hidden" name="category" value="{$request.category}">
						<input type="hidden" name="id" value="{$request.id}">
						<input type="hidden" name="bookmark" value="{$childCategory}">
						{if $request.tab}<input type="hidden" name="tab" value="{$request.tab}">{/if}
						<input type="hidden" name="searchAction" value="search">
						{if $request.category == "pfpTypes"} 
							<input type="hidden" name="facilityID" value="{$request.facilityID}">
							<input type="hidden" name="pfpGroup" value="{$request.pfpGroup}">
						{/if}	
					</td>
				</tr>
			</tbody>
		</table>
		<div class="padd2"></div>
	</form>
</div>
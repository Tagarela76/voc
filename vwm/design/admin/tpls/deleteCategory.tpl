	{if $color eq "green"}
		{include file="tpls:tpls/notify/greenNotify.tpl" text=$message}
	{/if}
	{if $color eq "orange"}
		{include file="tpls:tpls/notify/orangeNotify.tpl" text=$message}
	{/if}
	{if $color eq "blue"}
		{include file="tpls:tpls/notify/blueNotify.tpl" text=$message}
	{/if}



<form method="get" action="">
	  {*shadow*}
	<div class="shadow">
	<div class="shadow_top">
	<div class="shadow_bottom">
	 {**}
	 <div class="br_10"></div>
	
	                        <table class="users_min"  align="center" cellspacing="0" cellpadding="0">
                          <tr class="users_top_red" height="27">
<td  class="users_u_top_red"  width="100%" colspan="2">{$itemForDelete[0].name}</td>
<td  class="users_u_top_r_red"   width="10px" colspan="2">&nbsp;</td>

				
						</tr>
						 
						 
						 
					 
					
						
						 <tr height="35px">

<td class="border_users_l" width="35%">
ID number

</td>

 <td class="border_users_r"colspan="3">
        
			           <div style="width:100%;" >
					         {$itemForDelete[0].id}
					    </div >
			 
</td>
</tr>

{if $itemForDelete[0].links}
	{if $itemForDelete[0].links.productCnt}
	<tr height="25px">
		<td class="border_users_l" width="35%">
			Total linked products
		</td>
 		<td class="border_users_r"colspan="3">        
 			<div style="width:100%;" >
				{$itemForDelete[0].links.productCnt}
			</div >	 
		</td>
	</tr>	
	{/if}
<tr height="25px">
	<td class="border_users_l" width="35%">
		Total linked inventories
	</td>
 	<td class="border_users_r" colspan="3">        
 		<div style="width:100%;" >
			{$itemForDelete[0].links.inventoryCnt}
		</div >	 
	</td>
</tr>
<tr height="25px">
	<td class="border_users_l" width="35%">
		Total linked equipments
	</td>
 	<td class="border_users_r" colspan="3">        
 		<div style="width:100%;" >
			{$itemForDelete[0].links.equipmentCnt}
		</div >	 
	</td>
</tr>
<tr height="25px">
	<td class="border_users_l" width="35%">
		Total linked mixes
	</td>
 	<td class="border_users_r" colspan="3">        
 		<div style="width:100%;" >
			{$itemForDelete[0].links.mixCnt}
		</div >	 
	</td>
</tr>
{/if}

						<tr>
						 <td height="20" class="users_u_bottom " colspan="2">&nbsp;</td>
						 <td height="20" class="users_u_bottom_r "> &nbsp;  </td>
						</tr>
						</table>
  {*shadow*}
	</div>
	</div>
	</div>
	 {**}
<div align="right" style="margin:7px 50px 0px 7px;">
{*if $itemsCount > 0*}

{if $gobackAction=="viewDetails"}
<input type="button" value="No" class="button" onclick="location.href='admin.php?action=viewDetails&category={$request.category}{if $request.bookmark}&bookmark={$request.bookmark}{/if}&id={$itemForDelete[0].id}'">
{else}
<input type="button" value="No" class="button" onclick="location.href='admin.php?action=browseCategory&category={if $request.category == 'pfpLibrary'}{$request.bookmark}{else}{$request.category}{/if}{if $request.bookmark}&bookmark={if $request.category == 'pfpLibrary'}{$request.category}{else}{$request.bookmark}{/if}{if $request.subBookmark}&subBookmark={$request.subBookmark}{/if}{if $request.page}&page={$request.page}{/if}{/if}'"/>
{*<input type="button" value="No" class="button" onclick="location.href='admin.php?action=browseCategory&category=tables&bookmark={$request.category}'"/>*}
{/if}
<input type="submit" name="confirm" value="Yes" class="button">

{*else*}
{*<input type="submit" name="confirm" value="Ok" class="sub70">*}

{*/if*}
<input type="hidden" name="itemsCount" value="{$itemsCount}">
<input type="hidden" name="bookmark" value="{$request.bookmark}">
<input type="hidden" name="subBookmark" value="{$request.subBookmark}">
<input type="hidden" name="ID" value="{$ID}">
<input type="hidden" name="category" value="{$request.category}">
<input type="hidden" name="action" value="confirmDelete">
<input type="hidden" value="{$itemForDelete[0].id}" name="item_0">
</div>

</form>
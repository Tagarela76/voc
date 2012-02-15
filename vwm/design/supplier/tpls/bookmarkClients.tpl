
	{if $color eq "green"}
		{include file="tpls:tpls/notify/greenNotify.tpl" text=$message}
	{/if}
	{if $color eq "orange"}
		{include file="tpls:tpls/notify/orangeNotify.tpl" text=$message}
	{/if}
	{if $color eq "blue"}
		{include file="tpls:tpls/notify/blueNotify.tpl" text=$message}
	{/if}
	{if $color eq "blue2" && $itemsCount == 0}
		{include file="tpls:tpls/notify/blueNotify.tpl" text=$message}
	{/if}

<div class="padd7" align="center">
{*PAGINATION*}
		{include file="tpls:tpls/pagination.tpl"}
{*/PAGINATION*}
	<table  class="users" width="100%" cellspacing="0" cellpadding="0" bgcolor="#EFEFEF">
		<thead>	
		<tr  class="users_u_top_size users_top_blue">
			<td  class="users_u_top_blue"  width="5%" ><span style='display:inline-block; width:60px;'> <a onclick="CheckAll(this)" style='color:white'>All</a>/<a style='color:white' onclick="unCheckAll(this)" >None</a></span></td>
			<td  class="">Company ID</td>
			<td  class="">
				<a style='color:white;' onclick='$("#sort").attr("value","{if $sort==1}2{else}1{/if}"); $("#sortForm").submit();'>
		            <div style='width:100%;  color:white;'>					
						Company {if $sort==1 || $sort==2}<img src="{if $sort==1}images/asc2.gif{/if}{if $sort==2}images/desc2.gif{/if}" alt=""/>{/if}
					</div>
				</a>
			</td>
			<td  class="users_u_top_r_blue">
				<a style='color:white;' onclick='$("#sort").attr("value","{if $sort==3}4{else}3{/if}"); $("#sortForm").submit();'>
		            <div style='width:100%;  color:white;'>						
		                Discount {if $sort==3 || $sort==4}<img src="{if $sort==3}images/asc2.gif{/if}{if $sort==4}images/desc2.gif{/if}" alt=""/>{/if}				
					</div>					
				</a> 
			</td>
			

		</tr>
		</thead>
		
		<tbody>
{if $clients > 0}						 

{*BEGIN LIST*}				
{section name=i loop=$clients}	
	<tr class="hov_company">

			<td class="border_users_l border_users_b">
				<input type="checkbox"  value="{$clients[i].company_id}" name="item_{$smarty.section.i.index}" onclick="return CheckCB(this);">
			</td>
			
			<td class="border_users_b border_users_l" >
				<a href="{$clients[i].url}{if $page}&page={$page}{/if}{if $request.subBookmark}&subBookmark={$request.subBookmark}{/if}"><div style="width:100%;">{$clients[i].company_id}</div ></a>
			</td>
			
            <td class="border_users_b border_users_l" >
				<a href="{$clients[i].url}{if $page}&page={$page}{/if}{if $request.subBookmark}&subBookmark={$request.subBookmark}{/if}"><div style="width:100%;">{$clients[i].name} >> {$clients[i].fname}</div ></a>
			</td>
			
			<td class="border_users_b border_users_l border_users_r" >
				<a href="{$clients[i].url}{if $page}&page={$page}{/if}{if $request.subBookmark}&subBookmark={$request.subBookmark}{/if}"><div style="width:100%;">{$clients[i].discount} %</div ></a>
			</td>	
		
	</tr>
{/section}		 
							
{*END LIST*}

{else}

{*BEGIN	EMPTY LIST*}
		<tr class="">
		    <td  class="border_users_l border_users_r" style='text-align:center; vertical-align:middle;' colspan="4" >No Clients</td>
		</tr>
{*END	EMPTY LIST*}

{/if}
		</tbody>
		
		<tfoot>
		<tr>
			  <td class="users_u_bottom"></td>
        	  <td colspan="4" height="30" class="users_u_bottom_r"></td>
		</tr>	
		</tfoot>		
	</table>
	{*PAGINATION*}
		{include file="tpls:tpls/pagination.tpl"}
	{*/PAGINATION*}
</div>


</form>



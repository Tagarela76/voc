<div class="padd7" align="center">
{*PAGINATION*}
		{include file="tpls:tpls/pagination.tpl"}
{*/PAGINATION*}
	<table  class="users" width="100%" cellspacing="0" cellpadding="0" bgcolor="#EFEFEF">
		<thead>	
		<tr  class="users_u_top_size users_top_violet">
			<td  class="users_u_top_violet"  width="5%" ><span style='display:inline-block; width:60px;'> <a onclick="CheckAll(this)" style='color:white'>All</a>/<a style='color:white' onclick="unCheckAll(this)" >None</a></span></td>
			<td  class="">Product ID</td>
			<td  class="">
				<a style='color:white;' onclick='$("#sort").attr("value","{if $sort==1}2{else}1{/if}"); $("#sortForm").submit();'>
		            <div style='width:100%;  color:white;'>					
						Product {if $sort==1 || $sort==2}<img src="{if $sort==1}images/asc2.gif{/if}{if $sort==2}images/desc2.gif{/if}" alt=""/>{/if}
					</div>
				</a>
			</td>
			<td  class="">
				<a style='color:white;' onclick='$("#sort").attr("value","{if $sort==3}4{else}3{/if}"); $("#sortForm").submit();'>
		            <div style='width:100%;  color:white;'>						
		                Price {if $sort==3 || $sort==4}<img src="{if $sort==3}images/asc2.gif{/if}{if $sort==4}images/desc2.gif{/if}" alt=""/>{/if}				
					</div>					
				</a> 
			</td>
			<td  class="users_u_top_r_violet">
				
		            <div style='width:100%;  color:white;'>						
		                Clients 		
					</div>					
				
			</td>			

		</tr>
		</thead>
		
		<tbody>
{if $products > 0}						 

{*BEGIN LIST*}				
{foreach from=$products item=product} 
	<tr class="hov_company">

			<td class="border_users_l border_users_b">
				<input type="checkbox"  value="{$product->product_id}" name="item_{$smarty.foreach.i.index}" onclick="return CheckCB(this);">
			</td>
			
			<td class="border_users_b border_users_l" >
				<a href="{$product->url}{if $page}&page={$page}{/if}{if $request.subBookmark}&subBookmark={$request.subBookmark}{/if}"><div style="width:100%;">{$product->product_id}</div ></a>
			</td>
			
            <td class="border_users_b border_users_l" >
				<a href="{$product->url}{if $page}&page={$page}{/if}{if $request.subBookmark}&subBookmark={$request.subBookmark}{/if}"><div style="width:100%;">{$product->product_nr}</div ></a>
			</td>
			
			<td class="border_users_b border_users_l" >
				<a href="{$product->url}{if $page}&page={$page}{/if}{if $request.subBookmark}&subBookmark={$request.subBookmark}{/if}"><div style="width:100%;">$ {$product->price}</div ></a>
			</td>
			
			<td class="border_users_b border_users_l border_users_r" >
				<a href="{$product->url}{if $page}&page={$page}{/if}{if $request.subBookmark}&subBookmark={$request.subBookmark}{/if}" style="text-decoration: none;"><div style="width:100%;">
					{foreach from=$comapnyList item=comapnyArr}	
						{foreach from=$comapnyArr item=comapny}	
							{if $product->product_id == $comapny.product_id}{$comapny.name}. {/if}
						{/foreach}		
					{/foreach}
					</div ></a>
			</td>			
		
	</tr>
{/foreach}		 
							
{*END LIST*}

{else}

{*BEGIN	EMPTY LIST*}
		<tr class="">
		    <td  class="border_users_l border_users_r" style='text-align:center; vertical-align:middle;' colspan="5" >No products</td>
		</tr>
{*END	EMPTY LIST*}

{/if}
		</tbody>
		
		<tfoot>
		<tr>
			  <td class="users_u_bottom"></td>
        	  <td colspan="5" height="30" class="users_u_bottom_r"></td>
		</tr>	
		</tfoot>		
	</table>
	{*PAGINATION*}
		{include file="tpls:tpls/pagination.tpl"}
	{*/PAGINATION*}
</div>


</form>



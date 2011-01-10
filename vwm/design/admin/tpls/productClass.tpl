{include file="tpls:tpls/pagination.tpl"}

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
	<table class="users" width="100%" cellspacing="0" cellpadding="0">
		<thead>
		<tr class="users_u_top_size users_top_blue">
			<td  class="users_u_top_blue"  width="5%"><span style='display:inline-block; width:60px;'> <a onclick="CheckAll(this)" style='color:white'>All</a>/<a style='color:white' onclick="unCheckAll(this)" >None</a></span></td>
			<td>
				<a style='color:white;' onclick='$("#sort").attr("value","{if $sort==1}2{else}1{/if}"); $("#sortForm").submit();'>
		            <div style='width:100%;  color:white;'>						
		                ID Number 		
						{if $sort==1 || $sort==2}<img src="{if $sort==1}images/asc2.gif{/if}{if $sort==2}images/desc2.gif{/if}" alt=""/>{/if}				
					</div>
				</a>	
			</td>
			<td>
				<a style='color:white;' onclick='$("#sort").attr("value","{if $sort==3}4{else}3{/if}"); $("#sortForm").submit();'>
		            <div style='width:100%;  color:white;'>						
		                Product No 
						{if $sort==3 || $sort==4}<img src="{if $sort==3}images/asc2.gif{/if}{if $sort==4}images/desc2.gif{/if}" alt=""/>{/if}				
					</div>					
				</a> 
			</td>
			<td>
				<a style='color:white;' onclick='$("#sort").attr("value","{if $sort==5}6{else}5{/if}"); $("#sortForm").submit();'>
		            <div style='width:100%;  color:white;'>						
		                Product description 
						{if $sort==5 || $sort==6}<img src="{if $sort==5}images/asc2.gif{/if}{if $sort==6}images/desc2.gif{/if}" alt=""/>{/if}				
					</div>					
				</a>   
			</td>
			<td>
				<a style='color:white;' onclick='$("#sort").attr("value","{if $sort==7}8{else}7{/if}"); $("#sortForm").submit();'>
		            <div style='width:100%;  color:white;'>						
		                Type 	
						{if $sort==7 || $sort==8}<img src="{if $sort==7}images/asc2.gif{/if}{if $sort==8}images/desc2.gif{/if}" alt=""/>{/if}				
					</div>					
				</a>  
			</td>
			<td  class="users_u_top_r_blue">MSDS</td>
		</tr>
		</thead>
	
		<tbody>			 
{if $itemsCount > 0}						 
						 
{*BEGIN LIST*}						 
{section name=i loop=$category}												
		<tr class="hov_company">
			<td class="border_users_l border_users_b">
 				<input type="checkbox"  value="{$category[i].product_id}" name="item_{$smarty.section.i.index}">
 			</td>
 			
 			<td class="border_users_b border_users_l">
 				<a href="{$category[i].url}" ><div style="width:100%;">{$category[i].product_id}</div ></a>
			</td>
			
			<td class="border_users_b border_users_l">
             	<a href="{$category[i].url}"><div style="width:100%;">{$category[i].product_nr}</div ></a>
			</td>

			<td class="border_users_b border_users_l">
				<a href="{$category[i].url}"><div style="width:100%;">{$category[i].name}</div ></a>
			</td>
			
			<td class="border_users_b border_users_l">
				<a href="{$category[i].url}"><div style="width:100%;">{$category[i].coating}</div ></a>
			</td>
			
			<td class="border_users_b border_users_l border_users_r">
				{if $category[i].msdsLink}
					<a href="{$category[i].msdsLink}"><div style="width:100%;">view</div ></a>
				{else}
					&nbsp;
				{/if}
			</td>
		</tr>
{/section}				
{*END LIST*}

{else}

{*BEGIN	EMPTY LIST*}
		<tr align='center'>			
			<td colspan="6" class="border_users_l border_users_r" >No products</td>
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
</div>


</form>

{include file="tpls:tpls/pagination.tpl"}
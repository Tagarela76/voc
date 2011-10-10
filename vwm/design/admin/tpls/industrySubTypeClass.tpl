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
				<div style='width:100%;  color:white;'>						
		                ID Number 		
				</div>
			</td>
			<td>
				<div style='width:100%;  color:white;'>						
		                Industry Sub-Category  
				</div>					
			</td>
			<td class="users_u_top_r_blue">
				<div style='width:100%;  color:white;'>						
		                Industry Type 
				</div>					
			</td>
		</tr>
		</thead>
	
		<tbody>			 
{if $itemsCount > 0}						 
						 
{*BEGIN LIST*}						 
{*section name=i loop=$category*}
{foreach from=$allSubTypes item=subType key=i}	
		<tr class="hov_company">
			<td class="border_users_l border_users_b">
 				<input type="checkbox"  value="{$subType.id}" name="item_{$i}">
 			</td>
			
			<td class="border_users_b border_users_l">
				<a href="{$subType.url}"><div style="width:100%;">{$subType.id}</div ></a>
			</td>
 			
 			<td class="border_users_b border_users_l">
 				<a href="{$subType.url}" ><div style="width:100%;">{$subType.type}</div ></a>
			</td>
			
			<td class="border_users_b border_users_l border_users_r">
             	<a href="{$subType.url}"><div style="width:100%;">{$subType.parentType}</div ></a>
			</td>
		</tr>
{/foreach}		
{*/section*}				
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
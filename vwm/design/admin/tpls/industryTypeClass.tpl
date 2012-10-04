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
{*PAGINATION*}
    {include file="tpls:tpls/pagination.tpl"}
{*/PAGINATION*}
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
			<td  class="users_u_top_r_blue">
				<div style='width:100%;  color:white;'>						
					Industry Type 
				</div>					
			</td>
		</tr>
		</thead>
	
		<tbody>			 
{if $allTypes|@count > 0}						 
			
{foreach from=$allTypes item=type key=i}
	<tr class="hov_company">
		<td class="border_users_l border_users_b">
			<input type="checkbox"  value="{$type->id}" name="item_{$i}">
		</td>
		<td class="border_users_b border_users_l">
			<a href="{$type->url}"><div style="width:100%;">{$type->id}</div></a>
		</td>
		<td class="border_users_b border_users_l border_users_r">
           	<a href="{$type->url}"><div style="width:100%;">{$type->type}</div></a>
		</td>
	</tr>
{/foreach}
{*END LIST*}

{else}

{*BEGIN	EMPTY LIST*}
		<tr align='center'>			
			<td colspan="3" class="border_users_l border_users_r" >No Types</td>
		</tr>
{*END	EMPTY LIST*}

{/if}
		</tbody>
		
		<tfoot>
		<tr>
			 <td class="users_u_bottom"></td>
        	 <td colspan="3" height="30" class="users_u_bottom_r"></td>
		</tr>
		</tfoot>
	</table>
</div>


</form>

{include file="tpls:tpls/pagination.tpl"}
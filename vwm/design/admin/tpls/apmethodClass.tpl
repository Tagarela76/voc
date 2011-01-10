
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
			<td  class="">ID Number</td>
			<td  class="users_u_top_r_blue">AP Method description</td>				
		</tr>
		</thead>
		
		<tbody>
{if $itemsCount > 0}						 

{*BEGIN LIST*}						 
{section name=i loop=$category}						
						
		 <tr class="hov_company">
			<td class="border_users_l border_users_b">
				<input type="checkbox"  value="{$category[i].apmethod_id}" name="item_{$smarty.section.i.index}" onclick="return CheckCB(this);">
			</td>
            <td class="border_users_b border_users_l" >
				<a href="{$category[i].url}"><div style="width:100%;">{$category[i].apmethod_id}</div ></a>
			</td>
            <td class="border_users_b border_users_l border_users_r">
				<a href="{$category[i].url}"><div style="width:100%;">{$category[i].description}</div ></a>
			</td>				
		</tr>
{/section}								
{*END LIST*}

{else}

{*BEGIN	EMPTY LIST*}
		<tr class="">
		    <td  class="border_users_l border_users_r" colspan="3" >No AP Methods</td>
		</tr>
{*END	EMPTY LIST*}

{/if}
		</tbody>
		
		<tfoot>
		<tr>
			  <td class="users_u_bottom"></td>
        	  <td colspan="2" height="30" class="users_u_bottom_r"></td>
		</tr>	
		</tfoot>		
	</table>
	{*PAGINATION*}
		{include file="tpls:tpls/pagination.tpl"}
	{*/PAGINATION*}
</div>


</form>
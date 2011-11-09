
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
	
	<table  class="users"  cellspacing="0" cellpadding="0">
           <tr   
	  {if $request.bookmark eq "company"} class="users_top"  {/if}
	  {if $request.bookmark eq "facility"} class="users_top_green"  {/if}
	  {if $request.bookmark eq "department"} class="users_top_violet" {/if}
	  {if $request.bookmark eq "admin"} class="users_top_blue"  {/if} 
	  {if $request.bookmark eq "sales"} class="users_top_yellowgreen"  {/if} 
		   height="27" bgcolor="#ecb57f">
		   
		   
				<td width="1%" 
	  {if $request.bookmark eq "company"}class="users_u_top"  {/if}
	  {if $request.bookmark eq "facility"} class="users_u_top_green"  {/if}
	  {if $request.bookmark eq "department"} class="users_u_top_violet"  {/if}
	  {if $request.bookmark eq "admin"} class="users_u_top_blue"   {/if} 
	  {if $request.bookmark eq "sales"} class="users_u_top_yellowgreen"   {/if} 


				> <span style='display:inline-block; width:60px;'> <a onclick="CheckAll(this)" style='color:white'>All</a>/<a style='color:white' onclick="unCheckAll(this)" >None</a></span></td>
				
				<td width="10%" {*
	  {if $request.bookmark eq "company"}class="users_top" {/if}
	  {if $request.bookmark eq "facility"}class="users_top_green"  {/if}
	  {if $request.bookmark eq "department"} class="users_top_violet" {/if}
	  {if $request.bookmark eq "admin"} class="users_top_blue"  {/if} 
	  {if $request.bookmark eq "sales"} class="users_top_blue"  {/if}
*}
				>
				<a style='color:white;' onclick='$("#sort").attr("value","{if $sort==1}2{else}1{/if}"); $("#sortForm").submit();'>
		            <div style='width:100%;  color:white;'>						
		                ID Number 		
						{if $sort==1 || $sort==2}<img src="{if $sort==1}images/asc2.gif{/if}{if $sort==2}images/desc2.gif{/if}" alt=""/>{/if}				
					</div>
				</a>	
				</td>

				<td width="15%" {*
	  {if $request.bookmark eq "company"} class="users_top"  {/if}
	  {if $request.bookmark eq "facility"}class="users_top_green"  {/if}
	  {if $request.bookmark eq "department"}class="users_top_violet" {/if}
	  {if $request.bookmark eq "admin"} class="users_top_blue"  {/if} 
	  {if $request.bookmark eq "sales"} class="users_top_blue"  {/if}
*}
				>
				<a style='color:white;' onclick='$("#sort").attr("value","{if $sort==3}4{else}3{/if}"); $("#sortForm").submit();'>
		            <div style='width:100%;  color:white;'>						
		                Name 
						{if $sort==3 || $sort==4}<img src="{if $sort==3}images/asc2.gif{/if}{if $sort==4}images/desc2.gif{/if}" alt=""/>{/if}				
					</div>					
				</a> 
				</td>

				<td  width="15%" {*
	  {if $request.bookmark eq "company"} class="users_top"  {/if}
	  {if $request.bookmark eq "facility"} class="users_top_green"  {/if}
	  {if $request.bookmark eq "department"} class="users_top_violet" {/if}
	  {if $request.bookmark eq "admin"} class="users_top_blue"  {/if} 
	  {if $request.bookmark eq "sales"} class="users_top_blue"  {/if}
*}
				>				
				<a style='color:white;' onclick='$("#sort").attr("value","{if $sort==5}6{else}5{/if}"); $("#sortForm").submit();'>
		            <div style='width:100%;  color:white;'>						
		                Access name 
						{if $sort==5 || $sort==6}<img src="{if $sort==5}images/asc2.gif{/if}{if $sort==6}images/desc2.gif{/if}" alt=""/>{/if}				
					</div>					
				</a> 
				</td>

{if $request.bookmark != "admin" and $request.bookmark != "sales"}
				<td 
	  {if $request.bookmark eq "company"} class="users_u_top_r"  {/if}
	  {if $request.bookmark eq "facility"} class="users_u_top_r_green"  {/if}
	  {if $request.bookmark eq "department"} class="users_u_top_r_violet" {/if}
	  
				>Start point</td>
{else}
				<td width="15%"  {if $request.bookmark eq "admin"} class="users_u_top_r_blue"  {/if}{if $request.bookmark eq "sales"} class="users_u_top_r_yellowgreen"  {/if} >&nbsp;
				</td>
{/if}

			</tr>
						 
{if $itemsCount > 0}						 
						 
{*BEGIN LIST*}						 
{section name=i loop=$category}												
			
			<tr  height="10px" class="hov_company">
				<td style="border-bottom:1px solid #cacaca;"  class="border_users_l border_users_r">
 					<input type="checkbox"  value="{$category[i].user_id}" name="item_{$smarty.section.i.index}">
 				</td>

 				<td style="border-bottom:1px solid #cacaca"  class="border_users_r">
              		<a href="{$category[i].url}" ><div style="width:100%;">{$category[i].user_id}</div ></a>
              	</td>

				<td style="border-bottom:1px solid #cacaca"  class="border_users_r">
					<a href="{$category[i].url}"><div style="width:100%;">{$category[i].username}	</div ></a>
				</td>

				<td style="border-bottom:1px solid #cacaca"  class="border_users_r">
					<a href="{$category[i].url}" ><div style="width:100%;">{$category[i].accessname}	</div ></a>
				</td>
				
				<td style="border-bottom:1px solid #cacaca;" class="border_users_r">
				{if $request.bookmark != "admin" and $request.bookmark != "sales"}
					<a href="{$category[i].url}" ><div style="width:100%;">{$category[i].startPoint}</div ></a>
				{else}
					&nbsp;
				{/if}
				 </td>				
			</tr>
{/section}		
		
		<tr >
			<td   class="border_users_l border_users_r" colspan="5"  >&nbsp;</td>
		</tr>
{*END LIST*}

{else}

{*BEGIN	EMPTY LIST*}
		<tr align='center'>
			<td class="border_users_l border_users_r" colspan="5" >No users</td>
		</tr>
{*END	EMPTY LIST*}

{/if}
		<tr >
			<td  height="25" class="users_u_bottom">&nbsp;</td>
			<td colspan="3"  class="border_users"></td>	
			<td   class="users_u_bottom_r"></td>
		</tr>
	</table>
	
	{*PAGINATION*}
		{include file="tpls:tpls/pagination.tpl"}
	{*/PAGINATION*}
</div>
</form>

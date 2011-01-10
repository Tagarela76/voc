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
	
	<table class="users" width="100%" cellspacing="0" cellpadding="0">
	
		<thead>
			<tr class="users_u_top_size users_top_blue">
				<td class="users_u_top_blue"  width="5%"><span style='display:inline-block; width:60px;'> <a onclick="CheckAll(this)" style='color:white'>All</a>/<a style='color:white' onclick="unCheckAll(this)" >None</a></span></td>
				<td>
					<a style='color:white;' onclick='$("#sort").attr("value","{if $sort==1}2{else}1{/if}"); $("#sortForm").submit();'>
		            	<div style='width:100%;  color:white;'>						
		                	Rule NR US 		
							{if $sort==1 || $sort==2}<img src="{if $sort==1}images/asc2.gif{/if}{if $sort==2}images/desc2.gif{/if}" alt=""/>{/if}				
						</div>
					</a>	
				</td>
				<td>
					<a style='color:white;' onclick='$("#sort").attr("value","{if $sort==3}4{else}3{/if}"); $("#sortForm").submit();'>
		            	<div style='width:100%;  color:white;'>						
		                	Rule NR EU 		
							{if $sort==3 || $sort==4}<img src="{if $sort==3}images/asc2.gif{/if}{if $sort==4}images/desc2.gif{/if}" alt=""/>{/if}				
						</div>					
					</a> 
				</td>
				<td>
					<a style='color:white;' onclick='$("#sort").attr("value","{if $sort==5}6{else}5{/if}"); $("#sortForm").submit();'>
		            	<div style='width:100%;  color:white;'>						
		                	Rule NR China 		
							{if $sort==5 || $sort==6}<img src="{if $sort==5}images/asc2.gif{/if}{if $sort==6}images/desc2.gif{/if}" alt=""/>{/if}				
						</div>					
					</a>  
				</td>				
				<td class="users_u_top_r_blue">
					<a style='color:white;' onclick='$("#sort").attr("value","{if $sort==7}8{else}7{/if}"); $("#sortForm").submit();'>
		            	<div style='width:100%;  color:white;'>						
		                	Description 		
							{if $sort==7 || $sort==8}<img src="{if $sort==7}images/asc2.gif{/if}{if $sort==8}images/desc2.gif{/if}" alt=""/>{/if}				
						</div>					
					</a>  
				</td>
			</tr>		
		</thead>			
        
        <tbody>                          						 

{if $itemsCount > 0}						 
						 
{*BEGIN LIST*}						 
{section name=i loop=$category}												
			<tr class="hov_company">
				<td class="border_users_l border_users_b">
 					<input type="checkbox"  value="{$category[i].rule_id}" name="item_{$smarty.section.i.index}">
 				</td>
 				<td class="border_users_b border_users_l">
              		<a href="{$category[i].url}"><div style="width:100%;">{$category[i].rule_nr_us}</div ></a>
				</td>
				<td class="border_users_b border_users_l">
              		<a href="{$category[i].url}"><div style="width:100%;">{$category[i].rule_nr_eu}</div ></a>
				</td>
				<td class="border_users_b border_users_l">
              		<a href="{$category[i].url}"><div style="width:100%;">{$category[i].rule_nr_cn}</div ></a>
				</td>
				<td class="border_users_b border_users_l border_users_r">
					<a href="{$category[i].url}"><div style="width:100%;">{$category[i].description}</div ></a>
				</td>
			</tr>
{/section}												
{*END LIST*}

{else}

{*BEGIN	EMPTY LIST*}
		<tr class="" align='center'>
			<td colspan="5" class="border_users_l border_users_r" >No rules</td>
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
<div class="padd7" align="center">
	<table  class="users" width="100%" cellspacing="0" cellpadding="0" bgcolor="#EFEFEF">
		<thead>	
		<tr  class="users_u_top_size users_top_brown">
			
			<td  class="users_u_top_brown">Product ID</td>
			
			<td  class="">
				
		            <div style='width:100%;  color:white;'>					
						Accessory Name {if $sort==1 || $sort==2}<img src="{if $sort==1}images/asc2.gif{/if}{if $sort==2}images/desc2.gif{/if}" alt=""/>{/if}
					</div>
					
			</td>
			<td  class="">
				
		            <div style='width:100%;  color:white;'>					
						Client {if $sort==1 || $sort==2}<img src="{if $sort==1}images/asc2.gif{/if}{if $sort==2}images/desc2.gif{/if}" alt=""/>{/if}
					</div>
					
			</td>			
			<td  class="users_u_top_r_brown">
				
		            <div style='width:100%;  color:white;'>						
		                Discount {if $sort==3 || $sort==4}<img src="{if $sort==3}images/asc2.gif{/if}{if $sort==4}images/desc2.gif{/if}" alt=""/>{/if}				
					</div>					
				
			</td>
			

		</tr>
		</thead>
		
		<tbody>
{if $gom > 0}						 

		
{*BEGIN LIST*}				
{section name=i loop=$gom}	
	<tr class="hov_company">
			
			<td class="border_users_b border_users_l" >
				<a href="{$gom[i].url}{if $page}&page={$page}{/if}{if $request.subBookmark}&subBookmark={$request.subBookmark}{/if}"><div style="width:100%;">{$gom[i].id}</div ></a>
			</td>
			
			
            <td class="border_users_b border_users_l" >
				<a href="{$gom[i].url}{if $page}&page={$page}{/if}{if $request.subBookmark}&subBookmark={$request.subBookmark}{/if}"><div style="width:100%;">{$gom[i].name}</div ></a>
			</td>
            <td class="border_users_b border_users_l" >
				<a href="{$gom[i].url}{if $page}&page={$page}{/if}{if $request.subBookmark}&subBookmark={$request.subBookmark}{/if}"><div style="width:100%;">{$gom[i].cname} > {$gom[i].fname}</div ></a>
			</td>			
			<td class="border_users_b border_users_l border_users_r" >
				<a href="{$gom[i].url}{if $page}&page={$page}{/if}{if $request.subBookmark}&subBookmark={$request.subBookmark}{/if}"><div style="width:100%;">{if $gom[i].discount}{$gom[i].discount}{else}0{/if} %</div ></a>
			</td>	
		
	</tr>
{/section}		 
							
{*END LIST*}

{else}

{*BEGIN	EMPTY LIST*}
		<tr class="">
		    <td  class="border_users_l border_users_r" style='text-align:center; vertical-align:middle;' colspan="4" >No accessories yet</td>
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

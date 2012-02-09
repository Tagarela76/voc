
<div class="padd7">
	<table class="users" height="200" cellspacing="0" cellpadding="0" align="center">
    <tr class="users_top_green" height="27px">
<!--        <td class="users_top_green users_u_top_green" width="60">
            <span style='display:inline-block; width:60px;'> <a onclick="CheckAll(this)" style='color:white'>All</a>/<a style='color:white' onclick="unCheckAll(this)" >None</a></span>
        </td>
-->
        <td class="users_top_green users_u_top_green">
           <a style='color:white;' onclick='$("#sort").attr("value","{if $sort==1}2{else}1{/if}"); $("#sortForm").submit();'>
            	<div style='width:100%;  color:white;'>						
                	Product ID		
					{if $sort==1 || $sort==2}<img src="{if $sort==1}images/asc2.gif{/if}{if $sort==2}images/desc2.gif{/if}" alt=""/>{/if}				
				</div>					
			</a>   
        </td>
        <td class="users_top_green ">
         
            	<div style='width:100%;  color:white;'>						
                	Supplier Name		
					{if $sort==3 || $sort==4}<img src="{if $sort==3}images/asc2.gif{/if}{if $sort==4}images/desc2.gif{/if}" alt=""/>{/if}				
				</div>					
			  
        </td>
        <td class="users_top_green ">
           <a style='color:white;' onclick='$("#sort").attr("value","{if $sort==5}6{else}5{/if}"); $("#sortForm").submit();'>
            	<div style='width:100%;  color:white;'>						
                	Product Name		
					{if $sort==5 || $sort==6}<img src="{if $sort==5}images/asc2.gif{/if}{if $sort==6}images/desc2.gif{/if}" alt=""/>{/if}				
				</div>					
			</a>   
        </td>		
        <td class="users_u_top_r_green">
             <a style='color:white;' onclick='$("#sort").attr("value","{if $sort==7}8{else}7{/if}"); $("#sortForm").submit();'>
            	<div style='width:100%;  color:white;'>						
                	Discount, % 	
					{if $sort==7 || $sort==8}<img src="{if $sort==7}images/asc2.gif{/if}{if $sort==8}images/desc2.gif{/if}" alt=""/>{/if}				
				</div>					
			</a> 
        </td>

        {*if !$accessory}
        <td class="users_top_green">
           
            	<div style='width:100%;  color:white;'>						
                	Supplier	
						
				</div>					
			 
        </td>
        <td class="users_top_green">
            
            	<div style='width:100%;  color:white;'>						
                	Product NR 	
								
				</div>					
			
        </td>
        <td class="users_u_top_r_green">
            
            	<div style='width:100%;  color:white;'>						
                	Product Description 	
							
				</div>					
			
        </td>
        {/if*}
        
    </tr>

{if $supplierlist|@count > 0}  
    {*BEGIN LIST*}  
    {foreach from=$supplierlist item=supplier} 
    <tr class="hov_company" height="10px">
		
        <td class="border_users_r border_users_l border_users_b">
            <a href="{$supplier.url}" class="id_company1">
                <div style="width:100%;">
                    {$supplier.product_id}
                </div>
            </a>
        </td>		

        <td class="border_users_r border_users_b">
            <a href="{$supplier.url}" class="id_company1">
                <div style="width:100%;">
                    {$supplier.supplier}
                </div>
            </a>
        </td>
        <td class="border_users_r border_users_b">
            <a href="{$supplier.url}" class="id_company1">
                <div style="width:100%;">
                    {$supplier.product_nr}
                </div>
            </a>
        </td>		
        <td class="border_users_r border_users_b">
            <a href="{$supplier.url}" class="id_company1">
                <div style="width:100%;">
                    {$supplier.discount}
                </div>
            </a>
        </td>

    </tr>
    {/foreach} 
    <tr>
        <td colspan="{*if !$accessory}7{else}4{/if*}4" class="border_users_l border_users_r">
            &nbsp;
        </td>
    </tr>
    {*END LIST*}
{else}
    {*BEGIN	EMPTY LIST*}
    <tr>
        <td colspan="{*if !$accessory}7{else}4{/if*}4" class="border_users_l border_users_r" align="center">
            No Suplliers for inventory
        </td>
    </tr>
    {*END	EMPTY LIST*}
{/if}
    <tr>
        <td class="users_u_bottom">
        </td>
		<td class="border_users" colspan="2">
        </td>

        <td class="users_u_bottom_r">
        </td>
    </tr>
</table></div>
</form>

{if $smarty.request.tab == 'accessory'}
	{assign var='accessory' value=true}
{/if}
<div class="padd7">
{*PAGINATION*}
		{include file="tpls:tpls/pagination.tpl"}
{*/PAGINATION*}	
	<table class="users" height="200" cellspacing="0" cellpadding="0" align="center">
    <tr class="users_top_violet" height="27px">
<!--        <td class="users_top_violet users_u_top_violet" width="60">
            <span style='display:inline-block; width:60px;'> <a onclick="CheckAll(this)" style='color:white'>All</a>/<a style='color:white' onclick="unCheckAll(this)" >None</a></span>
        </td>
-->
        <td class="users_top_violet users_u_top_violet">
           <a style='color:white;' onclick='$("#sort").attr("value","{if $sort==1}2{else}1{/if}"); $("#sortForm").submit();'>
            	<div style='width:100%;  color:white;'>						
                	ID Number 		
					{if $sort==1 || $sort==2}<img src="{if $sort==1}images/asc2.gif{/if}{if $sort==2}images/desc2.gif{/if}" alt=""/>{/if}				
				</div>					
			</a>   
        </td>
        <td class="users_top_violet">
             <a style='color:white;' onclick='$("#sort").attr("value","{if $sort==3}4{else}3{/if}"); $("#sortForm").submit();'>
            	<div style='width:100%;  color:white;'>						
                	Product Name 	
					{if $sort==3 || $sort==4}<img src="{if $sort==3}images/asc2.gif{/if}{if $sort==4}images/desc2.gif{/if}" alt=""/>{/if}				
				</div>					
			</a> 
        </td>
        <td class="users_u_top_r_violet">
            <a style='color:white;' onclick='$("#sort").attr("value","{if $sort==5}6{else}5{/if}"); $("#sortForm").submit();'>
            	<div style='width:100%;  color:white;'>						
                	Usage
					{if $sort==5 || $sort==6}<img src="{if $sort==5}images/asc2.gif{/if}{if $sort==6}images/desc2.gif{/if}" alt=""/>{/if}				
				</div>					
			</a> 
        </td>
        {*if !$accessory}
        <td class="users_top_violet">
           
            	<div style='width:100%;  color:white;'>						
                	Supplier	
						
				</div>					
			 
        </td>
        <td class="users_top_violet">
            
            	<div style='width:100%;  color:white;'>						
                	Product NR 	
								
				</div>					
			
        </td>
        <td class="users_u_top_r_violet">
            
            	<div style='width:100%;  color:white;'>						
                	Product Description 	
							
				</div>					
			
        </td>
        {/if*}
        
    </tr>

{if $Products|@count > 0}  
    {*BEGIN LIST*}  
    {foreach from=$Products item=Product} 
    <tr class="hov_company" height="10px">
<!--        <td class="border_users_b  border_users_l border_users_r">
            <input type="checkbox" value="{*$Product->getID()*}" name="id[]">
        </td> -->
        <td class="border_users_r border_users_l border_users_b">
            <a href="{$Product->url}" class="id_company1">
                <div style="width:100%;">
                    {$Product->product_id}
                </div>
            </a>
        </td>
        <td class="border_users_r border_users_b">
            <a href="{$Product->url}" class="id_company1">
                <div style="width:100%;">
                    {$Product->product_nr}
                </div>
            </a>
        </td>
        <td class="border_users_r border_users_b">
            <a href="{$Product->url}" class="id_company1">
                <div style="width:100%;">
					<div style="float:right;padding: 2px 0px 0px 5px; width: 70px;">
						{foreach from=$typeName item=type key=key}
							{if $Product->product_id == $key}{if $type}, {$type}{/if}{/if}
						{/foreach}	                    
					</div>	
					{include file="tpls:tpls/vocIndicator.tpl" currentUsage=$Product->usage
							vocLimit=$Product->in_stock
							pxCount =$Product->pxCount }
							
                </div>
            </a>
        </td>

    </tr>
    {/foreach} 

    {*END LIST*}
{else}
    {*BEGIN	EMPTY LIST*}
    <tr>
        <td colspan="3" class="border_users_l border_users_r" align="center">
            No inventories in the facility
        </td>
    </tr>
    {*END	EMPTY LIST*}
{/if}
    <tr>
        <td class="users_u_bottom">
        </td>
        <td colspan="" height="15" class="border_users">
        </td>
        <td class="users_u_bottom_r">
        </td>
    </tr>
</table></div>
</form>	


{if $error}
{foreach from=$error item=msg}
	<span style='color:red; padding-left: 20px' >
	{$msg}<br>
	</span>
{/foreach}	
{/if}
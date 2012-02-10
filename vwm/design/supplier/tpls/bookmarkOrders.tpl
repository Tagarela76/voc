
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

<div class="padd7">
	<table class="users" height="200" cellspacing="0" cellpadding="0" align="center">
    <tr class="users_top_blue" height="27px">
		{if $request.tab != 'products'}
        <td class="users_top_blue users_u_top_blue" width="60">
            <span style='display:inline-block; width:60px;'> <a onclick="CheckAll(this)" style='color:white'>All</a>/<a style='color:white' onclick="unCheckAll(this)" >None</a></span>
        </td>
		{/if}
        <td class="{if $request.tab != 'products'}users_top_blue{else}users_top_blue users_u_top_blue{/if}">
           <a style='color:white;' onclick='$("#sort").attr("value","{if $sort==1}2{else}1{/if}"); $("#sortForm").submit();'>
            	<div style='width:100%;  color:white;'>						
                	ID Number 		
					{if $sort==1 || $sort==2}<img src="{if $sort==1}images/asc2.gif{/if}{if $sort==2}images/desc2.gif{/if}" alt=""/>{/if}				
				</div>					
			</a>   
        </td>
        <td class="users_top_blue">
             <a style='color:white;' onclick='$("#sort").attr("value","{if $sort==3}4{else}3{/if}"); $("#sortForm").submit();'>
            	<div style='width:100%;  color:white;'>						
                	Amount
					{if $sort==3 || $sort==4}<img src="{if $sort==3}images/asc2.gif{/if}{if $sort==4}images/desc2.gif{/if}" alt=""/>{/if}				
				</div>					
			</a> 
        </td>
        <td class="{if !$accessory}users_top_blue{else}users_u_top_r_blue{/if}">
            <a style='color:white;' onclick='$("#sort").attr("value","{if $sort==5}6{else}5{/if}"); $("#sortForm").submit();'>
            	<div style='width:100%;  color:white;'>						
                	Order Name	
					{if $sort==5 || $sort==6}<img src="{if $sort==5}images/asc2.gif{/if}{if $sort==6}images/desc2.gif{/if}" alt=""/>{/if}				
				</div>					
			</a> 
        </td>

        <td class="users_top_blue">
           
            <a style='color:white;' onclick='$("#sort").attr("value","{if $sort==7}8{else}7{/if}"); $("#sortForm").submit();'>
            	<div style='width:100%;  color:white;'>						
                	Status	
					{if $sort==7 || $sort==8}<img src="{if $sort==7}images/asc2.gif{/if}{if $sort==8}images/desc2.gif{/if}" alt=""/>{/if}				
				</div>					
			</a> 					
			 
        </td>
        <td class="users_top_blue">
            
            <a style='color:white;' onclick='$("#sort").attr("value","{if $sort==9}10{else}9{/if}"); $("#sortForm").submit();'>
            	<div style='width:100%;  color:white;'>						
                	Created Date	
					{if $sort==9 || $sort==10}<img src="{if $sort==9}images/asc2.gif{/if}{if $sort==10}images/desc2.gif{/if}" alt=""/>{/if}				
				</div>					
			</a> 				
			
        </td>
        <td class="users_top_blue">
            
            	<div style='width:100%;  color:white;'>						
                	Price 	
							
				</div>					
			
        </td>
        <td class="users_top_blue">
            
            	<div style='width:100%;  color:white;'>						
                	Discount 	
							
				</div>					
			
        </td>		
        <td class="users_top_blue">
            
            	<div style='width:100%;  color:white;'>						
                	Total 	
							
				</div>					
			
        </td>
        <td class="users_u_top_r_blue">
            
            	<div style='width:100%;  color:white;'>						
                	Client 	
							
				</div>					
			
        </td>			

        
    </tr>

{if $orderList|@count > 0}  
    {*BEGIN LIST*}  
    {foreach from=$orderList item=order} 
    <tr class="hov_company" height="10px">
		{if $request.tab != 'products'}
        <td class="border_users_b  border_users_l border_users_r">
            <input type="checkbox" value="{$order.order_id}" name="id[]">
        </td>
		{/if}
        <td class="{if $request.tab != 'products'}border_users_r border_users_b{else}border_users_b  border_users_l border_users_r{/if}">
            <a href="{$order.url}" class="id_company1">
                <div style="width:100%;">
                    {$order.order_id}
                </div>
            </a>
        </td>
        <td class="border_users_r border_users_b">
            <a href="{$order.url}" class="id_company1">
                <div style="width:100%;">
                    {$order.order_amount}
                </div>
            </a>
        </td>
        <td class="border_users_r border_users_b">
            <a href="{$order.url}" class="id_company1">
                <div style="width:100%;">
                    {$order.order_name}
                </div>
            </a>
        </td>

        <td class="border_users_r border_users_b">
            <a href="{$order.url}" class="id_company1">
                <div style="width:100%;">
                   {if $order.order_status == 1}In Progress {elseif $order.order_status == 2}Confirm{elseif $order.order_status == 3}Completed{elseif $order.order_status == 4}Cnceled{/if}
                </div>
            </a>
        </td>
        <td class="border_users_r border_users_b">
            <a href="{$order.url}" class="id_company1">
                <div style="width:100%;">
                    {$order.order_created_date}
                </div>
            </a>
        </td>
        <td class="border_users_r border_users_b">
            <a href="{$order.url}" class="id_company1">
                <div style="width:100%;">
                    {$order.price}
                </div>
            </a>
        </td>
        <td class="border_users_r border_users_b">
            <a href="{$order.url}" class="id_company1">
                <div style="width:100%;">
                    {$order.discount} %
                </div>
            </a>
        </td>		
        <td class="border_users_r border_users_b">
            <a href="{$order.url}" class="id_company1">
                <div style="width:100%;">
                    $ {$order.order_total}
                </div>
            </a>
        </td>
        <td class="border_users_b border_users_r">
            <a href="{$order.url}" class="id_company1">
                <div style="width:100%;">
                   {$order.client}
                </div>
            </a>
        </td>		

    </tr>
    {/foreach} 
    <tr>
        <td colspan="10" class="border_users_l border_users_r">
            &nbsp;
        </td>
    </tr>
    {*END LIST*}
{else}
    {*BEGIN	EMPTY LIST*}
    <tr>
        <td colspan="10" class="border_users_l border_users_r" align="center">
            No Orders
        </td>
    </tr>
    {*END	EMPTY LIST*}
{/if}
    <tr>
        <td class="users_u_bottom">
        </td>
        <td colspan="8" height="15" class="border_users">
        </td>
        <td class="users_u_bottom_r">
        </td>
    </tr>
</table></div>
</form>
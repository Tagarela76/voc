<div class="padd7">
	<table class="users" height="200" cellspacing="0" cellpadding="0" align="center">
    <tr class="users_top_violet" height="27px">
        <td class="users_top_violet users_u_top_violet" width="60">
            <span style='display:inline-block; width:60px;'> <a onclick="CheckAll(this)" style='color:white'>All</a>/<a style='color:white' onclick="unCheckAll(this)" >None</a></span>
        </td>
        <td class="users_top_violet">
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
                	Inventory Name 	
					{if $sort==3 || $sort==4}<img src="{if $sort==3}images/asc2.gif{/if}{if $sort==4}images/desc2.gif{/if}" alt=""/>{/if}				
				</div>					
			</a> 
        </td>
        <td class="users_u_top_r_violet">
            <a style='color:white;' onclick='$("#sort").attr("value","{if $sort==5}6{else}5{/if}"); $("#sortForm").submit();'>
            	<div style='width:100%;  color:white;'>						
                	Inventory description 	
					{if $sort==5 || $sort==6}<img src="{if $sort==5}images/asc2.gif{/if}{if $sort==6}images/desc2.gif{/if}" alt=""/>{/if}				
				</div>					
			</a> 
        </td>
    </tr>

{if $childCategoryItems|@count > 0}  
    {*BEGIN LIST*}  
    {foreach from=$childCategoryItems item=inventory} 
    <tr class="hov_company" height="10px">
        <td class="border_users_b  border_users_l border_users_r">
            <input type="checkbox" value="{$inventory->getID()}" name="id[]">
        </td>
        <td class="border_users_r border_users_b">
            <a href="{$inventory->url}" class="id_company1">
                <div style="width:100%;">
                    {$inventory->getID()}
                </div>
            </a>
        </td>
        <td class="border_users_r border_users_b">
            <a href="{$inventory->url}" class="id_company1">
                <div style="width:100%;">
                    {$inventory->getName()}
                </div>
            </a>
        </td>
        <td class="border_users_b border_users_r">
            <a href="{$inventory->url}" class="id_company1">
                <div style="width:100%;">
                    {$inventory->getDescription()}
                </div>
            </a>
        </td>
    </tr>
    {/foreach} 
    <tr>
        <td colspan="4" class="border_users_l border_users_r">
            &nbsp;
        </td>
    </tr>
    {*END LIST*}
{else}
    {*BEGIN	EMPTY LIST*}
    <tr>
        <td colspan="4" class="border_users_l border_users_r" align="center">
            No inventories in the facility
        </td>
    </tr>
    {*END	EMPTY LIST*}
{/if}
    <tr>
        <td class="users_u_bottom">
        </td>
        <td colspan="2" height="15" class="border_users">
        </td>
        <td class="users_u_bottom_r">
        </td>
    </tr>
</table></div>
</form>	{*this FORM opened at controlInsideDepartment.tpl*}

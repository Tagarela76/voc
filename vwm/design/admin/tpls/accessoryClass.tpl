{if $message}
<table cellspacing="0" cellpadding="0" width="100%" height="37px">    
    <tr>
        <td bgcolor="white" valign="bottom">
            {if $color eq "green"}
            {include file="tpls:tpls/notify/greenNotify.tpl" text=$message}
            {/if}
            {if $color eq "orange"}
            {include file="tpls:tpls/notify/orangeNotify.tpl" text=$message}
            {/if}
            {if $color eq "blue"}
            {include file="tpls:tpls/notify/blueNotify.tpl" text=$message}
            {/if}            
        </td>
    </tr>
</table>
{/if}

{*PAGINATION*} 
{include file="tpls:tpls/pagination.tpl"}  
{*/PAGINATION*} 
<div class="padd7" align="center">	
	
<table class="users" cellspacing="0" cellpadding="0" align="center">
		<tr class="users_u_top_size users_top_blue">
			<td  class="users_u_top_blue"  width="5%">
        	<div class="users_header_blue_l"><div>
            	<span style='display:inline-block; width:60px;'> <a onclick="CheckAll(this)" style='color:white'>All</a>/<a style='color:white' onclick="unCheckAll(this)" >None</a></span>
        	</div></div>
        </td>
        <td class="">
            <a style='color:white;' onclick='$("#sort").attr("value","{if $sort==1}2{else}1{/if}"); $("#sortForm").submit();'>
            	<div style='width:100%;  color:white;'>						
                	ID Number 		
					{if $sort==1 || $sort==2}<img src="{if $sort==1}images/asc2.gif{/if}{if $sort==2}images/desc2.gif{/if}" alt=""/>{/if}				
				</div>					
			</a>   
        </td>
        <td >
        	<div class="users_header_blue_r">
	            <a style='color:white;' onclick='$("#sort").attr("value","{if $sort==3}4{else}3{/if}"); $("#sortForm").submit();'>
	            	<div>						
	                	Accessory Name 	
						{if $sort==3 || $sort==4}<img src="{if $sort==3}images/asc2.gif{/if}{if $sort==4}images/desc2.gif{/if}" alt=""/>{/if}				
					</div>					
				</a> 
			</div>
        </td>
    </tr>

    {*BEGIN LIST*}  
{if $itemsCount > 0}   
    {section name=i loop=$childCategoryItems}
    <tr class="hov_accessory" >
        <td class="border_users_b border_users_r border_users_l">
            <input type="checkbox" value="{$childCategoryItems[i].id}" name="id[]">
        </td>
        <td class="border_users_r border_users_b">
            <a href="{$childCategoryItems[i].url}" class="id_accessory1">
                <div>
                    {$childCategoryItems[i].id}
                </div>
            </a>
        </td>
        <td class="border_users_b border_users_r">
            <a href="{$childCategoryItems[i].url}" class="id_accessory1">
                <div>
                    {$childCategoryItems[i].name}
                </div>
            </a>
        </td>
    </tr>
    {/section}
   
    {*END LIST*}

	{else}
	    {*BEGIN	EMPTY LIST*}
	    <tr class="">
	        <td class="border_users_l">
	            &nbsp;
	        </td>
	        <td  class="" align="right">
	            No accessories
	        </td>
	        <td class="border_users_r">
	            &nbsp;
	        </td>
	    </tr>
	    {*END	EMPTY LIST*}

	{/if}
    <tr>
        <td  class="users_u_bottom">
            &nbsp;
        </td>
        <td class="border_users">
        </td>
        <td class="users_u_bottom_r">
        </td>
    </tr>
</table>
	</div>
{*PAGINATION*}
{include file="tpls:tpls/pagination.tpl"}
{*/PAGINATION*}
</form>	
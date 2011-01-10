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
<table class="users" height="200" cellspacing="0" cellpadding="0" align="center">
    <tr class="users_header_green" height="27">
        <td width="60">
        	<div class="users_header_green_l"><div>
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
        	<div class="users_header_green_r">
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
    
    {section name=i loop=$childCategoryItems}
    <tr class="hov_accessory" height="10px">
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

	{if $smarty.section.i.total==0}
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
	{elseif $smarty.section.i.total <=7}
		<tr height='{math equation="x - (y * 27)" x=200 y=$smarty.section.i.total}'>
		<td class="border_users_l border_users_b border_users_r" colspan='11'>
				&nbsp;
			</td>						
		</tr>
	{/if}
    <tr>
        <td height="25" class="users_u_bottom">
            &nbsp;
        </td>
        <td class="border_users">
        </td>
        <td class="users_u_bottom_r">
        </td>
    </tr>
</table>
{*PAGINATION*}
{include file="tpls:tpls/pagination.tpl"}
{*/PAGINATION*}
</form>	
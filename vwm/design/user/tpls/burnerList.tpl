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
    <tr class="users_header_violet" height="27">
        <td width="60">
        	<div class="users_header_violet_l"><div>
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
        <td class="">
    
	            <a style='color:white;' onclick='$("#sort").attr("value","{if $sort==3}4{else}3{/if}"); $("#sortForm").submit();'>
	            	<div>						
	                	Burner Model 	
						{if $sort==3 || $sort==4}<img src="{if $sort==3}images/asc2.gif{/if}{if $sort==4}images/desc2.gif{/if}" alt=""/>{/if}				
					</div>					
				</a> 

        </td>
        <td class="">
    
	            <a style='color:white;' onclick='$("#sort").attr("value","{if $sort==5}6{else}5{/if}"); $("#sortForm").submit();'>
	            	<div>						
	                	Serial Number 	
						{if $sort==5 || $sort==6}<img src="{if $sort==5}images/asc2.gif{/if}{if $sort==6}images/desc2.gif{/if}" alt=""/>{/if}				
					</div>					
				</a> 

        </td>
        <td class="">
    
	            <a style='color:white;' onclick='$("#sort").attr("value","{if $sort==7}8{else}7{/if}"); $("#sortForm").submit();'>
	            	<div>						
	                	Manufacturer 	
						{if $sort==7 || $sort==8}<img src="{if $sort==7}images/asc2.gif{/if}{if $sort==8}images/desc2.gif{/if}" alt=""/>{/if}				
					</div>					
				</a> 

        </td>
        <td class="">
    
	            <a style='color:white;' onclick='$("#sort").attr("value","{if $sort==9}10{else}9{/if}"); $("#sortForm").submit();'>
	            	<div>						
	                	Input	
						{if $sort==9 || $sort==10}<img src="{if $sort==9}images/asc2.gif{/if}{if $sort==10}images/desc2.gif{/if}" alt=""/>{/if}				
					</div>					
				</a> 

        </td>	
        <td class="">
    
	            <a style='color:white;' onclick='$("#sort").attr("value","{if $sort==11}12{else}11{/if}"); $("#sortForm").submit();'>
	            	<div>						
	                	Output	
						{if $sort==11 || $sort==12}<img src="{if $sort==11}images/asc2.gif{/if}{if $sort==12}images/desc2.gif{/if}" alt=""/>{/if}				
					</div>					
				</a> 

        </td>		
        <td >
        	<div class="users_header_violet_r">
	            <a style='color:white;' onclick='$("#sort").attr("value","{if $sort==13}14{else}13{/if}"); $("#sortForm").submit();'>
	            	<div>						
	                	BTUS 	
						{if $sort==13 || $sort==14}<img src="{if $sort==13}images/asc2.gif{/if}{if $sort==14}images/desc2.gif{/if}" alt=""/>{/if}				
					</div>					
				</a> 
			</div>
        </td>		
    </tr>

    {*BEGIN LIST*}  
    
    {section name=i loop=$childCategoryItems}
    <tr class="hov_accessory" height="10px">
        <td class="border_users_b border_users_r border_users_l">
            <input type="checkbox" value="{$childCategoryItems[i].burner_id|escape}" name="id[]">
        </td>
        <td class="border_users_r border_users_b">
            <a href="{$childCategoryItems[i].url}" class="id_accessory1">
                <div>
                    {$childCategoryItems[i].burner_id|escape}
                </div>
            </a>
        </td>
        <td class="border_users_r border_users_b">
            <a href="{$childCategoryItems[i].url}" class="id_accessory1">
                <div>
                    {$childCategoryItems[i].model|escape}
                </div>
            </a>
        </td>
        <td class="border_users_r border_users_b">
            <a href="{$childCategoryItems[i].url}" class="id_accessory1">
                <div>
                    {$childCategoryItems[i].serial|escape}
                </div>
            </a>
        </td>
        <td class="border_users_r border_users_b">
            <a href="{$childCategoryItems[i].url}" class="id_accessory1">
                <div>
                    {$childCategoryItems[i].manufacturer|escape}
                </div>
            </a>
        </td>
        <td class="border_users_r border_users_b">
            <a href="{$childCategoryItems[i].url}" class="id_accessory1">
                <div>
                    {$childCategoryItems[i].input|escape}
                </div>
            </a>
        </td>
        <td class="border_users_r border_users_b">
            <a href="{$childCategoryItems[i].url}" class="id_accessory1">
                <div>
                    {$childCategoryItems[i].output|escape}
                </div>
            </a>
        </td>		
        <td class="border_users_b border_users_r">
            <a href="{$childCategoryItems[i].url}" class="id_accessory1">
                <div>
                    {$childCategoryItems[i].btu|escape}
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
			<td  class="" align="center" colspan="6">
	            No Burners
	        </td>
	        <td class="border_users_r">
	            &nbsp;
	        </td>
	    </tr>
	    {*END	EMPTY LIST*}
	{elseif $smarty.section.i.total <=7}
		<tr height='{math equation="x - (y * 27)" x=200 y=$smarty.section.i.total}'>
		<td class="border_users_l border_users_b border_users_r" colspan='8'>
				&nbsp;
			</td>						
		</tr>
	{/if}
    <tr>
        <td height="25" class="users_u_bottom">
            &nbsp;
        </td>
        <td class="border_users" colspan="6">
        </td>
        <td class="users_u_bottom_r">
        </td>
    </tr>
</table>
{*PAGINATION*}
{include file="tpls:tpls/pagination.tpl"}
{*/PAGINATION*}
</form>	
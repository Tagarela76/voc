<div align="center">
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
            {if $color eq "blue2" && $itemsCount == 0}
            {include file="tpls:tpls/notify/blueNotify.tpl" text=$message}
            {/if}
        </td>
    </tr>
</table>
{/if}

<table class="users" cellspacing="0" cellpadding="0">
    <tr class="users_header_blue">
        <td width="60">
            <div class="users_header_blue_l"><div><span style='display:inline-block; width:60px;'> <a onclick="CheckAll(this)" style='color:white'>All</a>/<a style='color:white' onclick="unCheckAll(this)" >None</a></span></div></div>
        </td>
        
        <td width="100">
        <div>        	
            <a style='color:white;' onclick='$("#sort").attr("value","{if $sort==1}2{else}1{/if}"); $("#sortForm").submit();'>            						
                	ID Number 		
					{if $sort==1 || $sort==2}<img src="{if $sort==1}images/asc2.gif{/if}{if $sort==2}images/desc2.gif{/if}" alt=""/>{/if}								
			</a>			
		</div>   
        </td>
        
        <td>
        	<div class="users_header_blue_r">
        		<div>
        		<a style='color:white;' onclick='$("#sort").attr("value","{if $sort==3}4{else}3{/if}"); $("#sortForm").submit();'>            							
                		Equipment name 	
						{if $sort==3 || $sort==4}<img src="{if $sort==3}images/asc2.gif{/if}{if $sort==4}images/desc2.gif{/if}" alt=""/>{/if}									
				</a> 
				</div>
        	</div>            
        </td>
    </tr>
{if $childCategoryItems|@count > 0} 
    {*BEGIN LIST*} 
    
 
   {section name=i loop=$childCategoryItems}
    <tr
{if $childCategoryItems[i].valid  eq "valid"}
 class="hov_company"
{elseif $childCategoryItems[i].valid  eq "expired"}
 class="us_red"
{elseif $childCategoryItems[i].valid  eq "preexpired"}
 class="us_orange"
{/if}
 height="10px">
        <td class="border_users_b border_users_l">
            <!-- Apply status icon -->
			{if $childCategoryItems[i].valid eq "valid"}
				<span class="ok">&nbsp;</span>
            {elseif $childCategoryItems[i].valid eq "preexpired"}
				<span class="warning">&nbsp;</span>
            {elseif $childCategoryItems[i].valid eq "expired"}
				<span class="error">&nbsp;</span>
            {/if}
			<input type="checkbox" value="{$childCategoryItems[i].equipment_id}" name="id[]">
        </td>
        <td class="border_users_b border_users_r">
            <a {if $permissions.equipment.view}href="{$childCategoryItems[i].url}"{/if}  class="id_company1" title="{$childCategoryItems[i].hoverMessage}">
                <div style="width:100%;">
                    {$childCategoryItems[i].equipment_id}
                </div>
            </a>
        </td>
        <td class="border_users_b border_users_r">
            <a {if $permissions.equipment.view}href="{$childCategoryItems[i].url}"{/if}  class="id_company1" title="{$childCategoryItems[i].hoverMessage}">
                <div style="width:100%;">
                    {$childCategoryItems[i].equip_desc} 
                </div>
            </a>
        </td>
    </tr>
    {/section}
    
    {if $smarty.section.i.total==0}
     <tr>
        <td colspan="3"  class="border_users_l border_users_r " align='center'>
            No equipments
        </td>
    </tr>    
    {/if}
 
    
    <tr>
        <td colspan="3" class="border_users_l border_users_r">
            &nbsp;
        </td>
    </tr>
    {*END LIST*}
{else}
    {*BEGIN	EMPTY LIST*}
    <tr>
        <td colspan="3" class="border_users_l border_users_r" align="center">
            No equipment in the department
        </td>
    </tr>
    {*END	EMPTY LIST*}
{/if}
    <tr>
        <td class="users_u_bottom">
        </td>
        <td height="15" class="border_users">
        </td>
        <td class="users_u_bottom_r">
        </td>
    </tr>
</table>
</div>
</form>	{*this FORM opened at controlInsideDepartment.tpl*}

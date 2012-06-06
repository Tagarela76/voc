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
    <tr class="users_top_green" height="27">
        <td class="users_u_top_green" width="60">
            <span style='display:inline-block; width:60px;'> <a onclick="CheckAll(this)" style='color:white'>All</a>/<a style='color:white' onclick="unCheckAll(this)" >None</a></span>
        </td>   
        <td style='width:85px;'>
           <a style='color:white;' onclick='$("#sort").attr("value","{if $sort==1}2{else}1{/if}"); $("#sortForm").submit();'>
            	<div style='width:100%;  color:white;'>						
                	ID Number          
					{if $sort==1 || $sort==2}<img  src="{if $sort==1}images/asc2.gif{/if}{if $sort==2}images/desc2.gif{/if}" />{/if}								
				</div>				
			</a>       
        </td>   
        <td class="">
        	<a style='color:white;' onclick='$("#sort").attr("value","{if $sort==3}4{else}3{/if}"); $("#sortForm").submit();'>
            	<div style='width:100%;  color:white;'>						
                	Supplier 		
					{if $sort==3 || $sort==4}<img src="{if $sort==3}images/asc2.gif{/if}{if $sort==4}images/desc2.gif{/if}" alt=""/>{/if}				
				</div>					
			</a>            
        </td>
        <td class="">
        	<a style='color:white;' onclick='$("#sort").attr("value","{if $sort==5}6{else}5{/if}"); $("#sortForm").submit();'>
            	<div style='width:100%;  color:white;'>						
                	Product No 		
					{if $sort==5 || $sort==6}<img src="{if $sort==5}images/asc2.gif{/if}{if $sort==6}images/desc2.gif{/if}" alt=""/>{/if}				
				</div>					
			</a>            
        </td>
        <td >
        	<a style='color:white;' onclick='$("#sort").attr("value","{if $sort==7}8{else}7{/if}"); $("#sortForm").submit();'>
            	<div style='width:100%;  color:white;'>						
                	Product Name 		
					{if $sort==7 || $sort==8}<img src="{if $sort==7}images/asc2.gif{/if}{if $sort==8}images/desc2.gif{/if}" alt=""/>{/if}				
				</div>					
			</a>            
        </td>
        <td >
        	<a style='color:white;' onclick='$("#sort").attr("value","{if $sort==9}10{else}9{/if}"); $("#sortForm").submit();'>
            	<div style='width:100%;  color:white;'>						
                	Coating 		
					{if $sort==9 || $sort==10}<img src="{if $sort==9}images/asc2.gif{/if}{if $sort==10}images/desc2.gif{/if}" alt=""/>{/if}				
				</div>					
			</a>
        </td>
        <td style='width:65px;'>
           <a style='color:white;' onclick='$("#sort").attr("value","{if $sort==11}12{else}11{/if}"); $("#sortForm").submit();'>
            	<div style='width:100%;  color:white;'>						
                	VOCLX 		
					{if $sort==11 || $sort==12}<img src="{if $sort==11}images/asc2.gif{/if}{if $sort==12}images/desc2.gif{/if}"/>	{/if}			
				</div>					
			</a>
        </td>
        <td  style='width:65px;'>
           <a style='color:white;' onclick='$("#sort").attr("value","{if $sort==13}14{else}13{/if}"); $("#sortForm").submit();'>
            	<div style='width:100%;  color:white;'>						
                	VOCWX 		
					{if $sort==13 || $sort==14}<img src="{if $sort==13}images/asc2.gif{/if}{if $sort==14}images/desc2.gif{/if}" />{/if}				
				</div>					
			</a>
        </td>
        <td width="66px" title="Percent of Volatile by Weight" >           
           <a style='color:white;' onclick='$("#sort").attr("value","{if $sort==15}16{else}15{/if}"); $("#sortForm").submit();'>
            	<div style='width:100%;  color:white;'>						
                	{* Percent of Volatile by Weight*} % (V/W) 		
					{if $sort==15 || $sort==16}<img src="{if $sort==15}images/asc2.gif{/if}{if $sort==16}images/desc2.gif{/if}" />{/if}				
				</div>					
			</a>
        </td>
        <td width="65px" title="Percent of Volatile by Volume" >           
           <a style='color:white;' onclick='$("#sort").attr("value","{if $sort==17}18{else}17{/if}"); $("#sortForm").submit();'>
            	<div style='width:100%;  color:white;'>						
                	{* Percent of Volatile by Weight*} % (V/V) 		
					{if $sort==17 || $sort==18}<img src="{if $sort==17}images/asc2.gif{/if}{if $sort==18}images/desc2.gif{/if}" />{/if}				
				</div>					
			</a>
        </td>
        <td width="63px" align="center">
            Tech Sheet
        </td>
		<td class="users_u_top_r_green">
            MSDS
        </td>
    </tr>

	
    {section loop=$childCategoryItems name=i}
    <tr class="border_users_l border_users_b hov_company users_u_body_size" height="27">
        <td class="border_users_b border_users_l">
            <input type="checkbox" value="{$childCategoryItems[i].product_id}" name="productID[]">
        </td>
        <td width="60px">
            <a href="{$childCategoryItems[i].url}" class="id_company1">
                <div>
                    {$childCategoryItems[i].product_id}
                </div>
            </a>
        </td>
        <td>
            <a href="{$childCategoryItems[i].url}" class="id_company1">
                <div>
                    {$childCategoryItems[i].supplier}
                </div>
            </a>
        </td>
        <td>
            <a href="{$childCategoryItems[i].url}" class="id_company1">
                <div>
                    {$childCategoryItems[i].product_nr}&nbsp;
                </div>
            </a>
        </td>
        <td>
            <a href="{$childCategoryItems[i].url}" class="id_company1">
                <div>
                    {$childCategoryItems[i].name}&nbsp;
                </div>
            </a>
        </td>
        <td >
            <a href="{$childCategoryItems[i].url}" class="id_company1">
                <div>
                    {$childCategoryItems[i].coating}&nbsp;
                </div>
            </a>
        </td>
        <td >
            <a href="{$childCategoryItems[i].url}" class="id_company1">
                <div>
                    {$childCategoryItems[i].voclx}&nbsp;
                </div>
            </a>
        </td>
        <td >
            <a href="{$childCategoryItems[i].url}" class="id_company1">
                <div>
                    {$childCategoryItems[i].vocwx}&nbsp;
                </div>
            </a>
        </td>
                <td >
            <a href="{$childCategoryItems[i].url}" class="id_company1">
                <div>
                    {$childCategoryItems[i].percent_volatile_weight}&nbsp;
                </div>
            </a>
        </td>
        <td >
            <a href="{$childCategoryItems[i].url}" class="id_company1">
                <div>
                    {$childCategoryItems[i].percent_volatile_volume}&nbsp;
                </div>
            </a>
        </td>
        <td align="center">
            {if $childCategoryItems[i].techSheetLink}
            <div>
                <a href='{$childCategoryItems[i].techSheetLink}' target="_blank">VIEW</a>
            </div>
			{else}
				&nbsp;
            {/if}
        </td>
		<td class="border_users_r" align="center">
            {if $childCategoryItems[i].msdsLink}
            <div>
                <a href='{$childCategoryItems[i].msdsLink}' target="_blank">VIEW</a>
                {*if $permissions.company.view*} 
                <!--| {*<a href='?action=deleteItem&itemID=MSDS_Sheet&itemsCount=1&item_0={$product.product_id}'>UNLINK</a>*}
				<a href='?action=deleteItem&category=MSDS_Sheet&id={$childCategoryItems[i].product_id}&departmentID={$request.id}'>UNLINK</a>
                {*/if*}-->
            </div>
            {else}           
				&nbsp;
            <div>
                    {if $permissions.company.view}
						<a href='?action=msdsUploader&step=edit&productID={$childCategoryItems[i].product_id}&itemID={$request.category}&id={$request.id}'>edit</a>
                    {else}
                    	--
                    {/if}
            </div>
            {/if}
        </td>
    </tr>
    {/section} 
	
	{if $smarty.section.i.total ==0}
		<tr align = 'center' height="173">						
			<td class="border_users_l border_users_b border_users_r" colspan='12'>
				No products in the company
			</td>						
		</tr>
	{elseif $smarty.section.i.total <=7}
		<tr height='{math equation="x - (y * 27)" x=200 y=$smarty.section.i.total}'>
		<td class="border_users_l border_users_b border_users_r" colspan='12'>
				&nbsp;
			</td>						
		</tr>
	{/if}
   
    {*END LIST*}

    <tr>
        <td height="25" class="users_u_bottom" colspan="6">
            &nbsp;
        </td>
        <td class="border_users" colspan="5">
        </td>
        <td class="users_u_bottom_r">
        </td>
    </tr>
</table>
{*PAGINATION*}
{include file="tpls:tpls/pagination.tpl"}
{*/PAGINATION*}
</form>	{*this FORM opened at controlInsideDepartment.tpl*}

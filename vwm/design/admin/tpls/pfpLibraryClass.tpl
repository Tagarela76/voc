

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
<div class="padd7" align="center">

{*PAGINATION*}
		{include file="tpls:tpls/paginationabc.tpl"}
{*/PAGINATION*}
<table class="users" cellspacing="0" cellpadding="0" align="center">
    <tr class="users_top_blue" height="27px">
        <td class="users_u_top_blue" width="10%">
            <span style='display:inline-block; width:60px;'> <a onclick="CheckAll(this)" style='color:white'>All</a>/<a style='color:white' onclick="unCheckAll(this)" >None</a></span>
        </td>
        
        <td class="" width="10%">
        	<a style='color:white;' onclick='$("#sort").attr("value","{if $sort==1}2{else}1{/if}"); $("#sortForm").submit();'>
            	<div style='width:100%;  color:white;'>						
                	ID
					{if $sort==1 || $sort==2}<img src="{if $sort==1}images/asc2.gif{/if}{if $sort==2}images/desc2.gif{/if}" alt=""/>{/if}				
				</div>					
			</a>                       
        </td>

        <td class="" width="50%">
        	<a style='color:white;' onclick='$("#sort").attr("value","{if $sort==3}4{else}3{/if}"); $("#sortForm").submit();'>
            	<div style='width:100%;  color:white;'>						
                	Description 	
					{if $sort==3 || $sort==4}<img src="{if $sort==3}images/asc2.gif{/if}{if $sort==4}images/desc2.gif{/if}" alt=""/>{/if}				
				</div>					
			</a> 
        </td>
        <td class="" width="20%">
        	<a style='color:white;' onclick='$("#sort").attr("value","{if $sort==3}4{else}3{/if}"); $("#sortForm").submit();'>
            	<div style='width:100%;  color:white;'>						
                	Ratio 	
					{if $sort==3 || $sort==4}<img src="{if $sort==3}images/asc2.gif{/if}{if $sort==4}images/desc2.gif{/if}" alt=""/>{/if}				
				</div>					
			</a> 
        </td>
        <td class="users_u_top_r_blue" width="20%">
        	<a style='color:white;' onclick='$("#sort").attr("value","{if $sort==3}4{else}3{/if}"); $("#sortForm").submit();'>
            	<div style='width:100%;  color:white;'>						
                	Products count 	
					{if $sort==3 || $sort==4}<img src="{if $sort==3}images/asc2.gif{/if}{if $sort==4}images/desc2.gif{/if}" alt=""/>{/if}				
				</div>					
			</a> 
        </td>
    </tr>
{if $pfps|@is_array and $pfps|@count > 0}
    {*BEGIN LIST*} 
    {foreach from=$pfps item=pfp} 
    {assign var='pfpid' value=$pfp->getId()}
	{assign var='subb' value=$request.subBookmark}
	{assign var='book' value=$request.category}
	{assign var='page' value=$request.page}
    {assign var='url' value="admin.php?action=viewDetails&category=pfpLibrary&bookmark=$book&subBookmark=$subb&id=$pfpid&page=$page"}
	<!-- Begin Highlighting -->
    <tr class="hov_company" height="10px"> 	
		
        <td class="border_users_b border_users_l border_users_r" >
			<input type="checkbox" value="{$pfp->getId()}" name="id[]">
        </td>

        <td class="border_users_b border_users_r" >
            <a href="{$url}" class="id_company1" title="{$pfp->getDescription()}">
                <div style="width:100%;">
                    {$pfp->getId()} &nbsp;
                </div>
            </a>
        </td>
        <td class="border_users_b border_users_r">
            <a href="{$url}" class="id_company1" title="">
                <div style="width:100%;" align="left">
                    {$pfp->getDescription()} &nbsp;
                </div>
            </a>
        </td>
        <td class="border_users_b border_users_r">
            <a href="{$url}" class="id_company1" title="{$mix->hoverMessage}">
                <div style="width:100%;">
                    {$pfp->getRatio()} &nbsp;
                </div>
            </a>
        </td>
        <td class="border_users_b border_users_r">
            <a href="{$url}" class="id_company1" title="{$mix->hoverMessage}">
                <div style="width:100%;">
                    {$pfp->getProductsCount()} &nbsp;
                </div>
            </a>
        </td>
    </tr>
    {/foreach} 
    <tr>
        <td colspan="5" class="border_users_l border_users_r">
            &nbsp;
        </td>
    </tr>
    {*END LIST*}
{else}
    {*BEGIN	EMPTY LIST*}
    <tr class="">
        <td colspan="5"class="border_users_l border_users_r" align="center">
            No pre formulated products for this supplier
        </td>
    </tr>
    {*END	EMPTY LIST*}
{/if}
    <tr>
        <td class="users_u_bottom">
        </td>
        <td colspan="3" height="15" class="border_users">
        </td>
        <td class="users_u_bottom_r">
        </td>
    </tr>
</table>
</div>
{*PAGINATION*}
{include file="tpls:tpls/paginationabc.tpl"}
{*/PAGINATION*}
</form>	{*close form that was opened at controlInsideDepartment.tpl*}

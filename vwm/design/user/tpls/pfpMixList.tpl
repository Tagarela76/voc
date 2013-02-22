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
    <tr class="users_top" height="27px">
        <td class="users_u_top" width="60px">
            <span style='display:inline-block; width:60px;'> <a onclick="CheckAll(this)" style='color:white'>All</a>/<a style='color:white' onclick="unCheckAll(this)" >None</a></span>
        </td>

        <td class="" width="60px">
        	<a style='color:white;' onclick='$("#sort").attr("value","{if $sort==1}2{else}1{/if}"); $("#sortForm").submit();'>
            	<div style='width:100%;  color:white;'>
                	ID
					{if $sort==1 || $sort==2}<img src="{if $sort==1}images/asc2.gif{/if}{if $sort==2}images/desc2.gif{/if}" alt=""/>{/if}
				</div>
			</a>
        </td>
		<td class="" >
        	<a style='color:white;' onclick='$("#sort").attr("value","{if $sort==3}4{else}3{/if}"); $("#sortForm").submit();'>
            	<div style='width:100%;  color:white;'>
                	Manufacturers/Suppliers
					{if $sort==3 || $sort==4}<img src="{if $sort==3}images/asc2.gif{/if}{if $sort==4}images/desc2.gif{/if}" alt=""/>{/if}
				</div>
			</a>
        </td>
        <td class="" >
        	<a style='color:white;' onclick='$("#sort").attr("value","{if $sort==3}4{else}3{/if}"); $("#sortForm").submit();'>
            	<div style='width:100%;  color:white;'>
                	Description
					{if $sort==3 || $sort==4}<img src="{if $sort==3}images/asc2.gif{/if}{if $sort==4}images/desc2.gif{/if}" alt=""/>{/if}
				</div>
			</a>
        </td>
        <td class="" width="120px">
        	<a style='color:white;' onclick='$("#sort").attr("value","{if $sort==3}4{else}3{/if}"); $("#sortForm").submit();'>
            	<div style='width:100%;  color:white;'>
                	Ratio
					{if $sort==3 || $sort==4}<img src="{if $sort==3}images/asc2.gif{/if}{if $sort==4}images/desc2.gif{/if}" alt=""/>{/if}
				</div>
			</a>
        </td>
        <td class="" width="120px">
        	<a style='color:white;' onclick='$("#sort").attr("value","{if $sort==3}4{else}3{/if}"); $("#sortForm").submit();'>
            	<div style='width:100%;  color:white;'>
                	Products count
					{if $sort==3 || $sort==4}<img src="{if $sort==3}images/asc2.gif{/if}{if $sort==4}images/desc2.gif{/if}" alt=""/>{/if}
				</div>
			</a>
        </td>
        <td class="users_u_top_r" width="30%">
        	<a style='color:white;' onclick='$("#sort").attr("value","{if $sort==5}6{else}5{/if}"); $("#sortForm").submit();'>
            	<div style='width:100%;  color:white;'>
                	Apply
					{if $sort==5 || $sort==6}<img src="{if $sort==5}images/asc2.gif{/if}{if $sort==6}images/desc2.gif{/if}" alt=""/>{/if}
				</div>
			</a>
        </td>
    </tr>
{if $pfps|@is_array and $pfps|@count > 0}
    {*BEGIN LIST*}
    {foreach from=$pfps item=pfp}
    {assign var='pfpid' value=$pfp->getId()}
    {assign var='departmentID' value=$smarty.request.id}
    {assign var='url' value="?action=viewPFPDetails&category=mix&id=$pfpid&departmentID=$departmentID"}
	<!-- Begin Highlighting -->
    <tr class="hov_company" height="10px">

        <td class="border_users_b border_users_l border_users_r" >
			<input type="checkbox" value="{$pfp->getId()|escape}" name="id[]">
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
				<div style="width:100%;">
                    {assign var="pfpProducts" value=$pfp->getProducts()}
						{foreach from=$pfpProducts item=item}
							{if $item->isPrimary()}
								{$item->supplier|escape} &nbsp;
							{/if}
						{/foreach}
                </div>
            </a>
        </td>
        <td class="border_users_b border_users_r">
            <a href="{$url}" class="id_company1" title="">
                <div style="width:100%;" align="left">
                    {$pfp->getDescription()|escape} &nbsp;
                </div>
				<div>
					<table style="font-size: 10; color: #8B7765;">
                        {assign var="pfpProducts" value=$pfp->getProducts()}
						{foreach from=$pfpProducts item=item}
							<tr>
								<td>{$item->product_nr|escape}</td>
								<td>{$item->name|escape}</td>
							</tr>
						{/foreach}
					</table>
				</div>
            </a>
        </td>
        <td class="border_users_b border_users_r">
            <a href="{$url}" class="id_company1" title="{$mix->hoverMessage}">
                <div style="width:100%;">
                    {*if $pfp->getIsProprietary() != 1*}
                        {$pfp->getRatio()} &nbsp;{if $pfp->isRangePFP}(with range){/if}
                    {*else}
                        &nbsp;IP
                    {/if*}
                </div>
            </a>
        </td>
        <td class="border_users_b border_users_r">
            <a href="{$url}" class="id_company1" title="{$mix->hoverMessage}">
                <div style="width:100%;">
                    {$pfp->getProductsCount()|escape} &nbsp;
                </div>
            </a>
        </td>
        <td class="border_users_b border_users_r">
            <a href="?action=addItem&category=mix&departmentID={$smarty.request.id}&pfp={$pfp->getId()}" target="_blank" class="id_company1" title="{$mix->hoverMessage}">
                <div style="width:100%;" >
                    Create mix using this pre formulated products
                </div>
            </a>
        </td>
    </tr>
    {/foreach}
    <tr>
        <td colspan="7" class="border_users_l border_users_r">
            &nbsp;
        </td>
    </tr>
    {*END LIST*}
{else}
    {*BEGIN	EMPTY LIST*}
    <tr class="">
        <td colspan="7"class="border_users_l border_users_r" align="center">
            No pre formulated products in the department
        </td>
    </tr>
    {*END	EMPTY LIST*}
{/if}
    <tr>
        <td class="users_u_bottom">
        </td>
        <td class="users_u_bottom_r" colspan="6" height="15">
        </td>
    </tr>
</table>
{*PAGINATION*}
{include file="tpls:tpls/pagination.tpl"}
{*/PAGINATION*}
</form>	{*close form that was opened at controlInsideDepartment.tpl*}

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
        <td class="users_u_top" width="60">
            <span style='display:inline-block; width:60px;'> <a onclick="CheckAll(this)" style='color:white'>All</a>/<a style='color:white' onclick="unCheckAll(this)" >None</a></span>
        </td>
        <td class="" width="10%">
        	<a style='color:white;' onclick='$("#sort").attr("value","{if $sort==1}2{else}1{/if}"); $("#sortForm").submit();'>
            	<div style='width:100%;  color:white;'>
                	Mix ID
					{if $sort==1 || $sort==2}<img src="{if $sort==1}images/asc2.gif{/if}{if $sort==2}images/desc2.gif{/if}" alt=""/>{/if}
				</div>
			</a>
        </td>
		<td class="" width="12%">
        	<a style='color:white;'>
            	<div style='width:100%;  color:white;'>
                	Product Name					
				</div>
			</a>
        </td>
		<td class="" width="10%">
        	<a style='color:white;'>
            	<div style='width:100%;  color:white;'>
                	Add Job
				</div>
			</a>
        </td>
        <td class="" width="15%">
        	<a style='color:white;' onclick='$("#sort").attr("value","{if $sort==3}4{else}3{/if}"); $("#sortForm").submit();'>
            	<div style='width:100%;  color:white;'>
                	Description
					{if $sort==3 || $sort==4}<img src="{if $sort==3}images/asc2.gif{/if}{if $sort==4}images/desc2.gif{/if}" alt=""/>{/if}
				</div>
			</a>
        </td>
		<td class="" width="15%">
        	<a style='color:white;'>
            	<div style='width:100%;  color:white;'>
                	R/O Description					
				</div>
			</a>
        </td>
		<td class="" width="13%">
        	<a style='color:white;'>
            	<div style='width:100%;  color:white;'>
                	Contact					
				</div>
			</a>
        </td>
		<td class="" width="5%">
        	<a style='color:white;'>
            	<div style='width:100%;  color:white;'>
                	R/O VIN number				
				</div>
			</a>
        </td>
        <td class="" width="10%">
        	<a style='color:white;' onclick='$("#sort").attr("value","{if $sort==5}6{else}5{/if}"); $("#sortForm").submit();'>
            	<div style='width:100%;  color:white;'>
                	VOC
					{if $sort==5 || $sort==6}<img src="{if $sort==5}images/asc2.gif{/if}{if $sort==6}images/desc2.gif{/if}" alt=""/>{/if}
				</div>
			</a>
        </td>
        <td class="users_u_top_r" width="15%">
        	<a style='color:white;' onclick='$("#sort").attr("value","{if $sort==7}8{else}7{/if}"); $("#sortForm").submit();'>
            	<div style='width:100%;  color:white;'>
                	Creation Date
					{if $sort==7 || $sort==8}<img src="{if $sort==7}images/asc2.gif{/if}{if $sort==8}images/desc2.gif{/if}" alt=""/>{/if}
				</div>
			</a>
        </td>		
    </tr>
{if $childCategoryItems|@is_array and $childCategoryItems|@count > 0}
    {*BEGIN LIST*}
    {foreach from=$childCategoryItems item=mix}
	<!-- Begin Highlighting -->
    <tr {if $mix->valid  eq "valid"}
 			class="hov_company"
		{else}
			{if $mix->valid  eq "invalid"}
			 class="us_red"
			{else}
 			class="us_orange"
			{/if}
		{/if}
 	height="10px">
        <!-- End Highlighting -->

        <td class="border_users_b border_users_l" >
            {if $mix->valid eq "valid"}
				<span class="ok">&nbsp;</span>
            {else}
            	{if $mix->valid eq "invalid"}
				<span class="error">&nbsp;</span>
           		{else}
				<span class="warning">&nbsp;</span>
            	{/if}
            {/if}
			<input type="checkbox" value="{$mix->mix_id}" name="id[]">
        </td>
        <td class="border_users_b border_users_r" >
            <a href="{$mix->url}" class="id_company1" title="{$mix->hoverMessage}">
                <div style="width:100%;">
                    {$mix->mix_id} &nbsp;
                </div>
            </a>
        </td>
		<td class="border_users_b border_users_r">
            <a href="{$mix->url}" class="id_company1" title="{$mix->hoverMessage}">
                <div style="width:100%;">
                    {assign var="products" value=$mix->getProducts()}
						{foreach from=$products item=item}
							{if $item->is_primary}
								{$item->name|escape} &nbsp;
							{/if}
						{/foreach}
				</div>		
            </a>
        </td>
		<td class="border_users_b border_users_r" >
			<div style="width:100%;">
				{if !$mix->hasChild}
                    <a href="?action=addItem&category=mix&departmentID={$request.id|escape:'url'}&parentMixID={$mix->mix_id|escape:'url'}&workOrderId={$mix->wo_id|escape:'url'}" title="Add child job">add</a> &nbsp;
				{/if}
				&nbsp;
			</div>
        </td>
        <td class="border_users_b border_users_r">
            <a href="{$mix->url}" class="id_company1" title="{$mix->hoverMessage}">
                <div style="width:100%;" align="left">
                    {$mix->description|escape} &nbsp;
                </div>
            </a>
        </td>
		<td class="border_users_b border_users_r">
            <a href="{$mix->url}" class="id_company1" title="{$mix->hoverMessage}">
                <div style="width:100%;" align="left">
					{assign var="workOrder" value=$mix->getWorkOrder()}
                    {$workOrder->description|escape} &nbsp;
                </div>
            </a>
        </td>
		<td class="border_users_b border_users_r">
            <a href="{$mix->url}" class="id_company1" title="{$mix->hoverMessage}">
                <div style="width:100%;" align="left">					
                    {$workOrder->customer_name|escape} &nbsp;
                </div>
            </a>
        </td>
		<td class="border_users_b border_users_r">
            <a href="{$mix->url}" class="id_company1" title="{$mix->hoverMessage}">
                <div style="width:100%;" align="left">					
                    {$workOrder->vin|escape} &nbsp;
                </div>
            </a>
        </td>
        <td class="border_users_b border_users_r">
            <a href="{$mix->url}" class="id_company1" title="{$mix->hoverMessage}">
                <div style="width:100%;">
                    {$mix->voc} &nbsp;
                </div>
            </a>
        </td>
        <td class="border_users_b border_users_r">
            <a href="{$mix->url}" class="id_company1" title="{$mix->hoverMessage}">
                <div style="width:100%;" >
                    {$mix->creation_time} &nbsp;
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
    <tr class="">
        <td colspan="10"class="border_users_l border_users_r" align="center">
            No mixes in the department
        </td>
    </tr>
    {*END	EMPTY LIST*}
{/if}
    <tr>
        <td class="users_u_bottom" colspan="9" height="15">
        </td>
        <td class="users_u_bottom_r">
        </td>
    </tr>
</table>
{*PAGINATION*}
{include file="tpls:tpls/pagination.tpl"}
{*/PAGINATION*}
</form>	{*close form that was opened at controlInsideDepartment.tpl*}

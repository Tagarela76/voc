<div class="padd7" align="center">
    {if $color eq "green"}
		{include file="tpls:tpls/notify/greenNotify.tpl" text=$message}
    {/if}
    {if $color eq "orange"}
		{include file="tpls:tpls/notify/orangeNotify.tpl" text=$message}
    {/if}
    {if $color eq "blue"}
		{include file="tpls:tpls/notify/blueNotify.tpl" text=$message}
    {/if}

    {*PAGINATION*}
	{include file="tpls:tpls/pagination.tpl"}
	{*/PAGINATION*}

	<input type='hidden' id='sort'>
    <table class="users" width="100%" cellspacing="0" cellpadding="0" bgcolor="#EFEFEF">
        <tr class="users_header_blue">
            <td width="60">
                <div class="users_header_blue_l"><div><span style='display:inline-block; width:60px;'> <a onclick="CheckAll(this)" style='color:white'>All</a>/<a style='color:white' onclick="unCheckAll(this)" >None</a></span></div></div>
            </td>
            <td width="110">
				<div>
					<a style='color:white;' onclick='$("#sort").attr("value","{if $sort==1}2{else}1{/if}"); $("#sortForm").submit();'>
						ID Number
			{if $sort==1 || $sort==2}<img src="{if $sort==1}images/asc2.gif{/if}{if $sort==2}images/desc2.gif{/if}" alt=""/>	{/if}
		</a>
	</div>
</td>
<td>
		<div>
			<a style='color:white;' onclick='$("#sort").attr("value","{if $sort==3}4{else}3{/if}"); $("#sortForm").submit();'>
				Department name
	{if $sort==3 || $sort==4}<img src="{if $sort==3}images/asc2.gif{/if}{if $sort==4}images/desc2.gif{/if}" alt=""/>{/if}
</a>
</div>
</td>

<td width="390">
	<div class="users_header_blue_r">
		<div>Gauge</div>
	</div>
</td>
</tr>

{if $childCategoryItems|@count > 0}
	{foreach from=$childCategoryItems item=department}
		<tr class="hov_company" height="10px">
            <td class="border_users_l border_users_b">
                <input type="checkbox" value="{$department.id}" name="id[]">
            </td>
            <td class="border_users_b border_users_l">
                <a {if $permissions.viewItem}href="{$department.url}"{/if}>
                    <div style="width:100%;">
                        {$department.id}
                    </div>
                </a>
            </td>
            <td class="border_users_b border_users_l">
                <a {if $permissions.viewItem}href="{$department.url}"{/if}>
                    <div style="width:100%;">
                        {$department.name|escape}
                    </div>
                </a>
            </td>
			<td style="width:450px;" class="border_users_b border_users_l border_users_r">
				<table  style="width: 100%; border-spacing: 0px;border-collapse: collapse;">
						{include file="tpls:tpls/vocIndicator.tpl" currentUsage=$department.gauge.currentUsage
							vocLimit=$department.gauge.vocLimit
							pxCount=$department.gauge.pxCount }					
					{if $department.time_gauge.timeLimit!=0}
						{include file="tpls:tpls/timeProductIndicator.tpl" overritenCurrenProductTime=$department.time_gauge.currentUsage
							overritenTimeProductLimit=$department.time_gauge.timeLimit
							overritenTimeProductCount=$department.time_gauge.pxCount
							overritenUnitType=$department.time_gauge.unitType}
					{/if}
						
					{if $department.qty_gauge.qtyLimit!=0}
						{include file="tpls:tpls/qtyProductIndicator.tpl" overritenCurrenProductQty=$department.qty_gauge.currentUsage
							overritenQtyProductLimit=$department.qty_gauge.qtyLimit
							overritenQtyProductCount=$department.qty_gauge.pxCount
							overritenUnitType=$department.qty_gauge.unitType}
					{/if}

					{if $department.nox_gauge.limit!=0}
						{include file="tpls:tpls/noxIndicator.tpl" _gauge=$department.nox_gauge}
					{/if}
				</table>
			</td>
        </tr>
	{/foreach}
{else}

	{*BEGIN	EMPTY LIST*}
	<tr>
		<td colspan="4"class="border_users_l border_users_r" align="center">
			No departments in the list
		</td>
	</tr>
	{*END	EMPTY LIST*}

{/if}
<tr>
	<td class="users_u_bottom ">
	</td>
	<td colspan="3" bgcolor="" height="30" class="users_u_bottom_r">
	</td>
</tr>
</table>
</div>
{*PAGINATION*}
{include file="tpls:tpls/pagination.tpl"}
{*/PAGINATION*}
</form>	

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

	<input type='hidden' id='sort'>
    <table class="users" width="100%" cellspacing="0" cellpadding="0" bgcolor="#EFEFEF">
        <tr class="users_header_blue">
            <td width="60">
                <div class="users_header_blue_l"><div><span style='display:inline-block; width:60px;'> <a onclick="CheckAll(this)" style='color:white'>All</a>/<a style='color:white' onclick="unCheckAll(this)" >None</a></span></div></div>
            </td>
			<td>
				<div class="users_header_blue">
					Number
				</div>
			</td>

			<td>
				<div class="users_header_blue">
					Description
				</div>
			</td>
			<td>
				<div class="users_header_blue">
					Customer Name
				</div>
			</td>
			<td>
				<div class="users_header_blue_r">
					<div>Status</div>
				</div>
			</td>
		</tr>

{if $childCategoryItems|@count > 0}
	{foreach from=$childCategoryItems item=workOrder}
		<tr class="hov_company" height="10px">
            <td class="border_users_l border_users_b">
                <input type="checkbox" value="{$workOrder->id}" name="id[]">
            </td>
            <td class="border_users_b border_users_l">
				<a {if $permissions.viewItem}href="{$workOrder->url}"{/if}>
					<div style="width:100%;">
						{$workOrder->number}
					</div>
				</a>
            </td>
            <td class="border_users_b border_users_l">
				<a {if $permissions.viewItem}href="{$workOrder->url}"{/if}>
					<div style="width:100%;">
						{$workOrder->description|escape}
					</div>
				</a>
            </td>
			<td style="width:250px;" class="border_users_b border_users_l border_users_l">
				<a {if $permissions.viewItem}href="{$workOrder->url}"{/if}>
					<div style="width:100%;">
						{$workOrder->customer_name|escape}
					</div>
				</a>
			</td>
			<td style="width:250px;" class="border_users_b border_users_l border_users_r">
				<a {if $permissions.viewItem}href="{$workOrder->url}"{/if}>
					<div style="width:100%;">
						{$workOrder->status|escape}
					</div>
				</a>
			</td>
        </tr>
	{/foreach}
{else}

	{*BEGIN	EMPTY LIST*}
	<tr>
		<td colspan="4"class="border_users_l border_users_r" align="center">
			No work orders in the list
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

</form>	

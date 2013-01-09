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
    <table class="users" width="100%" cellspacing="0" cellpadding="0">
		{*header*}
		<tr class="users_header_blue">
			<td width="60">
				<div class="users_header_blue_l"><div><span style='display:inline-block; width:60px;'> <a onclick="CheckAll(this)" style='color:white'>All</a>/<a style='color:white' onclick="unCheckAll(this)" >None</a></span></div></div>
            </td>
            <td width="100">
                <div>ID Number</div>
            </td>
            <td>
				<div> Facility name</div>
            </td>
			<td>
				<div>Location/Contact</div>
            </td>
			<td>
				<div>State</div>
            </td>
	     <td>
				<div class="users_header_blue_r"><div>Gauge</div></div>
            </td>

        </tr>
		{*/header*}

		{if $childCategoryItems|@count > 0}
			{foreach from=$childCategoryItems item=facility}
				<tr class="hov_company" height="10px">
					<td class="border_users_b border_users_l">
						<input type="checkbox" value="{$facility.id}" name="id[]">
					</td>
					<td class="border_users_b border_users_l">
						<a {if $permissions.viewItem}href="{$facility.url}"{/if}>
							<div style="width:100%;">
								{$facility.id}
							</div>
						</a>
					</td>
					<td class="border_users_b border_users_l">
						<a {if $permissions.viewItem}href="{$facility.url}"{/if}>
							<div style="width:100%;">
								{$facility.name|escape}
							</div>
						</a>
					</td>					
					<td class="border_users_b border_users_l">
						<a {if $permissions.viewItem}href="{$facility.url}"{/if}>
							<div style="width:100%;">
								{$facility.address|escape},&nbsp;{$facility.contact|escape}&nbsp({$facility.phone|escape})
							</div>
						</a>
					</td>
					<td class="border_users_b border_users_l" >
						<a {if $permissions.viewItem}href="{$facility.url}"{/if}>
							<div style="width:100%;">
								{$facility.stateName|escape}
							</div>
						</a>
					</td>
					<td class="border_users_b border_users_l border_users_r" style="width:390px;">
						<a {if $permissions.viewItem}href="{$facility.url}"{/if}>
						<table style="border-width: 0px;">													
								{include file="tpls:tpls/vocIndicator.tpl" currentUsage=$facility.gauge.currentUsage
										vocLimit=$facility.gauge.vocLimit
										pxCount=$facility.gauge.pxCount }
													
						</table>
						</a>
					</td>
				</tr>
			{/foreach}
		{else}

			{*BEGIN	EMPTY LIST*}
			<tr>
				<td colspan="6"class="border_users_l border_users_r" align="center">
					No facilities in the list
				</td>
			</tr>
			{*END	EMPTY LIST*}

		{/if}

    </table>
	{*footer*}
	<div align="center"><div class="users_bottom"><div class="users_footer_l"><div class="users_footer_r"><div class="users_footer"></div></div></div></div></div>
	
        {*/footer*}
</div>

</form>	{*close FORM tag opened at controlCategoriesList.tpl*}

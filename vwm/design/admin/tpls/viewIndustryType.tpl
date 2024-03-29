{if $color eq "green"}
	{include file="tpls:tpls/notify/greenNotify.tpl" text=$message}
{/if}

{if $color eq "orange"}
	{include file="tpls:tpls/notify/orangeNotify.tpl" text=$message}
{/if}

{if $color eq "blue"}
	{include file="tpls:tpls/notify/blueNotify.tpl" text=$message}
{/if}
<div style="padding:7px;">
	<table class="users"  align="center" cellpadding="0" cellspacing="0">
		<tr class="users_top_yellowgreen" >
			<td class="users_u_top_yellowgreen" width="27%" height="30" >
				<span >View details</span>
			</td>
			<td class="users_u_top_r_yellowgreen" width="300">
			</td>
		</tr>

		<tr>
			<td class="border_users_l border_users_b" height="20">
				1.	Industry Type ID :
			</td>
			<td class="border_users_l border_users_b border_users_r">
				<div align="left" >&nbsp;{$typeDetails->id}</div>
			</td>
		</tr>
		<tr>
			<td class="border_users_l border_users_b" height="20">
				2.	Industry Type :
			</td>
			<td class="border_users_l border_users_b border_users_r">
				<div align="left" >	&nbsp;{$typeDetails->type}</div>
			</td>
		</tr>
		<tr>
			<td class="border_users_l border_users_b" height="20">
				3.	{$browseCategoryMix->name4display} :
			</td>
			<td class="border_users_l border_users_b border_users_r">
				<div align="left" >	&nbsp;{$columnsSettingsMixValue}</div>
			</td>
		</tr>
		<tr>
			<td class="border_users_l border_users_r" colspan="2" style="padding:5px 3px 0 3px">
				<table width="100%" cellpadding="0" cellspacing="0" >

					<tr class="users_top_lightgray">
						<td class="users_u_top_lightgray" height="25px">Industry Sub-Categories</td>
						<td class="users_u_top_r_lightgray" >&nbsp;</td></tr>
					{if $subIndustryTypes|@count gt 0}
						<tr bgcolor="#e3e3e3">
							<td  class="border_users_l border_users_b" height="20" width="50%">
								Industry Sub-Category ID
							</td>
							<td class="border_users_l border_users_r border_users_b">
								<div align="left">Industry Sub-Category Name</div>

							</td>
						</tr>

						{foreach from=$subIndustryTypes item=subType}
							<tr class="">
								<td  class="border_users_l border_users_b" height="20" width="50%">
									<div align="left">&nbsp;{$subType->id}</div>
								</td>
								<td class="border_users_l border_users_r border_users_b">
									<div align="left">&nbsp;{$subType->type}</div>

								</td>
							</tr>
						{/foreach}

					{else}
						<tr>
							<td colspan=2 class="border_users_l border_users_r border_users_b" height="40">
								<div style="text-align: center; font-weight: bold;">No Sub-Categories in Industry Type!</div>
							</td>
						</tr>
					{/if}
				</table>
			</td>
		</tr>

		<tr>
			<td height="20" class="users_u_bottom">
				&nbsp;
			</td>
			<td height="20" class="users_u_bottom_r">
				&nbsp;
			</td>
		</tr>
	</table>
	<!-- Label List -->
	<table class="users"  align="center" cellpadding="0" cellspacing="0">
		<tr class="users_top_yellowgreen" >
			<td class="users_u_top_yellowgreen" width="27%" height="30" >
				<span >Label ID</span>
			</td>
			<td class="users_u_top_r_yellowgreen" width="300">
				<span >Label Text</span>
			</td>
		</tr>
        <tr>
            <td class="border_users_l border_users_b" height="20">
                {$companyLevelLabelRepairOrderDefault->name4display}
            </td>
            <td class="border_users_l border_users_b border_users_r">
                <div align="left" >&nbsp;{$repairOrderLabel}</div>
            </td>
        </tr>
        
        <tr>
            <td class="border_users_l border_users_b" height="20">
                {$companyLevelLabelProductNameDefault->name4display}
            </td>
            <td class="border_users_l border_users_b border_users_r">
                <div align="left" >&nbsp;{$productNameLabel}</div>
            </td>
        </tr>

        <tr>
            <td class="border_users_l border_users_b" height="20">
                {$companyLevelLabelAddJobDefault->name4display}
            </td>
            <td class="border_users_l border_users_b border_users_r">
                <div align="left" >&nbsp;{$addJobLabel}</div>
            </td>
        </tr>
        <tr>
            <td class="border_users_l border_users_b" height="20">
                {$companyLevelLabelDescriptionDefault->name4display}
            </td>
            <td class="border_users_l border_users_b border_users_r">
                <div align="left" >&nbsp;{$descriptionLabel}</div>
            </td>
        </tr>
        
        <tr>
            <td class="border_users_l border_users_b" height="20">
                {$companyLevelLabelRODescriptionDefault->name4display}
            </td>
            <td class="border_users_l border_users_b border_users_r">
                <div align="left" >&nbsp;{$roDescriptionLabel}</div>
            </td>
        </tr>
        
        <tr>
            <td class="border_users_l border_users_b" height="20">
                {$companyLevelLabelContactDefault->name4display}
            </td>
            <td class="border_users_l border_users_b border_users_r">
                <div align="left" >&nbsp;{$contactLabel}</div>
            </td>
        </tr>
        <tr>
            <td class="border_users_l border_users_b" height="20">
                {$companyLevelLabelROVinNumberDefault->name4display}
            </td>
            <td class="border_users_l border_users_b border_users_r">
                <div align="left" >&nbsp;{$roVinNumberLabel}</div>
            </td>
        </tr>
        <tr>
            <td class="border_users_l border_users_b" height="20">
                {$companyLevelLabelVocDefault->name4display}
            </td>
            <td class="border_users_l border_users_b border_users_r">
                <div align="left" >&nbsp;{$vocLabel}</div>
            </td>
        </tr>
        <tr>
            <td class="border_users_l border_users_b" height="20">
                {$companyLevelLabelCreationDateDefault->name4display}
            </td>
            <td class="border_users_l border_users_b border_users_r">
                <div align="left" >&nbsp;{$creationDateLabel}</div>
            </td>
        </tr>
        <tr>
            <td class="border_users_l border_users_b" height="20">
                {$companyLevelLabelUnitTypeDefault->name4display}
            </td>
            <td class="border_users_l border_users_b border_users_r">
                <div align="left" >&nbsp;{$unitTypeLabel}</div>
            </td>
        </tr>
		<tr>
            <tr>
            <td class="border_users_l border_users_b" height="20">
                {$companyLevelLabelPaintShopProductDefault->name4display}
            </td>
            <td class="border_users_l border_users_b border_users_r">
                <div align="left" >&nbsp;{$paintShopProductLabel}</div>
            </td>
        </tr>
        <tr>
            <td class="border_users_l border_users_b" height="20">
                {$companyLevelLabelBodyShopProductDefault->name4display}
            </td>
            <td class="border_users_l border_users_b border_users_r">
                <div align="left" >&nbsp;{$bodyShopProductLabel}</div>
            </td>
        </tr>
        <tr>
            <td class="border_users_l border_users_b" height="20">
                {$companyLevelLabelDetailingShopProductDefault->name4display}
            </td>
            <td class="border_users_l border_users_b border_users_r">
                <div align="left" >&nbsp;{$detailingShopProductLabel}</div>
            </td>
        </tr>
        
        <tr>
            <td class="border_users_l border_users_b" height="20">
                {$companyLevelLabelFuelAndOilProductDefault->name4display}
            </td>
            <td class="border_users_l border_users_b border_users_r">
                <div align="left" >&nbsp;{$fuelAndOilProductLabel}</div>
            </td>
        </tr>
		<tr>
            <td class="border_users_l border_users_b" height="20">
                {$companyLevelLabelSpentTimeDefault->name4display}
            </td>
            <td class="border_users_l border_users_b border_users_r">
                <div align="left" >&nbsp;{$spentTimeLabel}</div>
            </td>
        </tr>
		<tr>
			<td height="20" class="users_u_bottom">
				&nbsp;
			</td>
			<td height="20" class="users_u_bottom_r">
				&nbsp;
			</td>
		</tr>
	</table>
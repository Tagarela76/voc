{literal}
<script type="text/javascript">
	$(function() {
		//	global object
		industryTypePage.industryTypeId = {/literal} {$request.id} {literal};
	});
</script>
{/literal}
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
	<form method='POST' action='admin.php?action={$request.action}&category=industryType{if $request.action neq "addItem"}&id={$request.id}{/if}'>
		<table class="users" align="center" cellpadding="0" cellspacing="0">
			<tr class="users_u_top_size users_top">
				<td class="users_u_top" width="27%" height="30" >
					<span >{if $currentOperation eq "addItem"}Adding for a new Industry Type{else}Editing Industry Type{/if}</span>
				</td>
				<td class="users_u_top_r" width="300">
					&nbsp;
				</td>				
			</tr>
			<tr height="10px">
				<td class="border_users_l border_users_b" height="20">
					Industry Type:
				</td>
				<td class="border_users_l border_users_b border_users_r">
					<div align="left"><input type='text' name='type' value='{$data->type}'/></div>

					{foreach from=$violationList item="violation"}
                        {if $violation->getPropertyPath() eq 'type' || $violation->getPropertyPath() eq 'uniqueName'}							
							{*ERROR*}					
							<div class="error_img" style="float: left;"><span class="error_text">{$violation->getMessage()}</span></div>
							{*/ERROR*}						    
                        {/if}
                    {/foreach}								
				</td>
			</tr>
			<tr height="10px">
				<td class="border_users_l border_users_b" height="20">
					{$browseCategoryMix->name4display}:
				</td>
				<td class="border_users_l border_users_b border_users_r">
					<div align="left" style="float: left;">	
					<div id="browse_category_mix">{$columnsSettingsMixValue|escape} 
						{foreach from=$columnsSettingsMixValueArray item="browseCategoryMixValue"}
							<input type='hidden' name='browseCategoryMix_id[]' id='browseCategoryMix_id[]' value="{$browseCategoryMixValue}" />
						{/foreach}	
					</div>
					<a href="#" onclick="industryTypePage.manageDisplayColumnsMix.openDialog();">edit</a>
				</div>
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
		{if $currentOperation eq "edit"}
		<br>	
		<table class="users" align="center" cellpadding="0" cellspacing="0">
			<tr class="users_u_top_size users_top">
				<td class="users_u_top" width="27%" height="30" >
					<span >Editing Label</span>
				</td>
				<td class="users_u_top_r" width="300">
					&nbsp;
				</td>				
			</tr>
            <tr height="10px">
                <td class="border_users_l border_users_b" height="20">
                    {$companyLevelLabelRepairOrderDefault->name4display}
                </td>
                <td class="border_users_l border_users_b border_users_r">
                    <div align="left"><input type='text' name="{$companyLevelLabelRepairOrderDefault->label_id}" id="{$companyLevelLabelRepairOrderDefault->label_id}" value='{$repairOrderLabel}'/></div>
                    {if $errors.repair_order eq 'true'}							
                        {*ERROR*}					
                        <div class="error_img" style="float: left;"><span class="error_text">This value should not be blank</span></div>
                        {*/ERROR*}						    
                    {/if}
                </td>
            </tr>
            <tr height="10px">
                <td class="border_users_l border_users_b" height="20">
                    {$companyLevelLabelProductNameDefault->name4display}
                </td>
                <td class="border_users_l border_users_b border_users_r">
                    <div align="left"><input type='text' name="{$companyLevelLabelProductNameDefault->label_id}" id="{$companyLevelLabelProductNameDefault->label_id}" value='{$productNameLabel}'/></div>
                    {if $errors.product_name eq 'true'}							
                        {*ERROR*}					
                        <div class="error_img" style="float: left;"><span class="error_text">This value should not be blank</span></div>
                        {*/ERROR*}						    
                    {/if}
                </td>
            </tr>
            <tr height="10px">
                <td class="border_users_l border_users_b" height="20">
                    {$companyLevelLabelAddJobDefault->name4display}
                </td>
                <td class="border_users_l border_users_b border_users_r">
                    <div align="left"><input type='text' name="{$companyLevelLabelAddJobDefault->label_id}" id="{$companyLevelLabelAddJobDefault->label_id}" value='{$addJobLabel}'/></div>
                    {if $errors.add_job eq 'true'}							
                        {*ERROR*}					
                        <div class="error_img" style="float: left;"><span class="error_text">This value should not be blank</span></div>
                        {*/ERROR*}						    
                    {/if}
                </td>
            </tr>
            <tr height="10px">
                <td class="border_users_l border_users_b" height="20">
                    {$companyLevelLabelDescriptionDefault->name4display}
                </td>
                <td class="border_users_l border_users_b border_users_r">
                    <div align="left"><input type='text' name="{$companyLevelLabelDescriptionDefault->label_id}" id="{$companyLevelLabelDescriptionDefault->label_id}" value='{$descriptionLabel}'/></div>
                    {if $errors.description eq 'true'}							
                        {*ERROR*}					
                        <div class="error_img" style="float: left;"><span class="error_text">This value should not be blank</span></div>
                        {*/ERROR*}						    
                    {/if}
                </td>
            </tr>
            <tr height="10px">
                <td class="border_users_l border_users_b" height="20">
                    {$companyLevelLabelRODescriptionDefault->name4display}
                </td>
                <td class="border_users_l border_users_b border_users_r">
                    <div align="left"><input type='text' name="{$companyLevelLabelRODescriptionDefault->label_id}" id="{$companyLevelLabelRODescriptionDefault->label_id}" value='{$roDescriptionLabel}'/></div>
                    {if $errors.r_o_description eq 'true'}							
                        {*ERROR*}					
                        <div class="error_img" style="float: left;"><span class="error_text">This value should not be blank</span></div>
                        {*/ERROR*}						    
                    {/if}
                </td>
            </tr>
            <tr height="10px">
                <td class="border_users_l border_users_b" height="20">
                    {$companyLevelLabelContactDefault->name4display}
                </td>
                <td class="border_users_l border_users_b border_users_r">
                    <div align="left"><input type='text' name="{$companyLevelLabelContactDefault->label_id}" id="{$companyLevelLabelContactDefault->label_id}" value='{$contactLabel}'/></div>
                    {if $errors.contact eq 'true'}							
                        {*ERROR*}					
                        <div class="error_img" style="float: left;"><span class="error_text">This value should not be blank</span></div>
                        {*/ERROR*}						    
                    {/if}
                </td>
            </tr>
            <tr height="10px">
                <td class="border_users_l border_users_b" height="20">
                    {$companyLevelLabelROVinNumberDefault->name4display}
                </td>
                <td class="border_users_l border_users_b border_users_r">
                    <div align="left"><input type='text' name="{$companyLevelLabelROVinNumberDefault->label_id}" id="{$companyLevelLabelROVinNumberDefault->label_id}" value='{$roVinNumberLabel}'/></div>
                    {if $errors.r_o_vin_number eq 'true'}							
                        {*ERROR*}					
                        <div class="error_img" style="float: left;"><span class="error_text">This value should not be blank</span></div>
                        {*/ERROR*}						    
                    {/if}
                </td>
            </tr>
            <tr height="10px">
                <td class="border_users_l border_users_b" height="20">
                    {$companyLevelLabelVocDefault->name4display}
                </td>
                <td class="border_users_l border_users_b border_users_r">
                    <div align="left"><input type='text' name="{$companyLevelLabelVocDefault->label_id}" id="{$companyLevelLabelVocDefault->label_id}" value='{$vocLabel}'/></div>
                    {if $errors.voc eq 'true'}							
                        {*ERROR*}					
                        <div class="error_img" style="float: left;"><span class="error_text">This value should not be blank</span></div>
                        {*/ERROR*}						    
                    {/if}
                </td>
            </tr>
            <tr height="10px">
                <td class="border_users_l border_users_b" height="20">
                    {$companyLevelLabelCreationDateDefault->name4display}
                </td>
                <td class="border_users_l border_users_b border_users_r">
                    <div align="left"><input type='text' name="{$companyLevelLabelCreationDateDefault->label_id}" id="{$companyLevelLabelCreationDateDefault->label_id}" value='{$creationDateLabel}'/></div>
                    {if $errors.creation_date eq 'true'}							
                        {*ERROR*}					
                        <div class="error_img" style="float: left;"><span class="error_text">This value should not be blank</span></div>
                        {*/ERROR*}						    
                    {/if}
                </td>
            </tr>
            <tr height="10px">
                <td class="border_users_l border_users_b" height="20">
                    {$companyLevelLabelUnitTypeDefault->name4display}
                </td>
                <td class="border_users_l border_users_b border_users_r">
                    <div align="left"><input type='text' name="{$companyLevelLabelUnitTypeDefault->label_id}" id="{$companyLevelLabelUnitTypeDefault->label_id}" value='{$unitTypeLabel}'/></div>
                    {if $errors.unit_type eq 'true'}							
                        {*ERROR*}					
                        <div class="error_img" style="float: left;"><span class="error_text">This value should not be blank</span></div>
                        {*/ERROR*}						    
                    {/if}
                </td>
            </tr>
            <tr height="10px">
                <td class="border_users_l border_users_b" height="20">
                    {$companyLevelLabelPaintShopProductDefault->name4display}
                </td>
                <td class="border_users_l border_users_b border_users_r">
                    <div align="left"><input type='text' name="{$companyLevelLabelPaintShopProductDefault->label_id}" id="{$companyLevelLabelPaintShopProductDefault->label_id}" value='{$paintShopProductLabel}'/></div>
                    {if $errors.paint_shop_product eq 'true'}							
                        {*ERROR*}					
                        <div class="error_img" style="float: left;"><span class="error_text">This value should not be blank</span></div>
                        {*/ERROR*}						    
                    {/if}
                </td>
            </tr>
            <tr height="10px">
                <td class="border_users_l border_users_b" height="20">
                    {$companyLevelLabelBodyShopProductDefault->name4display}
                </td>
                <td class="border_users_l border_users_b border_users_r">
                    <div align="left"><input type='text' name="{$companyLevelLabelBodyShopProductDefault->label_id}" id="{$companyLevelLabelBodyShopProductDefault->label_id}" value='{$bodyShopProductLabel}'/></div>
                    {if $errors.body_shop_product eq 'true'}							
                        {*ERROR*}					
                        <div class="error_img" style="float: left;"><span class="error_text">This value should not be blank</span></div>
                        {*/ERROR*}						    
                    {/if}
                </td>
            </tr>
            <tr height="10px">
                <td class="border_users_l border_users_b" height="20">
                    {$companyLevelLabelDetailingShopProductDefault->name4display}
                </td>
                <td class="border_users_l border_users_b border_users_r">
                    <div align="left"><input type='text' name="{$companyLevelLabelDetailingShopProductDefault->label_id}" id="{$companyLevelLabelDetailingShopProductDefault->label_id}" value='{$detailingShopProductLabel}'/></div>
                    {if $errors.detailing_shop_product eq 'true'}							
                        {*ERROR*}					
                        <div class="error_img" style="float: left;"><span class="error_text">This value should not be blank</span></div>
                        {*/ERROR*}						    
                    {/if}
                </td>
            </tr>
            <tr height="10px">
                <td class="border_users_l border_users_b" height="20">
                    {$companyLevelLabelFuelAndOilProductDefault->name4display}
                </td>
                <td class="border_users_l border_users_b border_users_r">
                    <div align="left"><input type='text' name="{$companyLevelLabelFuelAndOilProductDefault->label_id}" id="{$companyLevelLabelFuelAndOilProductDefault->label_id}" value='{$fuelAndOilProductLabel}'/></div>
                    {if $errors.fuel_and_oils_product eq 'true'}							
                        {*ERROR*}					
                        <div class="error_img" style="float: left;"><span class="error_text">This value should not be blank</span></div>
                        {*/ERROR*}						    
                    {/if}
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
		{/if}	
		<div align="right">
			<br>
			<input type='submit' name='save' class="button" value='Save'/>
			<input type='button' name='cancel' class="button" value='Cancel' 
			{if $request.action=='edit'} onclick='location.href="admin.php?action=viewDetails&category=industryType&id={$request.id}"'{/if}
		{if $request.action=='addItem'} onclick='location.href="admin.php?action=browseCategory&category=tables&bookmark=industryType"'{/if}/>
	<span style="padding-right:50">&nbsp;</span>
</div>
</form>
</div>	
<div id="displayColumnsSettingsMixContainer" title="Mix Display Columns Settings" style="display:none;">Loading ...</div>	
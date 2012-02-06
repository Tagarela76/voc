{*ajax-preloader*}
<div style="height:16px;text-align:center;">
	<div id="preloader" style="display:none">
		<img src='images/ajax-loader.gif'>
	</div>
</div>

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
	{*if $parentCategory == 'facility'}
    <form method='POST' action='?action={$request.action}&category=inventory&id={$request.id}&facilityID={$request.facilityID}&tab={$inventory->getType()}'>    	
    {else $parentCategory == 'department'}
	<form method='POST' action='?action={$request.action}&category=inventory&id={$request.id}&departmentID={$request.departmentID}&tab={$inventory->getType()}'>
	{/if*}
	<form method='POST' action='?action={$request.action}&category=inventory&id={$request.id}&facilityID={$request.facilityID}&tab={$request.tab}'>
        <table class="users" align="center" cellpadding="0" cellspacing="0">
            <tr class="users_u_top_size users_top">
                <td class="users_u_top" width="30%">
                    <span>{if $request.action eq "addItem"}Adding for a new inventory{else}Editing inventory product{/if}</span>
                </td>
                <td class="users_u_top_r">
                </td>
            </tr>
            <tr>
                <td class="border_users_r border_users_l border_users_b" height="20">
                    Product Name :
                </td>
                <td class="border_users_r border_users_b">

					{$product->product_nr}
				
                </td>
            </tr>
            <tr>
                <td class="border_users_r border_users_l border_users_b" height="20">
                    Usage :
                </td>
                <td class="border_users_r border_users_b">

                    
                    {$product->usage}


                </td>
            </tr>
            <tr>
                <td class="border_users_r border_users_l border_users_b" height="20">
                    In stock :
                </td>
                <td class="border_users_r border_users_b">

                    <div align="left">
                        <input type='text' name='in_stock' value='{$product->in_stock}'>
                    </div>
								<div class="floatleft">	
								
									<select name="selectRecycleUnittypeClass" id="selectRecycleUnittypeClass" onchange="getUnittypes(document.getElementById('selectRecycleUnittypeClass'), {$companyID}, {$companyEx})" >									 										
										{section name=j loop=$typeEx}
										{if 'USALiquid' eq $typeEx[j]}<option value='USALiquid' {if 'USALiquid' eq $data->recycle.unittypeClass}selected="selected"{/if}>USA liquid</option>{/if}
										{if 'USADry' eq $typeEx[j]}<option value='USADry' {if 'USADry' eq $data->recycle.unittypeClass}selected="selected"{/if}>USA dry</option>{/if}
										{if 'USAWght' eq $typeEx[j]}<option value='USAWght' {if 'USAWght' eq $data->recycle.unittypeClass}selected="selected"{/if}>USA weight</option>{/if}										
										{if 'MetricVlm' eq $typeEx[j]}<option value='MetricVlm' {if 'MetricVlm' eq $data->recycle.unittypeClass}selected="selected"{/if}>Metric volume</option>{/if}
										{if 'MetricWght' eq $typeEx[j]}<option value='MetricWght' {if 'MetricWght' eq $data->recycle.unittypeClass}selected="selected"{/if}>Metric weight</option>{/if}		
										{/section}
							
									</select>
									<input type="hidden" id="company" value="{$companyID}">
									<input type="hidden" id="companyEx" value="{$companyEx}">
								</div>
								
								
					{if $validStatus.summary eq 'false'}
                    {if $validStatus.inventory_desc eq 'failed'}
                    {*ERORR*}
                    <div class="error_img">
                        <span class="error_text">Error!</span>
                    </div>
                    {*/ERORR*}
                    {/if}
                    {/if}

                </td>
            </tr>
            <tr>
                <td class="border_users_r border_users_l border_users_b" height="20">
                    Limit :
                </td>
                <td class="border_users_r border_users_b">

                    <div align="left">
                        <input type='text' name='limit' value='{$product->limit}'>
                    </div>
					{if $validStatus.summary eq 'false'}
                    {if $validStatus.inventory_desc eq 'failed'}
                    {*ERORR*}
                    <div class="error_img">
                        <span class="error_text">Error!</span>
                    </div>
                    {*/ERORR*}
                    {/if}
                    {/if}

                </td>
            </tr>
            <tr>
                <td class="border_users_r border_users_l border_users_b" height="20">
                    Amount :
                </td>
                <td class="border_users_r border_users_b">

                    <div align="left">
                        <input type='text' name='amount' value='{$product->amount}'>
                    </div>
					{if $validStatus.summary eq 'false'}
                    {if $validStatus.inventory_desc eq 'failed'}
                    {*ERORR*}
                    <div class="error_img">
                        <span class="error_text">Error!</span>
                    </div>
                    {*/ERORR*}
                    {/if}
                    {/if}

                </td>
            </tr>	
            <tr>
                <td class="border_users_r border_users_l border_users_b" height="20">
                    Unit Type :
                </td>
                <td class="border_users_r border_users_b">

                    <div align="left">
                        <input type='text' name='in_stock_unit_type' value='{$product->in_stock_unit_type}'>
                    </div>
					{if $validStatus.summary eq 'false'}
                    {if $validStatus.inventory_desc eq 'failed'}
                    {*ERORR*}
                    <div class="error_img">
                        <span class="error_text">Error!</span>
                    </div>
                    {*/ERORR*}
                    {/if}
                    {/if}

                </td>
            </tr>				
			
            <tr>
                <td class="users_u_bottom">
                </td>
                <td bgcolor="" height="20" class="users_u_bottom_r">
                </td>
            </tr>
        </table>
					
        <div align="right" class="margin7">

				<input type='button' class="button" value='Cancel' onclick="location.href='?action=browseCategory&category=facility&id={$request.facilityID}&bookmark=inventory&tab={$request.tab}'">
			
      	
            <input type='submit' class="button" value='Save'>
			<input type='hidden' name="facilityID" value='{$request.facilityID}'>
			<input type='hidden' name="product_id" value='{$product->product_id}'>
			<input type='hidden' name="inventory_id" value='{$product->id}'>
			<input type='hidden' name="edit" value='editing'>
        </div>									
    </form>
</div>

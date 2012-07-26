{if $color eq "green"}
{include file="tpls:tpls/notify/greenNotify.tpl" text=$message}
{/if}
{if $color eq "orange"}
{include file="tpls:tpls/notify/orangeNotify.tpl" text=$message}
{/if}
{if $color eq "blue"}
{include file="tpls:tpls/notify/blueNotify.tpl" text=$message}
{/if}
<div class="padd7">
    <table class="users" align="center" cellpadding="0" cellspacing="0">
        <tr class="users_top_yellowgreen users_u_top_size">
            <td class="users_u_top_yellowgreen" width="37%" height="30">
                <span>View details</span>
            </td>
            <td class="users_u_top_r_yellowgreen" width="300">
            </td>
        </tr>
        <tr>
            <td class="border_users_l border_users_b" height="20">
                Equipment NR :
            </td>
            <td class="border_users_l border_users_b border_users_r">
                <div align="left">
                    &nbsp;{$equipment.equipment_nr|escape}
                </div>
            </td>
        </tr>
        <tr>
            <td class="border_users_l border_users_b" height="20">
                <b>Description :</b>
            </td>
            <td class="border_users_l border_users_b border_users_r">
                <div align="left">
                    <b>&nbsp;{$equipment.equip_desc|escape}</b>
                </div>
            </td>
        </tr>
        
        {if $show.inventory}
        	{include file="tpls:inventory/design/viewEquipment.tpl}
        {/if}
                
        <tr>
            <td class="border_users_l border_users_b" height="20">
                Permit :
            </td>
            <td class="border_users_l border_users_b border_users_r">
                <div align="left">
                    &nbsp;{$equipment.permit|escape}
                </div>
            </td>
        </tr>
        <tr>
            <td class="border_users_l border_users_b" height="20">
                Expire({$equipment.expire->getFormatInfo()}) :
            </td>
            <td class="border_users_l border_users_b border_users_r">
                <div align="left">
                    &nbsp;{$equipment.expire->formatOutput()}
                </div>
            </td>
        </tr>
        <tr>
            <td class="border_users_l border_users_b" height="20">
                Daily :
            </td>
            <td class="border_users_l border_users_b border_users_r">
                <div align="left">
                    &nbsp;{$equipment.daily|escape}
                </div>
            </td>
        </tr>
        <tr>
            <td class="border_users_l border_users_b" height="20">
                Department track :
            </td>
            <td class="border_users_l border_users_b border_users_r">
                <div align="left">
                    &nbsp;{$equipment.dept_track|escape}
                </div>
            </td>
        </tr>
        <tr>
            <td class="border_users_l border_users_b" height="20">
                Facility track :
            </td>
            <td class="border_users_l border_users_b border_users_r">
                <div align="left">
                    &nbsp;{$equipment.facility_track|escape}
                </div>
            </td>
        </tr>
        <tr>
            <td class="border_users_l border_users_b" height="20">
                Status :
            </td>
            <td class="border_users_l border_users_b border_users_r">
                <div align="left">
                    &nbsp;{$equipment.status|escape}
                </div>
            </td>
        </tr>
		<tr>
            <td class="border_users_l border_users_b" height="20">
                MODEL No. :
            </td>
            <td class="border_users_l border_users_b border_users_r">
                <div align="left">
                    &nbsp;{$equipment.model_number|escape}
                </div>
            </td>
        </tr>
        <tr>
            <td class="border_users_l border_users_b" height="20">
                SERIAL No. :
            </td>
            <td class="border_users_l border_users_b border_users_r">
                <div align="left">
                    &nbsp;{$equipment.serial_number|escape}
                </div>
            </td>
        </tr>
        <tr>
            <td height="20" class="users_u_bottom">
            </td>
            <td colspan="2" height="20" class="users_u_bottom_r">
            </td>
        </tr>
    </table>
</div>
{if $equipmentFiltersList}				
<div id="filterContentDiv" style=" width: 100%;"> <div style="font-size: 16px; font-weight: bold; margin-left:5px;">Filters</div>
	<table class="users" align="center" cellspacing="0" cellpadding="0">
	<thead>
		<tr class="users_top_yellowgreen users_u_top_size">
			<td class="users_u_top_yellowgreen">Name</td>
			<td>Thickness size (inches)</td>
			<td>Width size (inches)</td>
			<td>Length size (inches)</td>
			<td>Filter Type </td>
			<td class="users_u_top_r_yellowgreen">Quantity</td>
		</tr>		
	</thead>

	<tbody id="filterContent" >				
			{section name=i loop=$equipmentFiltersList}		
				<tr class="border_users_l border_users_b">
					<td class="border_users_l">{$equipmentFiltersList[i]->name|escape}</td>
					<td>{$equipmentFiltersList[i]->height_size|escape}</td>
					<td>{$equipmentFiltersList[i]->width_size|escape}</td>
					<td>{$equipmentFiltersList[i]->length_size|escape}</td>
					{assign var="filterType" value=$equipmentFiltersList[i]->getFilterType()}
					<td>{$filterType->name|escape}</td>
					<td class="border_users_r">{$equipmentFiltersList[i]->qty|escape}</td>
				</tr>										
			{/section}																		
	</tbody>

	<tfoot>
		<tr class="">
			<td class="users_u_bottom" height="20"></td><td colspan="6" class="users_u_bottom_r"></td>
		</tr>
	</tfoot>

	</table>
</div> 
{/if}				
{if $equipmentLightingsList}	
<div id="lightingContentDiv" style="width: 100%;"><div style="font-size: 16px; font-weight: bold; margin-left:5px;">Lightings</div>
	<table class="users" align="center" cellspacing="0" cellpadding="0">
		<thead>
                        <tr class="users_top_yellowgreen users_u_top_size">
                                <td class="users_u_top_yellowgreen">Name</td>
				<td>Bulb Type</td>
				<td>Size</td>
				<td>Voltage</td>
				<td>Wattage</td>
				<td>Color</td>
				<td class="users_u_top_r_yellowgreen">Quantity</td>
			</tr>		
		</thead>

		<tbody id="lightingContent" >				
				{section name=i loop=$equipmentLightingsList}
                                        <tr class="border_users_l border_users_b">
						<td class="border_users_l">{$equipmentLightingsList[i]->name|escape}</td>
						{assign var="bulbType" value=$equipmentLightingsList[i]->getBulbType()}
						<td>{$bulbType->name|escape}</td>
						<td>{$equipmentLightingsList[i]->size|escape}</td>
						<td>{$equipmentLightingsList[i]->voltage|escape}</td>
						<td>{$equipmentLightingsList[i]->wattage|escape}</td>
						{assign var="color" value=$equipmentLightingsList[i]->getColor()}
						<td class="border_users_r">{$color->name|escape}</td>
						<td>{$equipmentLightingsList[i]->quantity|escape}</td>
					</tr>										
				{/section}																		
		</tbody>

		<tfoot>
			<tr class="">
				<td class="users_u_bottom" height="20"></td><td colspan="6" class="users_u_bottom_r"></td>
			</tr>
		</tfoot>

	</table>
</div>
{/if}		
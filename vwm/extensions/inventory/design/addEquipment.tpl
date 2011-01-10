<tr>
    <td class="border_users_l border_users_b" height="20">
        Inventory
    </td>
    <td class="border_users_l border_users_b border_users_r">
        <div align="left">
            <select name="selectInventoryID" id="selectInventoryID" onChange="getInventoryShortInfo(this)">
                {if $inventory|count == 0}
				
				<option value='0'>no inventory</option>
				
                {else}
				
                {section name=i loop=$inventory}
				<option value='{$inventory[i]->getID()}' {if $inventory[i]->getID() eq $data.inventory_id}selected="selected"{/if} > {$inventory[i]->getName()}  </option>                
                {/section}
				
                {/if}
            </select>
        </div>
    </td>
</tr>

<tr>
    <td class="border_users_l border_users_b" height="20">
        Inventory description :
    </td>
    <td class="border_users_l border_users_b border_users_r">    	
        {if $inventoryDet}
		
        <div align="left">
            <input type='text' name='inventoryDescription' id='inventoryDescription' readonly value='{$inventoryDet->getDescription()}'>
        </div> 
		
		{else}
		
        <div align="left">
            <input type='text' name='inventoryDescription' id='inventoryDescription' readonly value=''>
        </div> 
		
		{/if}
    </td>
</tr>
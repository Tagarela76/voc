
{*PAGINATION*}
{include file="tpls:tpls/pagination.tpl"}
{*/PAGINATION*}
<div class="padd7">
    <table class="users" align="center" cellpadding="0" cellspacing="0">
        <tr class="users_u_top_size users_top_brown">
            <td class="users_u_top_brown" colspan="5">
                <span>Logbook Equipment List</span>
            </td>
        </tr>
        <tr class="users_top_lightgray">
            <td width="10%">
                <span style='display:inline-block; width:60px;'> 
                    <a onclick="CheckAll(this)" style='color:black'>All</a>
                    /
                    <a style='color:black' onclick="unCheckAll(this)" >None</a>
                </span>
            </td>	
            <td class="border_users_b border_users_r" width="10%">
                ID
            </td>
            <td class="border_users_b border_users_r" width = "10%">
                Facility ID
            </td>
            <td class="border_users_b border_users_r" width = "50%">
                Name
            </td>
            <td class="border_users_b border_users_r" width = "20%">
                Permit Number
            </td>
        </tr>
        {foreach from=$logbookEquipmantList item=logbookEquipmant}
            <tr>
                <td class="border_users_b border_users_r border_users_l">
                    <input type="checkbox" name="checkLogbookEquipmant[]" value="{$logbookEquipmant->getId()}">
                </td>
                <td class="border_users_b border_users_r">
                    <a href="?action=viewItemDetails&category=logbook&facilityId={$facilityId|escape}&id={$logbookEquipmant->getId()|escape}&tab=logbookEquipment">
                        {$logbookEquipmant->getId()|escape}
                    </a>
                </td>
                <td class="border_users_b border_users_r">
                    <a href="?action=viewItemDetails&category=logbook&facilityId={$facilityId|escape}&id={$logbookEquipmant->getId()|escape}&tab=logbookEquipment">
                        {$logbookEquipmant->getFacilityId()|escape}
                    </a>
                </td>
                <td class="border_users_b border_users_r">
                    <a href="?action=viewItemDetails&category=logbook&facilityId={$facilityId|escape}&id={$logbookEquipmant->getId()|escape}&tab=logbookEquipment">
                        {$logbookEquipmant->getEquipDesc()|escape}
                    </a>
                </td>
                <td class="border_users_b border_users_r">
                     <a href="?action=viewItemDetails&category=logbook&facilityId={$facilityId|escape}&id={$logbookEquipmant->getId()|escape}&tab=logbookEquipment">
                         {if $logbookEquipmant->getPermit() == ''}
                             NONE
                         {else}
                             {$logbookEquipmant->getPermit()|escape}
                         {/if}
                     </a>
                 </td>
            </tr>
        {/foreach}
    </table>
    <div align="center"><div class="users_bottom"><div class="users_u_bottom"><div class="users_u_bottom_r"></div></div></div></div>
    <input type='hidden' name='tab' value='{$tab|escape}'>
</div>
{*PAGINATION*}
{include file="tpls:tpls/pagination.tpl"}
{*/PAGINATION*}
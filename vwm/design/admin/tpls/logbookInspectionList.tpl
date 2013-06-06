<table  class="users"  cellspacing="0" cellpadding="0">
    <tr class="users_u_top_size users_top_brown">
        <td class="users_u_top_brown" width="5%">
            <a onclick="CheckAll(this)" style='color:white'>All</a>/<a style='color:white' onclick="unCheckAll(this)" >None</a>
        </td>
        <td width="10%">
            ID
        </td>
        <td width="25%">
            Inspection Type Name
        </td>
        <td width="10%">
            Facility Id
        </td>
        <td width="10%">
            Has Permit
        </td>
        <td  class="users_u_top_r_brown" width="10%">
            Edit
        </td>
    </tr>

    {foreach from=$inspectionTypeList item=inspectionType}
        {assign var="settings" value=$inspectionType->getInspectionType()}
        <tr>
            <td class="border_users_b border_users_r border_users_l" width="5%">
                <input type='checkbox'>
            </td>
            <td class="border_users_b border_users_r" width="10%">
                {$inspectionType->getId()|escape}
            </td>
            <td class="border_users_b border_users_r" width="25%">
                {$settings->typeName|escape}
            </td>
            <td class="border_users_b border_users_r">
                {$inspectionType->getFacilityIds()|escape}
            </td>
            <td class="border_users_b border_users_r">
                {if $settings->permit|escape}
                    yes
                {else}
                    no
                {/if}
            </td>
            <td class="border_users_b border_users_r">
                <a href='?action=addItem&category=logbook&typeId={$inspectionType->getId()|escape}'>edit</a>
            </td>
        </tr>
    {/foreach}
</table>
<div align="center"><div class="users_bottom"><div class="users_u_bottom"><div class="users_u_bottom_r"></div></div></div></div>
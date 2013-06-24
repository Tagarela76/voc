<div class="padd7">
    <table class="users" align="center" cellpadding="0" cellspacing="0">
        <tr class="users_u_top_size users_top_brown">
            <td class="users_u_top_brown" colspan="5">
                <span>Logbook Custom Description</span>
            </td>
        </tr>
        <tr class="users_top_lightgray">
            <td width="5%">
                <span style="display:inline-block; width:60px;"> 
                    <a onclick="CheckAll(this)" style="color:black">All</a>
                    /
                    <a style="color:black" onclick="unCheckAll(this)">None</a>
                </span>
            </td>	
            <td class="border_users_b border_users_r" width="10%">
                ID
            </td>
            <td class="border_users_b border_users_r" width="50%">
                Description
            </td>
            <td class="border_users_b border_users_r" width="10%">
                Notes
            </td>
            <td class="border_users_b border_users_r" width="25%">
                Inspection Type Id
            </td>
        </tr>
        {foreach from=$logbookCustomDescriptionList item=logbookCustomDescription}
            <tr>
                <td class="border_users_b border_users_r border_users_l">
                    <input type="checkbox" name="checkCustomDescription[]" value="{$logbookCustomDescription->getId()|escape}">
                </td>	
                <td class="border_users_b border_users_r" width="10%">
                    <a href="?action=viewLogbookDetails&category=logbook&facilityId={$facilityId|escape}&id={$logbookCustomDescription->getId()|escape}&tab=logbookCustomDescription">
                        {$logbookCustomDescription->getId()|escape}
                    </a>
                </td>
                <td class="border_users_b border_users_r" width="50%">
                    <a href="?action=viewLogbookDetails&category=logbook&facilityId={$facilityId|escape}&id={$logbookCustomDescription->getId()|escape}&tab=logbookCustomDescription">
                        {$logbookCustomDescription->getDescription()|escape}
                    </a>
                </td>
                <td class="border_users_b border_users_r" width="10%">
                    <a href="?action=viewLogbookDetails&category=logbook&facilityId={$facilityId|escape}&id={$logbookCustomDescription->getId()|escape}&tab=logbookCustomDescription">
                    {if $logbookCustomDescription->getNotes()}
                        yes
                    {else}
                        no
                    {/if}
                    </a>
                </td>
                <td class="border_users_b border_users_r" width="25%">
                    <a href="?action=viewLogbookDetails&category=logbook&facilityId={$facilityId|escape}&id={$logbookCustomDescription->getId()|escape}&tab=logbookCustomDescription">
                        {$logbookCustomDescription->getInspectionTypeId()|escape}
                    </a>
                </td>
            </tr>
        {/foreach}
    </table>
    <div align="center"><div class="users_bottom"><div class="users_u_bottom"><div class="users_u_bottom_r"></div></div></div></div>
    <input type='hidden' name='tab' value='{$tab|escape}'>
</div>
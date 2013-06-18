<form action="" method="get">
    <div class="padd7">
        <table class="users" align="center" cellpadding="0" cellspacing="0">
            <tr class="users_u_top_size users_top">
                <td class="users_u_top">
                    <span >Add Logbook Equipment</span>
                </td>
                <td class="users_u_top_r">
                    &nbsp;
                </td>
            </tr>
            <tr class="border_users_b border_users_r" height='30'>
                <td class="border_users_l">
                    Logbook Equipment
                </td>
                <td class="border_users_l">
                    <input type='text' name = 'logbookEquipmentName' value='{$logbookEquipment->getName()|escape}'>
                    {foreach from=$violationList item="violation"}
                        {if $violation->getPropertyPath() eq 'name'}
                            {*ERROR*}
                            <div class="error_img" style="float: left;"><span class="error_text">{$violation->getMessage()}</span></div>
                                {*/ERROR*}
                            {/if}
                        {/foreach}
                </td>
            </tr>
        </table>
        <div align="center" ><div class="users_bottom"><div class="users_u_bottom"><div class="users_u_bottom_r"></div></div></div></div>

        <input type='hidden' name="category" value="logbook">
        <input type='hidden' name="action" value="saveLogbookEquipment">
        <input type='hidden' name="facilityId" value="{$facilityId|escape}">
        <input type='hidden' name="logbookEquipmentId" value="{$logbookEquipment->getId()|escape}">

        <div align="right" style="padding: 12px 12px">
            <input type="submit" value="Save" class="button">
            <input type="button" value="Cancel" class="button" onclick="location.href = '?action=browseCategory&category=facility&id={$facilityId}&bookmark=logbook&tab=logbookEquipment'">
        </div>
    </div>
</form>
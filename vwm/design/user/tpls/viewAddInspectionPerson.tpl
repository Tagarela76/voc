<form action="{$actionUrl}" method="post">
    <div class="padd7">
        <table class="users" align="center" cellpadding="0" cellspacing="0">
            <tr class="users_u_top_size users_top_brown">
                <td class="users_u_top_brown">
                    <span >Add Inspection Person</span>
                </td>
                <td class="users_u_top_r_brown">
                    &nbsp;
                </td>
            </tr>

            <tr class="border_users_b border_users_r" height='30'>
                <td class="border_users_l">
                    Inspection Person
                </td>
                <td class="border_users_l">
                    <input type='text' name = 'personName' value='{$inspectionPerson->getName()|escape}'>
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
        <input type='hidden' name="facilityId" value="{$facilityId|escape}">
        <div align="right" style="padding: 12px 12px">
            <input type="submit" value="Save" class="button">
            <input type="button" value="Cancel" class="button" onclick='history.back()'>
            <input type="button" value="Delete" class="button" onclick="location.href = '?action=deleteItem&category=logbook&facilityID={$facilityId|escape}&checkInspectionPerson%5B%5D={$inspectionPerson->getId()|escape}&tab=inspectionPerson'">
        </div>
    </div>
</form>




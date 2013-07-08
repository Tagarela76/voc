<div style="width: 100%; padding: 10px 13px;">
    <input type='button' class="button" value="<< Back" onclick="location.href = '?action=browseCategory&category=facility&id=125&bookmark=logbook&tab=logbookEquipment'">
    <input type='button' class="button" value="Edit" onclick="location.href = '?action=addItem&category=logbook&facilityID={$logbookEquipment->getFacilityId()|escape}&tab=logbookEquipment&logbookEquipmentId={$logbookEquipment->getId()|escape}'">
</div>
<div>
    <table class="users" align="center" cellpadding="0" cellspacing="0">
        <tr  class="users_u_top_size users_top_brown">
            <td class="users_u_top_brown" width='15%'>
                Logbook Equipment #{$logbookEquipment->getId()|escape}
            </td>
            <td class="users_u_top_r_brown">
            </td>
        </tr>
        <tr>
            <td class="border_users_b border_users_r border_users_l">
                Logbook Equipment ID
            </td>
            <td class="border_users_b border_users_r">
                {$logbookEquipment->getId()|escape}
            </td>
        </tr>
        <tr>
            <td class="border_users_b border_users_r border_users_l">
                Facility Id
            </td>
            <td class="border_users_b border_users_r">
                {$logbookEquipment->getFacilityId()|escape}
            </td>
        </tr>
        <tr>
            <td class="border_users_b border_users_r border_users_l">
                Name
            </td>
            <td class="border_users_b border_users_r">
                {$logbookEquipment->getEquipDesc()|escape}
            </td>
        </tr>
        <tr>
            <td class="border_users_b border_users_r border_users_l">
                Permit Number
            </td>
            <td class="border_users_b border_users_r">
                {if $logbookEquipment->getPermit() == ''}
                    NONE
                {else}
                    {$logbookEquipment->getPermit()|escape}
                {/if}
            </td>
        </tr>
    </table>
    <div align="center"><div class="users_bottom"><div class="users_u_bottom"><div class="users_u_bottom_r"></div></div></div></div>
</div>
            
            
<form method="get" action="" id="controlForm">	
    <div class="button_float_left" style="width: 100%; padding: 10px 13px; ">
        <div  class="button_alpha add_button" style="display: inline-block;">
            <input type="submit" name="action" value="addItem">
        </div>
        <div  class="button_alpha delete_button" style="display: inline-block;">
            <input type="submit" name="action" value="deleteItem">
        </div>
    </div>

    {*Logbook LIST*}
    <div class="padd7">

        <table class="users" align="center" cellpadding="0" cellspacing="0" >
            <tr class="users_u_top_size users_top_brown">
                <td class="users_u_top_brown" width="27%" colspan="6">
                    <span>List of last records at logbook</span>
                </td>
                <td class="users_u_top_r_brown" width="5%">
                </td>
            </tr>
            <tr class="users_top_lightgray">
                <td width="60">
                    <span style='display:inline-block; width:60px;'> 
                        <a onclick="CheckAll(this)" style='color:black'>All</a>
                        /
                        <a style='color:black' onclick="unCheckAll(this)" >None</a>
                    </span>
                </td>
                <td class="border_users_b border_users_r" width="10%">
                    Date
                </td>
                <td class="border_users_b border_users_r" width = "10%">
                    Time
                </td>
                <td class="border_users_b border_users_r" width = "10%">
                    Inspected By
                </td>
                <td class="border_users_b border_users_r" width = "20%">
                    Inspection Type
                </td>
                <td class="border_users_b border_users_r" width = "30%">
                    Condition
                </td>
                <td class="border_users_b border_users_r" width = "20%">
                    Notes
                </td>
            </tr>
            {foreach from=$logbookList item=logbook}
                <tr>
                    <td class="border_users_b border_users_r border_users_l">
                        <input type="checkbox" name="checkLogbook[]" value="{$logbook.logbookId}">
                    </td>
                    <td class="border_users_b border_users_r">
                        <a href="?action=viewLogbookDetails&category=logbook&facilityId={$facilityId}&id={$logbook.logbookId}&tab=logbook">
                            {$logbook.creationDate}
                        </a>
                    </td>
                    <td class="border_users_b border_users_r">
                        <a href="?action=viewLogbookDetails&category=logbook&facilityId={$facilityId}&id={$logbook.logbookId}&tab=logbook">
                            {$logbook.creationTime}
                        </a>
                    </td>
                    <td class="border_users_b border_users_r">
                        <a href="?action=viewLogbookDetails&category=logbook&facilityId={$facilityId}&id={$logbook.logbookId}&tab=logbook">
                            {$logbook.inspectionPersonName}
                        </a>
                    </td>
                    <td class="border_users_b border_users_r">
                        <a href="?action=viewLogbookDetails&category=logbook&facilityId={$facilityId}&id={$logbook.logbookId}&tab=logbook">
                            {$logbook.inspectionType->typeName}
                        </a>
                    </td>
                    <td class="border_users_b border_users_r">
                        <a href="?action=viewLogbookDetails&category=logbook&facilityId={$facilityId}&id={$logbook.logbookId}&tab=logbook">
                            {$logbook.condition}
                        </a>
                    </td>
                    <td class="border_users_b border_users_r">
                        <a href="?action=viewLogbookDetails&category=logbook&facilityId={$facilityId}&id={$logbook.logbookId}&tab=logbook">
                            {$logbook.notes}
                        </a>
                    </td>
                </tr>
            {/foreach}
        </table>
        <div align="center"><div class="users_bottom"><div class="users_u_bottom"><div class="users_u_bottom_r"></div></div></div></div>

        {*PAGINATION*}
        {include file="tpls:tpls/pagination.tpl"}
        {*/PAGINATION*}
    </div>
    <input type="hidden" name="category" value="logbook">
    <input type="hidden" name="facilityID" value="{$facilityId}">
    <input type="hidden" name="tab" value="logbook">
    <input type="hidden" name="equipmentId" value="{$logbookEquipment->getId()|escape}">
    
</form>
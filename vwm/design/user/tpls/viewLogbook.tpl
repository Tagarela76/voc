{if $color eq "green"}
    {include file="tpls:tpls/notify/greenNotify.tpl" text=$message}
{/if}
{if $color eq "orange"}
    {include file="tpls:tpls/notify/orangeNotify.tpl" text=$message}
{/if}
{if $color eq "blue"}
    {include file="tpls:tpls/notify/blueNotify.tpl" text=$message}
{/if}

{*PAGINATION*}
	{include file="tpls:tpls/pagination.tpl"}
{*/PAGINATION*}
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
                {$logbook.creationDate}
            </td>
            <td class="border_users_b border_users_r">
                {$logbook.creationTime}
            </td>
             <td class="border_users_b border_users_r">
                {$logbook.inspectionPersonName}
            </td>
            <td class="border_users_b border_users_r">
                <a href="?action=viewLogbookDetails&category=logbook&facilityID={$facilityId}&id={$logbook.logbookId}&tab={$tab}">
                    {$logbook.inspectionType->typeName}
                </a>
            </td>
           <td class="border_users_b border_users_r">
                {$logbook.condition}
            </td>
            <td class="border_users_b border_users_r">
                {$logbook.notes}
            </td>
        </tr>
        {/foreach}
    </table>
    <div align="center"><div class="users_bottom"><div class="users_u_bottom"><div class="users_u_bottom_r"></div></div></div></div>
    <input type='hidden' name='tab' value='{$tab|escape}'>
{*PAGINATION*}
	{include file="tpls:tpls/pagination.tpl"}
{*/PAGINATION*}

</div>
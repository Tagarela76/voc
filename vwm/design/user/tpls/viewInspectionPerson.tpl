{*PAGINATION*}
	{include file="tpls:tpls/pagination.tpl"}
{*/PAGINATION*}
<div class="padd7">
    <table class="users" align="center" cellpadding="0" cellspacing="0" >
        <tr class="users_u_top_size users_top_brown">
            <td class="users_u_top_brown" width="27%" colspan="4">
                <span>List of Inspections Person</span>
            </td>

        </tr>
        <tr class="users_top_lightgray">
            <td class="border_users_b border_users_r" width="10%">
                <span style='display:inline-block; width:60px;'> 
                    <a onclick="CheckAll(this)" style='color:black'>All</a>
                    /
                    <a style='color:black' onclick="unCheckAll(this)" >None</a>
                </span>
            </td>	
            <td class="border_users_b border_users_r" width="10%">
                ID
            </td>
            <td class="border_users_b border_users_r" width = "40%">
                Name
            </td>
           <!-- <td class="border_users_b border_users_r" width = "10%">
                edit
            </td>-->
        </tr>
        {foreach from=$inspectionPerson item=person}
            <tr>
                <td class="border_users_b border_users_r border_users_l">
                    <input type="checkbox" name="checkInspectionPerson[]" value="{$person->getId()|escape}">
                </td>	
                <td class="border_users_b border_users_r" width="10%">
                    <a href="?action=viewLogbookDetails&category=logbook&facilityId={$facilityId|escape}&id={$person->getId()|escape}&tab=inspectionPerson">
                        {$person->getId()|escape}
                    </a>
                </td>
                <td class="border_users_b border_users_r" width = "40%">
                    <a href="?action=viewLogbookDetails&category=logbook&facilityId={$facilityId|escape}&id={$person->getId()|escape}&tab=inspectionPerson">
                        {$person->getName()|escape}
                    </a>
                </td>
                <!--<td class="border_users_b border_users_r" width = "10%">
                    <a href="?action=addItem&category=logbook&facilityID={$facilityId|escape}&tab=inspectionPerson&inspectionPersonId={$person->getId()|escape}">edit</a>
                </td>-->
            </tr>
        {/foreach}
    </table>
    <div align="center"><div class="users_bottom"><div class="users_u_bottom"><div class="users_u_bottom_r"></div></div></div></div>
    <input type='hidden' name='tab' value='{$tab|escape}'>
</div>
{*PAGINATION*}
	{include file="tpls:tpls/pagination.tpl"}
{*/PAGINATION*}
{if $color eq "green"}
{include file="tpls:tpls/notify/greenNotify.tpl" text=$message}
{/if}
{if $color eq "orange"}
{include file="tpls:tpls/notify/orangeNotify.tpl" text=$message}
{/if}
{if $color eq "blue"}
{include file="tpls:tpls/notify/blueNotify.tpl" text=$message}
{/if}

<form method="POST" action="{if !$action}?action=confirmDelete{else}{$action}{/if}" style="margin:0px">
    {*shadow
    <div class="shadow">
        <div class="shadow_top">
            <div class="shadow_bottom">
                *}
                <table class="users_min" align="center" cellspacing="0" cellpadding="0">
                    {**header**}
                    <tr class="users_header_red">
                        <td width="10%"><div class="users_header_red_l">
                            <div>Select</div></div>
                        </td>
                        <td >
                            <div>ID Number</div>
                        </td>
                        <!-- Special Headers -->
                         {if $itemType eq "mix" or $itemType eq "nox"}
                            <td><div class="users_header_red_r"><div>Description</div></div></td>
						 {elseif $itemType eq "equipment"}
                         	<td><div class="users_header_red_r"><div>Equipment Name</div></div></td>
                         {elseif $itemType eq "inventory"}
                         	<td><div class="users_header_red_r"><div>Inventory Name</div></div></td>
                         {elseif $itemType eq "department"}
                         	<td><div class="users_header_red_r"><div>Department Name</div></div></td>
                         {elseif $itemType eq "facility"}
                         	<td><div>Facility Name</div></td>
                         {elseif $itemType eq "company"}
                         	<td><div>Company Name</div></td>
                         {elseif $itemType eq "accessory"}
                         	<td><div class="users_header_red_r"><div>Accessory Name</div></div></td>
                         {elseif $itemType eq "carbonfootprint"}
                         	<td><div class="users_header_red_r"><div> Direct Carbon Emission Name</div></div></td>
                         {elseif $itemType eq "logbook"}
                         	<td><div class="users_header_red_r"><div> Description</div></div></td>
                         {elseif $itemType eq "wastestorage"}
                         	<td><div class="users_header_red_r"><div> Storage Name</div></div></td>
                         {/if}

                        {if $itemType eq "facility" || $itemType eq "company"}
                        <td><div class="users_header_red_r"><div>
                           	Location/Contact</div></div>
                        </td>
                        {/if}
                    </tr>
                    {**/header**}


                    {if $itemsCount > 0}
                    {section name=i loop=$itemForDelete}
                    <tr class="hov_company border_users_b border_users_r">
                        <td class="border_users_l">
                            <input type="checkbox" checked="checked" value="{$itemForDelete[i].id}" name="item_{$smarty.section.i.index}">
                        </td>
                        <td>
                            <div style="width:100%;">
                                {if $itemForDelete[i].custom_id eq ""}
                                {$itemForDelete[i].id}
                                {else}
                                {$itemForDelete[i].custom_id}
                                {/if}
                            </div>
                        </td>
                        {if $itemType eq "mix" or $itemType eq "nox"}
                        <td>
                            <div style="width:100%;">
                                {$itemForDelete[i].description}
                            </div>
                        </td>
                        {else}
                        <td>
                            <div style="width:100%;">
                                {$itemForDelete[i].name}
                            </div>
                        </td>
                        {*if $itemType != "department"*}
						{if $itemType eq "facility" || $itemType eq "company"}
                        <td width="40%">
                            <div style="width:100%;">
                                {$itemForDelete[i].address},&nbsp;{$itemForDelete[i].contact}&nbsp({$itemForDelete[i].phone})
                            </div>
                        </td>
                        {/if}
                        {/if}
                    </tr>
                    {*mix*}
                    {if $itemForDelete[i].linkedItemCount>0}
                    <tr>
                        <td colspan="4">
                            <table class="users_min" cellspacing="0" cellpadding="0" align="center">
                                <tr class="users_top_lightgray" height="25">
                                    <td class="users_u_top_lightgray">
                                        {$itemForDelete[i].linkedItem}
                                    </td>
                                    <td class="users_u_top_r_lightgray">
                                        &nbsp;
                                    </td>
                                </tr>
                                <tr bgcolor="#e3e3e3">
                                    <td class="border_users_l border_users_b" height="20" width="50%">
                                        {$itemForDelete[i].linkedItem} id
                                    </td>
                                    <td class="border_users_l border_users_r border_users_b">
                                        <div align="left">
                                            Description
                                        </div>
                                    </td>
                                </tr>
                                {section name=j loop=$itemForDelete[i].linkedItemCount}
                                <tr>
                                    <td class="border_users_l border_users_r border_users_b" height="20" width="50%">
                                        {$itemForDelete[i].inUseList[j].id}
                                    </td>
                                    <td class=" border_users_r border_users_b" height="20">
                                        {$itemForDelete[i].inUseList[j].desc}
                                    </td>
                                </tr>
                                {if $itemForDelete[i].inUseList[j].linkedItemCount2>0}
                                <tr>
                                    <td colspan="4">
                                        <table class="users_min" cellspacing="0" cellpadding="0" align="center">
                                            <tr class="users_top_lightgray" height="25">
                                                <td colspan="2" class="users_u_top_lightgray">
                                                    Mixes
                                                </td>
                                                <td class="users_u_top_r_lightgray">
                                                    &nbsp;
                                                </td>
                                            </tr>
                                            <tr bgcolor="#e3e3e3">
                                                <td class="border_users_l border_users_b" height="20" width="50%">
                                                    Mix id
                                                </td>
                                                <td colspan="2" class="border_users_l border_users_r border_users_b">
                                                    <div align="left">
                                                        Description
                                                    </div>
                                                </td>
                                            </tr>
                                            {section name=k loop=$itemForDelete[i].inUseList[j].linkedItemCount2}
                                            <tr>
                                                <td class="border_users_l border_users_r border_users_b" height="20">
                                                    {$itemForDelete[i].inUseList[j].inUseList2[k].id}
                                                </td>
                                                <td colspan="2" class=" border_users_r border_users_b" height="20">
                                                    {$itemForDelete[i].inUseList[j].inUseList2[k].desc}
                                                </td>
                                            </tr>
                                            {/section}
                                        </table>
                                    </td>
                                </tr>
                                {/if}
                                {*/mix*}
                                {/section}
                            </table>
                        </td>
                    </tr>

                    {/if}
                    {/section}

                    {else}
                    {*BEGIN	EMPTY LIST*}
                    {if $itemType == "department"}
                    <tr>
                        <td colspan="4" class="border_users_l border_users_r" align="center">
                            No departments selected.
                        </td>
                    </tr>
                    {elseif $itemType=="facility"}
                    <tr class="border_users_l border_users_r" align="center">
                        <td colspan="4">
                            No facilities selected.
                        </td>
                    </tr>
                    {elseif $itemType=="company"}
                    <tr class="cell">
                        <td colspan="4" class="border_users_l border_users_r" align="center">
                            No companies selected.
                        </td>
                    </tr>
                    {elseif $itemType=="inventory"}
                    <tr class="cell">
                        <td colspan="4" class="border_users_l border_users_r">
                            &nbsp;
                        </td>
                    </tr>
                    {elseif $itemType=="accessory"}
                    <tr class="cell">
                        <td colspan="3" class="border_users_l border_users_r">
                            &nbsp;
                        </td>
                    </tr>
                    {elseif $itemType=="usage"}
                    <tr class="cell">
                        <td colspan="4" class="border_users_l border_users_r">
                            &nbsp;
                        </td>
                    </tr>
                    {elseif $itemType=="carbonfootprint"}
                    <tr class="cell">
                        <td colspan="4" class="border_users_l border_users_r">
                            No Carbon Emissions selected.&nbsp;
                        </td>
                    </tr>
                    {elseif $itemType=="logbook"}
                    <tr class="cell">
                        <td colspan="4" class="border_users_l border_users_r">
                           No Logbook Records selected.&nbsp;
                        </td>
                    </tr>
                    {elseif $itemType=="wastestorage"}
                    <tr class="cell">
                        <td colspan="4" class="border_users_l border_users_r">
                           No storages selected.&nbsp;
                        </td>
                    </tr>
                    {/if}
                    {*END	EMPTY LIST*}
                    {/if}
                    <tr class="users_u_top_size">
                     	{if $itemType eq "facility" || $itemType eq "company"}
                        <td bgcolor="" height="20" class="users_u_bottom " colspan="3">&nbsp;</td>
                        {else}
                        <td bgcolor="" height="20" class="users_u_bottom " colspan="2">&nbsp;</td>
                        {/if}
                        <td bgcolor="" height="20" class="users_u_bottom_r ">
                            &nbsp;
                        </td>
                    </tr>
                </table>
                {*shadow
            </div>
        </div>
    </div>
    *}
    <div align="center" class="padd7">
        <div align="right" style="width:690px ;padding:0 50px" >
            {if $itemsCount > 0 && ($itemType != "wastestorage" || $error != "date")}
				{*<input type="button" value="No" class="button" onclick="location.href='?action=browseCategory&categoryID={$itemType}&{if $itemType eq "facility"}company{elseif $itemType eq "department"}facility{else}department{/if}ID={$itemID}'">*}
				<input type="button" value="No" class="button" onclick="location.href='{$cancelUrl}'">
				<input type="submit" name="confirm" value="Yes" class="button">
            {else}
            <input type="button" value="OK" class="button margintop10 button_big" onclick="location.href='{$cancelUrl}'">
            {*<input type="button" value="OK" class="button margintop10 button_big" onclick="location.href='?action=browseCategory&categoryID={$itemType}&{if $itemType eq "facility"}company{elseif $itemType eq "department"}facility{else}department{/if}ID={$itemID}'">*}
            {/if}
			<input type="hidden" name="departmentID" value="{$departmentID}">
            <input type="hidden" name="itemsCount" value="{$itemsCount}">
			{*<input type="hidden" name="itemID2" value="{$itemID}">*}
			<input type="hidden" name="itemID" value="{$itemType}">
			{if $itemType=="inventory" && $deleteWithProducts==true}
				<input type="hidden" name="deleteWithProducts" value="yes">
			{/if}
			{if $itemType=="carbonfootprint" || $itemType=="logbook"}
				<input type='hidden' name='facilityID' value="{$facilityID}">
			{elseif $itemType =="wastestorage"}
				<input type='hidden' name='error' value="{$error}">
				<input type='hidden' name='facilityID' value="{$facilityID}">
				<input type='hidden' name='info' value="{$info.method}">
				<input type='hidden' name='dateEmpty' value="{$info.date}">
			{/if}

			{if $request.tab}
				<input type='hidden' name='tab' value="{$request.tab}">
			{/if}
        </div>
    </div>
</form>
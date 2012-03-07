<!-- БЫДЛОКОД -->
{if $color eq "green"}
{include file="tpls:tpls/notify/greenNotify.tpl" text=$message}
{/if}
{if $color eq "orange"}
{include file="tpls:tpls/notify/orangeNotify.tpl" text=$message}
{/if}
{if $color eq "blue"}
{include file="tpls:tpls/notify/blueNotify.tpl" text=$message}
{/if}
<form method="POST" action="?action=deleteItem&category=sales">
    {*shadow*
    <div class="shadow">
        <div class="shadow_top">
            <div class="shadow_bottom">
                **}
                <table class="users_min" cellspacing="0" cellpadding="0" align="center">
                    <tr class="users_top_red" height="27">
                        <td class="users_u_top_red" width="100%" colspan="2">
                            {$itemForDelete[0].name}
                        </td>
                        <td class="users_u_top_r_red" width="10px" colspan="2">
                            &nbsp;
                        </td>
                    </tr>
                    <tr height="25px">
                        <td width="35%" class="id_company1 border_users_b border_users_l">
                            ID number
                        </td>
                        <td class="border_users_b  border_users_r" colspan="3">
                            <div style="width:100%;">
                                {if $itemForDelete[0].custom_id eq ""}
                              	  {$itemForDelete[0].id}
                                {else}
                              	  {$itemForDelete[0].custom_id}
                                {/if}
                            </div>
                        </td>
                    </tr>
                    
					{*if ($itemType != "department" || $itemType != "insideDepartment") && 
						$itemType != "mix" && 
						$itemType != "MSDS_Sheet"*}
						
					{if $itemType == 'company' || $itemType == 'facility'} 
                    <tr height="25px">
                        <td class="id_company1 border_users_b border_users_l" width="35%">
                            Location / Contact
                        </td>
                        <td class="border_users_b border_users_r" colspan="3">
                            <div style="width:100%;">
                                {$itemForDelete[0].address},&nbsp;{$itemForDelete[0].contact}&nbsp({$itemForDelete[0].phone})
                            </div>
                        </td>
                    </tr>
                    {/if}
                    {if $itemType eq "mix"} 
                    <tr height="25px">
                        <td class="id_company1 border_users_b border_users_l" width="35%">
                            Description
                        </td>
                        <td class="border_users_b  border_users_r" colspan="3">
                            <div style="width:100%;">
                                {$itemForDelete[0].description}
                            </div>
                        </td>
                    </tr>
                    {/if}
                    <tr>
                        {**mix*************************} 
                        {if $itemForDelete[0].linkedItemCount>0}
                        <td colspan="4" class="border_users_l border_users_r">
                            <br>
                            <table class="users_min" cellspacing="0" cellpadding="0" align="center">
                                <tr class="users_top_lightgray" height="25">
                                    <td colspan="2" class="users_u_top_lightgray">
                                        {$itemForDelete[0].linkedItem}
                                    </td>
                                    <td class="users_u_top_r_lightgray">
                                        &nbsp;
                                    </td>
                                </tr>
                                <tr bgcolor="#e3e3e3">
                                    <td class="border_users_l border_users_b" height="20" width="50%">
                                        {$itemForDelete[0].linkedItem} id
                                    </td>
                                    <td colspan="2" class="border_users_l border_users_r border_users_b">
                                        <div align="left">
                                            Description
                                        </div>
                                    </td>
                                </tr>
                                {section name=i loop=$itemForDelete[0].linkedItemCount}
                                <tr>
                                    <td class="border_users_l  border_users_b" height="20">
                                        {$itemForDelete[0].inUseList[i].id}
                                    </td>
                                    <td colspan="2" class="border_users_l border_users_r border_users_b" height="20">
                                        {$itemForDelete[0].inUseList[i].desc}
                                    </td>
                                </tr>
                                {if $itemForDelete[0].inUseList[i].linkedItemCount2>0}
                                <tr>
                                    <td colspan="3">
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
                                            {section name=j loop=$itemForDelete[0].inUseList[i].linkedItemCount2}
                                            <tr>
                                                <td class="border_users_l border_users_r border_users_b" height="20">
                                                    {$itemForDelete[0].inUseList[i].inUseList2[j].id}
                                                </td>
                                                <td colspan="2" class=" border_users_r border_users_b" height="20">
                                                    {$itemForDelete[0].inUseList[i].inUseList2[j].desc}
                                                </td>
                                            </tr>
                                            {/section}
                                        </table>
                                        <br>
                                    </td>
                                </tr>
                                {/if} 
                                {**/mix****************************}  
                                {/section} 
                            </table>
                        </td>
                        {/if}
                    </tr>
                    <tr>
                        <td bgcolor="" height="20" class="users_u_bottom " colspan="2">
                            &nbsp;
                        </td>
                        <td bgcolor="" height="20" class="users_u_bottom_r " colspan="2">
                            &nbsp; 
                        </td>
                    </tr>
                </table>
                {*shadow*
            </div>
        </div>
    </div>**} 
    <div align="center" class="padd7">
        <div align="right" style="width:690px ;padding:0 50px;">
            {if $itemsCount > 0 && ($itemType != "wastestorage" || $error != "date")}
            	{*if $gobackAction=="viewDetails"}
        	<input type="button" value="No" class="button" style="float:right;" onclick="location.href='?action=viewDetails&itemID={$gobacktoViewCategory}&id={$itemID}'"> 
				{else}
			<input type="button" value="No" class="button" style="float:right;" onclick="location.href='?action=browseCategory&categoryID={$gobackCategory}&{if $gobackCategory eq "facility"}company{elseif $gobackCategory eq "department"}facility{else}department{/if}ID={$itemID}'">
            	{/if*}
			<input type="button" value="No" class="button" onclick="location.href='{$cancelUrl}'">

            <input type="submit" name="confirm" value="Yes" class="button" style="float:right;margin-left:5px;"> 			
			{elseif $error == "date"}
			<input type="button" name="confirm" value="Ok" class="button" style="float:right;margin-left:5px;" onclick="location.href='{$cancelUrl}'"> 
			{else}
			<input type="submit" name="confirm" value="Ok" class="button" style="float:right;margin-left:5px;">
			{/if}

			<input type="hidden" name="itemsCount" value="{$itemsCount}">
			<input type="hidden" name="itemID" value="{$itemType}">
			<input type="hidden" name="item_0" value="{$itemForDelete[0].id}">
 
        </div>
    </div>
</form>
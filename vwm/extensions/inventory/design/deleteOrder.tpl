{if $color eq "green"}
{include file="tpls:tpls/notify/greenNotify.tpl" text=$message}
{/if}
{if $color eq "orange"}
{include file="tpls:tpls/notify/orangeNotify.tpl" text=$message}
{/if}
{if $color eq "blue"}
{include file="tpls:tpls/notify/blueNotify.tpl" text=$message}
{/if}

<form method="GET" action="?action=edit&category={$request.category}&facilityID={$request.facilityID}&tab={$request.tab}" style="margin:0px">
    {*shadow
    <div class="shadow">
        <div class="shadow_top">
            <div class="shadow_bottom">
                непонятно нужно или нет, Аллаааааааааа!*}
                <table class="users_min" align="center" cellspacing="0" cellpadding="0">
                    {**header**}
                    <tr class="users_header_red">
                        <td width="10%"><div class="users_header_red_l">
                            <div>Select</div></div>
                        </td>
                        <td >
                            <div>ID Number</div>
                        </td>

                         <td><div class="users_header_red_r"><div>Order name</div></div></td>
                                           
                    </tr>
                    {**/header**}  
                    
                    
                    {if $itemsCount > 0} 
                    {section name=i loop=$itemForDelete} 
                    <tr class="hov_company border_users_b border_users_r">
                        <td class="border_users_l">
                            <input type="checkbox" checked="checked" value="{$itemForDelete[i].order_id}" name="item_{$smarty.section.i.index}">
                        </td>
                        <td>
                            <div style="width:100%;">

                                {$itemForDelete[i].order_id}
         
                            </div>
                        </td>
                        
                        <td>
                            <div style="width:100%;">
                                {$itemForDelete[i].order_name}
                            </div>
                        </td>
                        
                    {/section} 
                   
                    {else}
                    {*BEGIN	EMPTY LIST*}

                    <tr>
                        <td colspan="3" class="border_users_l border_users_r" align="center">
                            No orders selected.
                        </td>
                    </tr>

                    {*END	EMPTY LIST*} 
                    {/if}
                    <tr class="users_u_top_size">

                        <td bgcolor="" height="20" class="users_u_bottom " colspan="2">&nbsp;</td>
                   
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
				<input type="button" value="No" class="button" onclick="location.href='{$cancelUrl}'">
				<input type="submit" value="Yes" class="button">				

			<input type="hidden" name="facilityID" value="{$request.facilityID}">
            <input type="hidden" name="category" value="{$request.category}">
			<input type="hidden" name="itemsCount" value="{$itemsCount}">
			<input type="hidden" name="tab" value="{$request.tab}">
			<input type="hidden" name="cancel" value="confirm">
			<input type="hidden" name="action" value="edit">
		
        </div>
    </div>
</form>
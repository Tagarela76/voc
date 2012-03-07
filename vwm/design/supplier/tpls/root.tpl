<div class="padd7" align="center">
    {if $color eq "green"}
    	{include file="tpls:tpls/notify/greenNotify.tpl" text=$message}
    {/if}
    {if $color eq "orange"}
    	{include file="tpls:tpls/notify/orangeNotify.tpl" text=$message}
    {/if}
    {if $color eq "blue"}
    	{include file="tpls:tpls/notify/blueNotify.tpl" text=$message}
    {/if}   
    <table class="users" width="100%" cellspacing="0" cellpadding="0" bgcolor="#EFEFEF">
        <tr class="users_u_top_size users_top_blue">
            <td class="users_u_top_blue" width="60">
                {* $smarty.const.HEADER_COMPANIES_SELECT *}<span style='display:inline-block; width:60px;'> <a onclick="CheckAll(this)" style='color:white'>All</a>/<a style='color:white' onclick="unCheckAll(this)" >None</a></span>
            </td>
            <td width="100">
               Jobber ID
            </td>            
            <td>
                Jobber Name
            </td>  
			<td class="users_u_top_r_blue">          
                Location/Contact
            </td>            
        </tr>

	{if $childCategoryItems|@count > 0}                
		{foreach from=$childCategoryItems item=jobber}
		<tr class="hov_company" height="10px">
            <td class="border_users_l border_users_b">
                <input type="checkbox" value="{$jobber->jobber_id}" name="id[]">
            </td>
            <td class="border_users_b border_users_l">
                <a {if $permissions.viewItem}href="{$jobber->url}"{/if}>
                    <div style="width:100%;">
                        {$jobber->jobber_id}
                    </div>
                </a>
            </td>
            <td class="border_users_b border_users_l border_users_r">
                <a {if $permissions.viewItem}href="{$jobber->url}"{/if}>
                    <div style="width:100%;">
                        {$jobber->name}
                    </div>
                </a>
            </td>                 
            <td class="border_users_r border_users_b ">
                <a {if $permissions.viewItem}href="{$jobber->url}"{/if}>
                    <div style="width:100%;">
                        {$jobber->address},&nbsp;{$jobber->contact}&nbsp({$jobber->phone}) 
                    </div>
                </a>
            </td>           
        </tr>
		{/foreach}                 
	{else}
		
        {*BEGIN	EMPTY LIST*}        
        <tr>
            <td colspan="4"class="border_users_l border_users_r" align="center">
                No Jobbers!
            </td>
        </tr>        		
        {*END	EMPTY LIST*}
		
    {/if}
        <tr>
            <td class="users_u_bottom ">
            </td>
            <td colspan="3" bgcolor="" height="30" class="users_u_bottom_r">
            </td>
        </tr>
    </table>
</div>

</form>	{*close FORM tag opened at controlCategoriesList.tpl*}

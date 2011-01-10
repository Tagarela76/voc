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
    {if $color eq "blue2" && $itemsCount == 0}
    {include file="tpls:tpls/notify/blueNotify.tpl" text=$message}
    {/if}	
    <table class="users" width="100%" cellspacing="0" cellpadding="0" bgcolor="#EFEFEF">
        <tr class="users_u_top_size users_top_blue">
            <td class="users_u_top_blue" width="10%">
                Select {$childCategory} 
            </td>
            <td>
                ID Number
            </td>
            {if $categoryType eq "facility"}
            <td>
                Facility name
            </td>
            {elseif $categoryType eq "department"}
            <td class="users_u_top_r_blue">
                Department name
            </td>
            {else}
            <td>
                Company name
            </td>
            {/if}
            {if $categoryType ne "department"}
            <td class="users_u_top_r_blue">
                Location/Contact
            </td>
            {/if}
        </tr>
		
        {if $itemsCount > 0} 
        {*BEGIN LIST*}  
        {section name=i loop=$category} 
        <tr {if $category[i].valid  eq "invalid"}  class="" {else} class="hov_company" {/if}  height="10px">
            <td class="border_users_l border_users_b">
                <input type="checkbox" value="{$category[i].id}" name="item_{$smarty.section.i.index}">
            </td>
            <td class="border_users_b border_users_l">
                <a {if $permissions.viewItem}href="{$category[i].url}"{/if}>
                    <div style="width:100%;">
                        {$category[i].id}
                    </div>
                </a>
            </td>
            <td class="border_users_b border_users_l border_users_r">
                <a {if $permissions.viewItem}href="{$category[i].url}"{/if}>
                    <div style="width:100%;">
                        {$category[i].name}
                    </div>
                </a>
            </td>
            {if $categoryName != "facility"}            
            <td class="border_users_r border_users_b ">
                <a {if $permissions.viewItem}href="{$category[i].url}"{/if}>
                    <div style="width:100%;">
                        {$category[i].address},&nbsp;{$category[i].contact}&nbsp({$category[i].phone}) 
                    </div>
                </a>
            </td>
            {/if}
        </tr>
        {/section} 
        {*END LIST*}
        
        {else}
		
        {*BEGIN	EMPTY LIST*}
        {if $categoryType eq "company"}
        <tr>
            <td colspan="4"class="border_users_l border_users_r" align="center">
                No companies in the list
            </td>
        </tr>
        {elseif $categoryType eq "facility"}
        <tr>
            <td colspan="4"class="border_users_l border_users_r" align="center">
                No facilities in chosen company
            </td>
        </tr>
        {elseif $categoryType eq "department"}
        <tr>
            <td colspan="3" class="border_users_l border_users_r" align="center">
                No departments in chosen facility
            </td>
        </tr>
        {/if}		
        {*END	EMPTY LIST*}
		
        {/if}
        <tr>
            <td class="users_u_bottom ">
            </td>
            <td colspan={if $categoryType eq "department"}"2"{else}"3"{/if} bgcolor="" height="30" class="users_u_bottom_r">
            </td>
        </tr>
    </table>
</div>

</form>	{*close FORM tag opened at controlCategoriesList.tpl*}

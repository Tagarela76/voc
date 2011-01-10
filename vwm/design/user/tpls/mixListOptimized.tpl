<table cellspacing="0" cellpadding="0" width="100%" height="37px">    
    <tr>
        <td bgcolor="white" valign="bottom">
            {if $color eq "green"}
            {include file="tpls/notify/greenNotify.tpl" text=$message}
            {/if}
            {if $color eq "orange"}
            {include file="tpls/notify/orangeNotify.tpl" text=$message}
            {/if}
            {if $color eq "blue"}
            {include file="tpls/notify/blueNotify.tpl" text=$message}
            {/if}
        </td>
    </tr>
</table>
{*PAGINATION*}
{include file="tpls/pagination.tpl"}
{*/PAGINATION*}
<table class="users" height="200" cellspacing="0" cellpadding="0" align="center">
    <tr class="users_top" height="27px">
       <td class="users_u_top" width="5%">
            <span style='display:inline-block; width:60px;'> <a onclick="CheckAll(this)" style='color:white'>All</a>/<a style='color:white' onclick="unCheckAll(this)" >None</a></span>
        </td>
        <td class="" width="20%">
            ID Number            
        </td>
        <td class="">
            Description
        </td>
         <td class="" width="10%">
        	VOC
        </td>
        <td class="users_u_top_r" width="15%">
        	Creation Date
        </td>
    </tr>
{if $childCategoryItems|@count > 0}
    {*BEGIN LIST*} 
    {foreach from=$childCategoryItems item=mix}     
	<!-- Begin Highlighting -->
    <tr {if $mix->valid  eq MixOptimized::MIX_IS_VALID}
 			class="hov_company"
		{else}
			{if $mix->valid  eq MixOptimized::MIX_IS_INVALID}
			 class="us_red"
			{else}
 			class="us_orange"
			{/if}
		{/if}
 	height="10px"> 	
        <!-- End Highlighting -->
		
        <td class="border_users_b border_users_l">
            {if $mix->valid eq MixOptimized::MIX_IS_VALID}
				<span class="ok">&nbsp;</span>
            {else}
            	{if $mix->valid eq MixOptimized::MIX_IS_INVALID}
				<span class="error">&nbsp;</span>
           		{else}
				<span class="warning">&nbsp;</span>
            	{/if}
            {/if}
			<input type="checkbox" value="{$mix->mix_id}" name="id[]">
        </td>
        <td class="border_users_b border_users_r">
            <a href="?action=viewDetails&category=mix&id={$mix->mix_id}&departmentID={$request.id}" class="id_company1" title="{$mix->hoverMessage}">
                <div style="width:100%;">
                    {$mix->mix_id} 
                </div>
            </a>
        </td>
        <td class="border_users_b border_users_r">
            <a href="?action=viewDetails&category=mix&id={$mix->mix_id}&departmentID={$request.id}" class="id_company1" title="{$mix->hoverMessage}">
                <div style="width:100%;" align="left">
                    {$mix->description} 
                </div>
            </a>
        </td>
        <td class="border_users_b border_users_r">
            <a href="?action=viewDetails&category=mix&id={$mix->mix_id}&departmentID={$request.id}" class="id_company1" title="{$mix->hoverMessage}">
                <div style="width:100%;" align="left">
                    {$mix->voc} 
                </div>
            </a>
        </td>
        <td class="border_users_b border_users_r">
            <a href="?action=viewDetails&category=mix&id={$mix->mix_id}&departmentID={$request.id}" class="id_company1" title="{$mix->hoverMessage}">
                <div style="width:100%;" align="left">
                    {$mix->creation_time} 
                </div>
            </a>
        </td>
    </tr>
    {/foreach} 
    <tr>
        <td colspan="5" class="border_users_l border_users_r">
            &nbsp;
        </td>
    </tr>
    {*END LIST*}
{else}
    {*BEGIN	EMPTY LIST*}
    <tr class="">
        <td colspan="5"class="border_users_l border_users_r" align="center">
            No mixes in the department
        </td>
    </tr>
    {*END	EMPTY LIST*}
{/if}
    <tr>
        <td class="users_u_bottom">
        </td>
        <td height="15" class="border_users">
        </td>
        <td class="users_u_bottom_r">
        </td>
    </tr>
</table>
{*PAGINATION*}
{include file="tpls/pagination.tpl"}
{*/PAGINATION*}
</form>	{*close form that was opened at controlInsideDepartment.tpl*}

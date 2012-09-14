{if $message}
<table cellspacing="0" cellpadding="0" width="100%" height="37px">    
    <tr>
        <td bgcolor="white" valign="bottom">
            {if $color eq "green"}
            {include file="tpls:tpls/notify/greenNotify.tpl" text=$message}
            {/if}
            {if $color eq "orange"}
            {include file="tpls:tpls/notify/orangeNotify.tpl" text=$message}
            {/if}
            {if $color eq "blue"}
            {include file="tpls:tpls/notify/blueNotify.tpl" text=$message}
            {/if}            
        </td>
    </tr>
</table>
{/if}

	<form id="editForm" name="addForm" action='{$sendFormAction}' method="post">		
		{foreach from=$childCategoryItems key="depName" item="burnerByDep"}
            {if $burnerByDep}
                <table class="users" align="center" cellpadding="0" cellspacing="0">
                    <tr class="users_header_violet">
                        <td height="30" width="30%">
                            <div class="users_header_violet_l"><div><span ><b>{$depName|escape}</b></span></div></div>
                        </td>
                        <td>
                            <div class="users_header_violet_r"><div>&nbsp;</div></div>				
                        </td>								
                    </tr>
                    {foreach from=$burnerByDep item="burner"}
                        <tr>
                            <td class="border_users_l border_users_b" width="15%" height="20">
                                {$burner.model|escape}
                            </td>
                            <td class="border_users_l border_users_b border_users_r">
                                {if $request.action eq "edit"}
                                    <div align="left" style="float: left;">	<input type='text' name='ratio_{$burner.burner_id|escape}' value='{$burner.ratio|escape}'></div>												
									{foreach from=$violationList key="burnerID" item="violationListByBurner"} 
										{if $burnerID == $burner.burner_id}					
											{*ERROR*}					
											<div class="error_img" style="float: left;"><span class="error_text">{$violationListByBurner}</span></div>
											{*/ERROR*}		
										{/if}										
									{/foreach}
                                {else}
                                    {$burner.ratio|escape}
                                {/if}	
                            </td>
                        </tr>
                    {/foreach}				
                    <tr>
                        <td height="20" class="users_u_bottom">
                        </td>
                        <td height="20" class="users_u_bottom_r">
                        </td>
                    </tr>
                </table>
                <br />
             {/if}
		{/foreach}			
		{*BUTTONS*}	
		<div align="right" class="margin5">
			{if $request.action eq "edit"} 
				<input type='button' name='cancel' class="button" value='Cancel' onClick="location.href='?action=browseCategory&category=facility&id={$request.facilityID}&bookmark=nox&tab={$request.tab}'" />
				<input type='submit' name='save' class="button" value='Save'>
			{/if} 
						
		</div>


		{*HIDDEN*}
		<input type='hidden' name='action' value='{$request.action}'>
		<input type='hidden' name='tab' value='{$request.tab}'>

</form>
</div>
</form>	
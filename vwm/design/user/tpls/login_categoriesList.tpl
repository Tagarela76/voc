<table class="top_block" border="0" width="100%" cellpadding=0 cellspacing=0>
    <tr>
    	
		{*All companies > Company name > Faciality name > Department name*}
		
        <td class="padd7" width="60%" valign="top">            
			{if $request.category eq "root"}
            
				{if !$permissions.root.view}
            <div>
                <h1 class="logininfo">Welcome to VOC-WEB-MANAGER!</h1>
            </div>
				{else}
					<a href="{$urlRoot}" class="id_company_link ">All companies</a>
            	{/if}
				
            {elseif $request.category eq "company"}
			 
            	{if $permissions.root.view} 
					<a href="{$urlRoot}" class="id_company_link ">All companies</a>
            		>
            	{/if}
            	{if !$permissions.company.view}
					<a href="{$urlCompany}" class="id_company_link">{$companyName}</a>
            	{else}
					<span class="id_company_link ">{$companyName}</span>
            	{/if}
            
			{elseif $request.category eq "facility"}
			
            	{if $permissions.root.view}
					<a href="{$urlRoot}" class="id_company_link ">All companies</a>
            		>
            	{/if}
            	{if $permissions.company.view}
					<a href="{$urlCompany}" class="id_company_link">{$companyName}</a>
            		>
            	{else}
					<span class="id_company_link ">{$companyName} > </span>
            	{/if}	
            	{if !$permissions.facility.view}
					<a href="{$urlFacility}" class="id_company_link">{$facilityName}</a>
            	{else}
					<span class="id_company_link ">{$facilityName}</span>
            	{/if}
            
			{elseif  $request.category eq "department"}
			
            	{if $permissions.root.view}
					<a href="{$urlRoot}" class="id_company_link ">All companies</a>
            		>
            	{/if}
            	{if $permissions.company.view}
					<a href="{$urlCompany}" class="id_company_link">{$companyName}</a>
            		>
            	{else}
					<span class="id_company_link ">{$companyName} > </span>
            	{/if} 
            	{if $permissions.facility.view} 
					<a href="{$urlFacility}" class="id_company_link">{$facilityName}</a>
            		>
            	{else}
					<span class="id_company_link ">{$facilityName} > </span>
            	{/if} 
            	{if !$permissions.department.view} 
					<a href="{$urlDepartment}" class="id_company_link">{$departmentName}</a>
            	{else}
					<span class="id_company_link ">{$departmentName}</span>
            	{/if}
            {/if}
			
			{/*All companies > Company name > Faciality name > Department name*}
			           
            <br>
            <br>
			
			{*Contacts*}			                   				
            {if $request.category == "company" || $request.category == "facility"}
				{if $request.action != 'addItem' && $request.action != 'deleteItem'}
						<i>{$address}</i>
            			<br>
            			<i>{$contact}&nbsp;( {$phone} )</i>
            			<br>					
				{/if}	
            {/if}       
			     
			{if $request.category != "root" && $request.action=="browseCategory"}
				{include file="tpls:tpls/controlBrowseCategory.tpl}
			{/if} 
            {/*Contacts*}
			 
        </td>
        <td align="left" class="">           
        </td>
        <td width="35%" class="padd7" valign="top" align="right">
            <table cellpadding=3 cellspacing=0>
                <tr>
                	<td></td>
                    <td>
                        <span class="nameCompany">
                            <p>
                                {$accessname}
                            </p>
                        </span>
                    </td>
                    <td>
                        {if $request.category != 'root' && !($request.action == 'addItem' && $request.category == 'company')}
                        <div align="center">
                            {*<input type="button" class="button" value="Settings" onclick="location.href='?action=settings&itemID={$smarty.session.overCategoryType}&id={$smarty.session.CategoryID}'">*}
							{*<input type="button" class="button" value="Settings" onclick="location.href='?action=settings&category={if $request.action!='addItem'}{$request.category}{else}{$request.parent_category}{/if}&id={$request.id}'">*}
							<input type="button" class="button" value="Settings" onclick="location.href='?action=settings{if $request.category && $request.id && $request.bookmark}&category={$request.category}&id={$request.id}&bookmark={$request.bookmark}'"
									{elseif $request.category && $request.id && !$request.facilityID && !$request.departmentID}&category={$request.category}&id={$request.id}'"
									{elseif $request.category && $request.facilityID}&category=facility&id={$request.facilityID}&bookmark={$request.category}'"
									{elseif $request.category && $request.departmentID}&category=department&id={$request.departmentID}&bookmark={$request.category}'"
									{/if}>
                        </div>
						{/if}
                    </td>
                </tr>
                <tr>
                	<td><a href="?action=showIssueReport&category={if $request.action!='addItem'}{$request.category}&id={$request.id}{else}{$request.parent_category}&id={$request.parent_id}{/if}"><img src="images/question_y.png" title="{$smarty.const.DESCRIPTION_SUGGEST_FEATURE}"/></a></td>
                    <td align="middle">                       
                       <a href="?action=showIssueReport&category={if $request.action!='addItem'}{$request.category}&id={$request.id}{else}{$request.parent_category}&id={$request.parent_id}{/if}" style="color:#506480;font-size:12px;" title="{$smarty.const.DESCRIPTION_SUGGEST_FEATURE}"><b>{$smarty.const.LINK_SUGGEST_FEATURE}</b></a>
                    </td>
                    <td>
                        <div class="" align="center">
                            <input type="button" class="button" value=" Logout " onclick="location.href='?action=logout'">
                        </div>
                    </td>
                </tr>
				{*SEARCH IS FREEZED*}
                {*<tr>
                    <td valign="top">
                        <input type='text' name='' value='' style="float:right;">
                    </td>
                    <td>
                        <div align="center">
                            <input type="button" class="button" value=" Search ">
                        </div>
                    </td>
                </tr>*}
            </table>
        </td>
    </tr>
    <tr>
        <td>
            {*GLOBAL NOTIFICATIONS*}
            {if $globalColor eq "green"}
            {include file="tpls:tpls/notify/greenNotify.tpl" text=$globalMessage}
            {/if}
            {if $globalColor eq "orange"}
            {include file="tpls:tpls/notify/orangeNotify.tpl" text=$globalMessage}
            {/if}
            {if $globalColor eq "blue"}
            {include file="tpls:tpls/notify/blueNotify.tpl" text=$globalMessage}
            {/if}
        </td>
    </tr>
</table>

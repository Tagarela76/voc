{*shadow_table*} 
<table class="" cellspacing="0" cellpadding="0" align="center">
    <tr>
        <td valign="top" class="report_uploader_t_l">
        </td>
        <td valign="top" class="report_uploader_t">
        </td>
        <td valign="top" class="report_uploader_t_r">
        </td>
    </tr>
    <tr>
        <td valign="top" class="report_uploader_l">
        </td>
        <td valign="top" class="report_uploader_c">
            {*shadow_table*}
			
            <table width="100%" cellpadding="5" cellspacing="0">
                <tr>
                    <td>
                        {if $permissions.company.view}{*<a style="color: black" href="?action=msdsUploader&step=main&itemID={$itemID}&id={$id}"><h2>MSDS Uploader</h2></a>*}
						<a style="color: black" href="?action=msdsUploader&step=main&itemID={$request.category}&id={$request.id}"><h2>MSDS Uploader</h2></a>
                        {else}<h2>MSDS Uploader (no permissions)</h2>
                        {/if}
                    </td>
                    <td>
                        Uploads MSDS sheets to VOC WEB MANAGER and assigns them to products.
                    </td>
                </tr>
                <tr>
                    <td>
                        <a style="color: black" href="#" onclick="Popup.showModal('ruleListModal');return false;"><h2>Manage Rule List</h2></a>
                    </td>
                    <td>
                        Add or remove rules at your rule list.
                    </td>
                </tr>
                <tr>
                    <td>
                        <a style="color: black" href="#" onclick="Popup.showModal('emailNotifyModal');return false;"><h2>Manage Email Notifications</h2></a>
                    </td>
                    <td>
                        Add or remove notifications about limits you want to get at your email.
                    </td>
                </tr>
            </table>
			
            {*/shadow_table*} 
        </td>
        <td valign="top" class="report_uploader_r">
        </td>
    </tr>
    <tr>
        <td valign="top" class="report_uploader_b_l">
        </td>
        <td valign="top" class="report_uploader_b">
        </td>
        <td valign="top" class="report_uploader_b_r">
        </td>
    </tr>
</table>
{*/shadow_table*} 
{*SELECT_RULES_POPUP*} 
<div id="ruleListModal" style="text-align:center;height:700px;overflow:auto;display:none;">
    <div style="width:800px">
        <div class="popup_table_t_l">
            <div class="popup_table_t_r">
                <div class="popup_table_t_center">
                </div>
            </div>
        </div>
        <div class="popup_table_center">
            <form>
                <table width="750px" cellpadding="0" cellspacing="0" class="popup_table" align="center">
                    <tr>
                        <td class="control_list" style="border-bottom:0px solid #fff;padding-left:0px">
                            Select: <a onclick="CheckAll(this)" name="allRules" class="id_company1">All</a>
                            /<a onclick="unCheckAll(this)" name="allRules" class="id_company1">None</a>
                        </td>
                        <td colspan="2" style="border-bottom:0px solid #fff">
                            <div style="float:right;padding-right:5px">
                                <a href="#" onClick="Popup.hide('ruleListModal');">X</a>
                            </div>
                            <div>
                                I am customizing this rule list as 
								<span style="padding:0px 3px;"><input type="radio" name="role" value="user" checked></span>user 
								<span style="padding:0px 3px;"><input type="radio" name="role" value="{$categoryName}"></span>{$categoryName}
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3">
                            <h1 class="titleinfo_popup">Choose Rules</h1>
                        </td>
                    </tr>
                    <tr class="table_popup_rule">
                        <td align="center">
                            Select
                        </td>
                        <td style="width:20%">
                            Rule
                        </td>
                        <td>
                            Description
                        </td>
                    </tr>
                    {section name=i loop=$ruleList}
                    <tr name="ruleList" id="row_{$smarty.section.i.index}">
                        <td align="center">
                            <input type="checkbox" name="ruleID" value="{$ruleList[i].rule_id}"
								{section  name=j loop=$customizedRuleList}
									{if $ruleList[i].rule_id  == $customizedRuleList[j].rule_id}
 										checked
									{/if}	
								{/section}
>
                        </td>
                        <td id="ruleName_{$smarty.section.i.index}">
                            {$ruleList[i].rule_nr}&nbsp;
                        </td>
                        <td>
                            {$ruleList[i].description}&nbsp;
                        </td>
                    </tr>
                    {/section} 
                </table>
                <input type="button" class="button" value="Save" onClick="saveCustomizedRuleList();">
				<input type="button" class="button" value="Cancel" onClick="Popup.hide('ruleListModal');">
				<input id="userID" type="hidden" name="userID" value="{$userID}">
				<input id="categoryName" type="hidden" name="categoryName" value="{$categoryName}">
				{if $categoryName == 'department'}
				<input id="categoryNameID" type="hidden" name="categoryNameID" value="{$cfd.departmentID}"> 
				{elseif $categoryName == 'facility'}
				<input id="categoryNameID" type="hidden" name="categoryNameID" value="{$cfd.facilityID}"> 
				{elseif $categoryName == 'company'}
				<input id="categoryNameID" type="hidden" name="categoryNameID" value="{$cfd.companyID}"> 
				{/if}
            </form>
        </div>
        <div class="popup_table_b_l">
            <div class="popup_table_b_r">
                <div class="popup_table_b_center">
                </div>
            </div>
        </div>
    </div>
</div>
{*end*}
{*SELECT_LIMITS_POPUP*} 
<div id="emailNotifyModal" style="text-align:center;height:700px;overflow:auto;display:none;">
    <div style="width:800px">
        <div class="popup_table_t_l">
            <div class="popup_table_t_r">
                <div class="popup_table_t_center">
                </div>
            </div>
        </div>
        <div class="popup_table_center">
            <form>
                <table width="750px" cellpadding="0" cellspacing="0" class="popup_table" align="center">
                    <tr>
                        <td class="control_list" style="border-bottom:0px solid #fff;padding-left:0px">
                            Select: <a onclick="CheckAll(this)" name="allNotifies" class="id_company1">All</a>
                            /<a onclick="unCheckAll(this)" name="allNotifies" class="id_company1">None</a>
                        </td>
                        <td colspan="2" style="border-bottom:0px solid #fff">
                            <div style="float:right;padding-right:5px">
                                <a href="#" onClick="Popup.hide('emailNotifyModal');">X</a>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3">
                            <h1 class="titleinfo_popup">Choose Email Notifications</h1>
                        </td>
                    </tr>
                    <tr class="table_popup_rule">
                        <td align="center" width="10%">
                            Select
                        </td>
                        <td style="width:50%">
                            Notification
                        </td>
                        <td>
                            Description
                        </td>
                    </tr>
                    {section name=i loop=$notificationsList}
                    <tr name="notificationsList" id="row_{$smarty.section.i.index}">
                        <td align="center">
                            <input type="checkbox" name="notifyID" value="{$notificationsList[i].id}"
								{section  name=j loop=$notificationsListSelected}
									{if $notificationsList[i].id  == $notificationsListSelected[j].id}
 										checked
									{/if}	
								{/section}
>
                        </td>
                        <td id="ruleName_{$smarty.section.i.index}">
                            {*$notificationsList[i].name*}{$notificationsList[i].description}&nbsp;
                        </td>
                        <td>
                            {*$notificationsList[i].description*}&nbsp;
                        </td>
                    </tr>
                    {/section} 
                </table>
                <input type="button" class="button" value="Save" onClick="saveEmailNotifications();">
				<input type="button" class="button" value="Cancel" onClick="Popup.hide('emailNotifyModal');">
				<input id="userID" type="hidden" name="userID" value="{$userID}">
				<input id="categoryName" type="hidden" name="categoryName" value="{$categoryName}">
				{if $categoryName == 'department'}
				<input id="categoryNameID" type="hidden" name="categoryNameID" value="{$cfd.departmentID}"> 
				{elseif $categoryName == 'facility'}
				<input id="categoryNameID" type="hidden" name="categoryNameID" value="{$cfd.facilityID}"> 
				{elseif $categoryName == 'company'}
				<input id="categoryNameID" type="hidden" name="categoryNameID" value="{$cfd.companyID}"> 
				{/if}
            </form>
        </div>
        <div class="popup_table_b_l">
            <div class="popup_table_b_r">
                <div class="popup_table_b_center">
                </div>
            </div>
        </div>
    </div>
</div>
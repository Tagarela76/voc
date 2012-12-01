{literal}
<script type="text/javascript">
	$(function() {
		//	global settings object defined at settings.js
		settings.companyId = {/literal} {$cfd.companyID} {literal}; 
		settings.facilityId = {/literal} {$cfd.facilityID} {literal};
		
	});
</script>
{/literal}

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
				{if $permissions.company.view}
				 <tr>
                    <td>
						<a style="color: black" href="#managePermission" onclick="settings.managePermissions.openDialog()">
							<h2>Manage Permissions</h2>
						</a>
                    </td>
                    <td>
                        {php}echo VOCApp::t('general', 'Here you can manage user permissions');{/php}
                    </td>
                </tr>
				{/if}
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
                        <a style="color: black" href="#" onclick="$('#ruleList').dialog('open');"><h2>Manage Rule List</h2></a>
                    </td>
                    <td>
                        Add or remove rules at your rule list.
                    </td>
                </tr>
                <tr>
                    <td>
                        <a style="color: black" href="#" onclick="$('#emailNotify').dialog('open');"><h2>Manage Email Notifications</h2></a>
                    </td>
                    <td>
                        Add or remove notifications about limits you want to get at your email.
                    </td>
                </tr>
				<tr>
                    <td>
						<a style="color: black" href="#manageAdditionalEmailAccounts" onclick="settings.manageAdditionalEmailAccounts.openDialog()">
							<h2>Additional Email Accounts</h2>
						</a>
                    </td>
                    <td>
                        Add or remove additional email accounts.
                    </td>
                </tr>
                <tr>
                    <td>
						<a style="color: black" href="#manageQtyProductGage" onclick="settings.manageQtyProductGage.openDialog()">
							<h2>Quentity Gage</h2>
						</a>
                    </td>
                    <td>
                        Manage Product QTY Gage Settings
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
{*JQUERY POPUP SETTINGS*}
<link href="modules/js/jquery-ui-1.8.2.custom/css/smoothness/jquery-ui-1.8.2.custom.css" rel="stylesheet" type="text/css"/>

<script type="text/javascript" src="modules/js/jquery-ui-1.8.2.custom/development-bundle/external/jquery.bgiframe-2.1.1.js"></script>
<script type="text/javascript" src="modules/js/jquery-ui-1.8.2.custom/development-bundle/ui/jquery.ui.core.js"></script>
<script type="text/javascript" src="modules/js/jquery-ui-1.8.2.custom/development-bundle/ui/jquery.ui.widget.js"></script>
<script type="text/javascript" src="modules/js/jquery-ui-1.8.2.custom/development-bundle/ui/jquery.ui.mouse.js"></script>
<script type="text/javascript" src="modules/js/jquery-ui-1.8.2.custom/development-bundle/ui/jquery.ui.draggable.js"></script>
<script type="text/javascript" src="modules/js/jquery-ui-1.8.2.custom/development-bundle/ui/jquery.ui.position.js"></script>
<script type="text/javascript" src="modules/js/jquery-ui-1.8.2.custom/development-bundle/ui/jquery.ui.resizable.js"></script>
<script type="text/javascript" src="modules/js/jquery-ui-1.8.2.custom/development-bundle/ui/jquery.ui.dialog.js"></script>
{*END OF SETTINGS*}

{*NEW RULES POPUP*}
<div id="ruleList" title="Choose Rules" style='display:none;'>

            <form>
                <table width="750px" cellpadding="0" cellspacing="0" class="popup_table" align="center">
                    <tr>
                        <td class="control_list" style="border-bottom:0px solid #fff;padding-left:0px">
                            Select: <a onclick="CheckAll(this)" name="allRules" class="id_company1">All</a>
                            /<a onclick="unCheckAll(this)" name="allRules" class="id_company1">None</a>
                        </td>
                        <td colspan="2" style="border-bottom:0px solid #fff">
                            <div>
                                I am customizing this rule list as
								<span style="padding:0px 3px;"><input type="radio" name="role" value="user" checked></span>user
								<span style="padding:0px 3px;"><input type="radio" name="role" value="{$categoryName}"></span>{$categoryName}
                            </div>
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
								{/section}>
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
{*END OF NEW RULES POPUP*}

{*SELECT_LIMITS_POPUP*}
<div id="emailNotify" title="Choose Email Notifications" style="display:none;">
            <form>
                <table width="750px" cellpadding="0" cellspacing="0" class="popup_table" align="center">
                    <tr>
                        <td class="control_list" colspan="3" style="border-bottom:0px solid #fff;padding-left:0px">
                            Select: <a onclick="CheckAll(this)" name="allNotifies" class="id_company1">All</a>
                            /<a onclick="unCheckAll(this)" name="allNotifies" class="id_company1">None</a>
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
								{/section}>
                        </td>
                        <td id="ruleName_{$smarty.section.i.index}">
                            {$notificationsList[i].description}&nbsp;
                        </td>
                        <td>
                            &nbsp;
                        </td>
                    </tr>
                    {/section}
                </table>
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
{*END OF POPUP LIMITS*}

<div id="managePermissionsContainer" title="Manage User Permissions" style="display:none;">Loading ...</div>
<div id="manageAdditionalEmailAccountsContainer" title="Manage Additional Email Accounts" style="display:none;">Loading ...</div>
<div id="manageQtyProductGageContainer" title="Manage Quentity Gage Settings" style="display:none;">Loading ...</div>
{literal}
<script type="text/javascript">
	$(function() {
		//	global reminderPage object defined at manageReminders.js
		pfpTypesPage.facilityId = {/literal} {$facilityId} {literal};

	});
</script>
{/literal}

<div id="notifyContainer">
	{if $color eq "green"}
		{include file="tpls:tpls/notify/greenNotify.tpl" text=$message}
	{/if}
	{if $color eq "orange"}
		{include file="tpls:tpls/notify/orangeNotify.tpl" text=$message}
	{/if}
	{if $color eq "blue"}
		{include file="tpls:tpls/notify/blueNotify.tpl" text=$message}
	{/if}
</div>

<div class="padd7">
	<form action='' name="addPfpType" onsubmit="return false;">
		<table class="users addCompany" cellpadding="0" cellspacing="0" align="center">
			<tr class="users_header_orange">
				<td>
					<div class="users_header_orange_l"><div><b>Edit PFP type</b></div></div>
				</td>
				<td>
					<div class="users_header_orange_r"><div>&nbsp;</div></div>
				</td>
			</tr>
			<tr class="border_users_b border_users_r">
				<td height="20" class="border_users_l">
					PFP type name:
				</td>
				<td>
					<div align="left">
						<input id='pfpTypeName' type='text' name='pfpTypeName' value='{$pfpTypeName}' maxlength="150">
					</div>
                    {*ERROR*}
                    <div id="error_pfpType" class="error_img" style="display:none;"><span class="error_text">Error!</span></div>
                    <div id="error_pfpTypeCount" class="error_img" style="display:none;"><span class="error_text">Only 10 types should be been!</span></div>
                    {*/ERROR*}
				</td>
			</tr>
			<tr class="border_users_b border_users_r">
				<td height="20" class="border_users_l">
					Departments:
				</td>
                <td>
					<div align="left">
						<div id="departments2pfpTypes_list">{$pfpDepartmentsName}
							<input type='hidden' name='pfpDepartments_id' id='pfpDepartments_id' value="{$pfpDepartments}" />
						</div>
						<a href="#" onclick="pfpTypesPage.managePfpTypes.openDialog();">edit</a>
                    </div>
					{if $woDepartmentsError eq 'true'}
						{*ERROR*}
						<div class="error_img" style="float: left;"><span class="error_text">This value should not be blank.</span></div>
						{*/ERROR*}
					{/if}
                </td>
			</tr>

			<tr class="border_users_l border_users_r">
				<td colspan="2">&nbsp;</td>
			</tr>

			<tr>
				<td height="20" class="users_u_bottom">&nbsp;</td>
				<td height="20" class="users_u_bottom_r">&nbsp;</td>
			</tr>
		</table>


		<table cellpadding="5" cellspacing="0" align="center" width="95%">
			<tr>
				<td>
		{*BUTTONS*}
		<div align="right">
			<input type='button' name='cancel' class="button" value='Cancel' onClick="location.href='?action=browseCategory&category=facility&id={$facilityId}&bookmark=pfpTypes'">
			<input type='submit' name='save' class="button" value='Save' onClick="savePfpTypesDetails();">
		</div>

		{*HIDDEN*}
		<input type='hidden' name='facility_id' value='{$facilityId}'>
		<input type='hidden' name='pfpId' id='pfpId' value='{$pfpId}'>

		</form>
						</td>
			</tr>
		</table>
</div>

<div id="setDepartmentToPfpContainer" title="Set department to pfp type" style="display:none;">Loading ...</div>
{include file="tpls:tpls/pleaseWait.tpl" text=$pleaseWaitReason}
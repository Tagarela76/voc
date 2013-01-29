{literal}
<script type="text/javascript">
	$(function() {
		//	global settings object defined at settings.js
		categoryUnittype.Id = '{/literal} {$department->getDepartmentId()} {literal}';
		categoryUnittype.category = 'department';
		apMethod.Id = '{/literal}{$department->getDepartmentId()}{literal}';
		apMethod.category = 'department';
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
	<form action='' name="addDepartment" onsubmit="return false;">
		<table class="users addCompany" cellpadding="0" cellspacing="0" align="center">
			<tr class="users_header_orange">
				<td>
					<div class="users_header_orange_l"><div><b>{if $request.action eq "addItem"}Adding for a new department{else}Editing department{/if}</b></div></div>
				</td>
				<td>
					<div class="users_header_orange_r"><div>&nbsp;</div></div>
				</td>	
			</tr>
			
			<tr class="border_users_b border_users_r">		
				<td class="border_users_l" height="20" width="15%">
					Department name:
				</td>
				<td>
					<div align="left">
						<input id='departmentName' type='text' name='name' value='{$department->getName()|escape}' maxlength="64">
					</div>					
			     		{*ERROR*}					
							<div id="error_name" class="error_img" style="display:none;"><span class="error_text">Error!</span></div>					    						
							<div id="error_name_alredyExist" class="error_img" style="display:none;"><span class="error_text">Entered name is alredy in use!</span></div>
						{*/ERROR*}									
													
				</td>					
			</tr>
			
			<tr class="border_users_b border_users_r">			
				<td height="20" class="border_users_l">
					VOC monthly limit:
				</td>
				<td>
					<div align="left">
						<input id='departmentLimit' type='text' name='voc_limit' value='{$department->getVocLimit()|escape}' maxlength="14">
					</div>							
			     				{*ERROR*}					
								<div id="error_voc_limit" class="error_img" style="display:none;"><span class="error_text">Error!</span></div>
							    {*/ERROR*}						    						
				</td>					
			</tr>
			
			<tr class="border_users_b border_users_r">			
				<td height="20" class="border_users_l">
					VOC annual limit:
				</td>
				<td>
					<div align="left">
						<input id='departmentAnnualLimit' type='text' name='voc_annual_limit' value='{$department->getVocAnnualLimit()|escape}' maxlength="14">
					</div>							
			     				{*ERROR*}					
								<div id="error_voc_annual_limit" class="error_img" style="display:none;"><span class="error_text">Error!</span></div>
							    {*/ERROR*}						    						
				</td>					
			</tr>

			<tr>
                <td class="border_users_l border_users_b us_gray" height="23px">
                    Default UnitType:
                </td>
                <td class="border_users_r border_users_l border_users_b">
                        <input type="button" value="Set" onclick="categoryUnittype.manageUnittype.openDialog(); return false;">
                </td>
            </tr>
			
			<!--<tr>
                <td class="border_users_l border_users_b us_gray" height="23px">
                    Default AP Methods:
                </td>
                <td class="border_users_r border_users_l border_users_b">                    
                        <input type="button" value="Set" onclick="apMethod.manageAPMethod.openDialog(); return false;">                    
                </td>
            </tr>-->
			
			<tr class="border_users_b border_users_r">
				<td height="20" class="border_users_l">
					Share {$woLabel}:
				</td>
				<td>
					<div align="left">
						<input id='share_wo' type='checkbox' name='share_wo' {if $department->getShareWo()} checked="checked"{/if}/>
					</div>			     				
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
			<input type='button' name='cancel' class="button" value='Cancel' 
				{if $request.action eq "addItem"} onClick="location.href='?action=browseCategory&category=facility&id={$request.id}&bookmark=department'"
				{elseif $request.action eq "edit"} onClick="location.href='?action=browseCategory&category=department&id={$request.id}&bookmark=mix'"
				{/if}
			>
			<input type='submit' name='save' class="button" value='Save' onClick="saveDepartmentDetails();">						
		</div>
		
		{*HIDDEN*}
		<input type='hidden' name='action' value={$request.action}>		
		{if $request.action eq "addItem"}
			<input type='hidden' name='facility_id' value='{$request.id}'>
		{/if}			
		{if $request.action eq "edit"}
			<input type="hidden" name="id" value="{$request.id}">
		{/if}
		
		</form>
						</td>
			</tr>
		</table>
</div>

{include file="tpls:tpls/pleaseWait.tpl" text=$pleaseWaitReason}	

<input type = 'hidden' id='unittype' name = 'unittype' value="{$unittype}">
<input type = 'hidden' id='apMethods' name = 'apMethods' value="{$defaultAPMethodList}">
{*JQUERY POPUP SETTINGS*}
<link href="modules/js/jquery-ui-1.8.2.custom/css/smoothness/jquery-ui-1.8.2.custom.css" rel="stylesheet" type="text/css"/>

{*END OF SETTINGS*}

<div id="manageUnitTypeContainer" title="Manage Department Unit Type Settings" style="display:none;">Loading ...</div>
<div id="manageApMethodsContainer" title="Manage Department AP Methods Settings" style="display:none;">Loading ...</div>
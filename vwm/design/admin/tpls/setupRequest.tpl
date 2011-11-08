<div class="padd7" align="center">
	<table class="users" width="100%" cellspacing="0" cellpadding="0" bgcolor="#EFEFEF">
		<tr class="users_u_top_size users_top_blue">
			<td class="users_u_top_blue" width="5%">
				<span style="display:inline-block; width:60px;">
					<a style="color:white" onclick="CheckAll(this)">All</a>
					/
					<a onclick="unCheckAll(this)" style="color:white">None</a>
				</span>
			</td>
			<td>Company Name</td>
			<td>Additional Information</td>
			<td>Request Date</td>
			<td>Creater User</td>
			<td class="users_u_top_r_blue">Status</td>
		</tr>
		{if $setupRequest.company|@count gt 0}
			{foreach from=$setupRequest.company item=request key=i}
				<tr class="hov_company">
					<td class="border_users_l border_users_b"><input type="checkbox" name="setupRequestCompanyID[]" value=""/></td>
					<td class="border_users_l border_users_b"><a href="{$request->url}"><div style="width:100%;">{$request->name}</div></a></td>
					<td class="border_users_l border_users_b"><a href="#" onclick="$('#additionalInformationCompany_{$i}').dialog('open'); return false;">View Information</a></td>
					<td class="border_users_l border_users_b"><div style="width:100%;">{$request->date}</div></td>
					<td class="border_users_l border_users_b" width="12%"><div style="width:100%;">{$request->creater_name}</div></td>
					<td class="border_users_l border_users_b border_users_r"><div style="width:100%;"><a href="{$request->url}">{$request->status}</a></div></td>
				</tr>
			{/foreach}
		{else}
			<tr>
				<td colspan="10" align="center" class="border_users_l border_users_r">
					No requests to add new company
				</td>
			</tr>	
		{/if}	
		<tr>
			<td colspan="3" height="15" class="users_u_bottom">
			</td>
			<td colspan="3" height="15" class="users_u_bottom_r">
			</td>
		</tr>
	</table>
	<br/>	
	<table class="users" width="100%" cellspacing="0" cellpadding="0" bgcolor="#EFEFEF">
		<tr class="users_u_top_size users_top_blue">
			<td class="users_u_top_blue" width="5%">
				<span style="display:inline-block; width:60px;">
					<a style="color:white" onclick="CheckAll(this)">All</a>
					/
					<a onclick="unCheckAll(this)" style="color:white">None</a>
				</span>
			</td>
			<td>Facility Name</td>
			<td>Company</td>
			<td>EPA ID Number</td>
			<td>VOC Monthly Limit</td>
			<td>VOC Annual Limit</td>
			<td>Additional Information</td>
			<td>Request Date</td>
			<td>Creater User</td>
			<td class="users_u_top_r_blue">Status</td>
		</tr>
		{if $setupRequest.facility|@count gt 0}
			{foreach from=$setupRequest.facility item=request key=i}
				<tr class="hov_company">
					<td class="border_users_l border_users_b"><input type="checkbox" name="setupRequestFacilityID[]" value=""/></td>
					<td class="border_users_l border_users_b"><a href="{$request->url}"><div style="width:100%;">{$request->name}</div></a></td>
					<td class="border_users_l border_users_b"><a href="{$request->url}"><div style="width:100%;">{$request->parent_name}</div></a></td>
					<td class="border_users_l border_users_b"><a href="{$request->url}"><div style="width:100%;">{$request->epa}</div></a></td>
					<td class="border_users_l border_users_b"><a href="{$request->url}"><div style="width:100%;">{$request->voc_monthly_limit}</div></a></td>
					<td class="border_users_l border_users_b"><a href="{$request->url}"><div style="width:100%;">{$request->voc_annual_limit}</div></a></td>
					<td class="border_users_l border_users_b"><a href="#" onclick="$('#additionalInformationFacility_{$i}').dialog('open'); return false;">View Information</a></td>
					<td class="border_users_l border_users_b"><div style="width:100%;">{$request->date}</div></td>
					<td class="border_users_l border_users_b"><div style="width:100%;">{$request->creater_name}</div></td>
					<td class="border_users_l border_users_b border_users_r"><div style="width:100%;"><a href="{$request->url}">{$request->status}</a></div></td>
				</tr>
			{/foreach}
		{else}
			<tr>
				<td colspan="10" align="center" class="border_users_l border_users_r">
					No requests to add new facility
				</td>
			</tr>	
		{/if}	
		<tr>
			<td colspan="5" height="15" class="users_u_bottom">
			</td>
			<td colspan="5" height="15" class="users_u_bottom_r">
			</td>
		</tr>
	</table>
	<br/>	
	<table class="users" width="100%" cellspacing="0" cellpadding="0" bgcolor="#EFEFEF">
		<tr class="users_u_top_size users_top_blue">
			<td class="users_u_top_blue" width="5%">
				<span style="display:inline-block; width:60px;">
					<a style="color:white" onclick="CheckAll(this)">All</a>
					/
					<a onclick="unCheckAll(this)" style="color:white">None</a>
				</span>
			</td>
			<td>Department Name</td>
			<td>Facility</td>
			<td>Company</td>
			<td>VOC Monthly Limit</td>
			<td>VOC Annual Limit</td>
			<td>Email</td>
			<td>Request Date</td>
			<td>Creater User</td>
			<td class="users_u_top_r_blue">Status</td>
		</tr>
		{if $setupRequest.department|@count gt 0}
			{foreach from=$setupRequest.department item=request}
				<tr class="hov_company">
					<td class="border_users_l border_users_b"><input type="checkbox" name="setupRequestDepartmentID[]" value=""/></td>
					<td class="border_users_l border_users_b"><a href="{$request->url}"><div style="width:100%;">{$request->name}</div></a></td>
					<td class="border_users_l border_users_b"><a href="{$request->url}"><div style="width:100%;">{$request->parent_name}</div></a></td>
					<td class="border_users_l border_users_b"><a href="{$request->url}"><div style="width:100%;">{$request->company_name}</div></a></td>
					<td class="border_users_l border_users_b"><a href="{$request->url}"><div style="width:100%;">{$request->voc_monthly_limit}</div></a></td>
					<td class="border_users_l border_users_b"><a href="{$request->url}"><div style="width:100%;">{$request->voc_annual_limit}</div></a></td>
					<td class="border_users_l border_users_b"><a href="{$request->url}"><div style="width:100%;">{$request->email}</div></a></td>
					<td class="border_users_l border_users_b"><div style="width:100%;">{$request->date}</div></td>
					<td class="border_users_l border_users_b"><div style="width:100%;">{$request->creater_name}</div></td>
					<td class="border_users_l border_users_b border_users_r"><div style="width:100%;"><a href="{$request->url}">{$request->status}</a></div></td>
				</tr>
			{/foreach}
		{else}
			<tr>
				<td colspan="10" align="center" class="border_users_l border_users_r">
					No requests to add new department
				</td>
			</tr>	
		{/if}
		<tr>
			<td colspan="5" height="15" class="users_u_bottom">
			</td>
			<td colspan="5" height="15" class="users_u_bottom_r">
			</td>
		</tr>
	</table>
</div>
		
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
		
{*ADDITIONAL_INFORMATION_FACILITY_POPUP*}
{foreach from=$setupRequest.facility item=request key=k}
<div id="additionalInformationFacility_{$k}" title="View Additional Information" style="background-color:#e3e9f8; padding:25px; font-size:150%; text-align:center;display:none;">		
	<table id="infList" width="200px" cellpadding="0" cellspacing="0" class="popup_table" align="center" >
		<tr><td>Country:</td><td>{$request->country_name}</td></tr>
		<tr><td>State:</td><td> {$request->state}</td></tr>
		<tr><td>City: </td><td>{$request->city}</td></tr>
		<tr><td>Adress: </td><td>{$request->address}</td></tr>
		<tr><td>Zip Code: </td><td>{$request->zip_code}</td></tr>
		<tr><td>County:</td><td> {$request->county}</td></tr>
		<tr><td>Phone:</td><td> {$request->phone}</td></tr>
		<tr><td>Fax: </td><td>{$request->fax}</td></tr>
		<tr><td>Email: </td><td>{$request->email}</td></tr>
		<tr><td>Contact:</td><td> {$request->contact}</td></tr>
		<tr><td>Title: </td><td>{$request->title}</td></tr>
	</table>	
</div>
{/foreach}

{*ADDITIONAL_INFORMATION_COMPANY_POPUP*}
{foreach from=$setupRequest.company item=request key=k}
<div id="additionalInformationCompany_{$k}" title="View Additional Information" style="background-color:#e3e9f8; padding:25px; font-size:150%; text-align:center;display:none;">		
	<table id="infList" width="200px" cellpadding="0" cellspacing="0" class="popup_table" align="center" >
		<tr><td>Country:</td><td>{$request->country_name}</td></tr>
		<tr><td>State:</td><td> {$request->state}</td></tr>
		<tr><td>City: </td><td>{$request->city}</td></tr>
		<tr><td>Adress: </td><td>{$request->address}</td></tr>
		<tr><td>Zip Code: </td><td>{$request->zip_code}</td></tr>
		<tr><td>Phone:</td><td> {$request->phone}</td></tr>
		<tr><td>Fax: </td><td>{$request->fax}</td></tr>
		<tr><td>Email: </td><td>{$request->email}</td></tr>
		<tr><td>Contact:</td><td> {$request->contact}</td></tr>
		<tr><td>Title: </td><td>{$request->title}</td></tr>
	</table>	
</div>
{/foreach}

{literal}
<script>	
	$(function() {
		{/literal}{foreach from=$setupRequest.facility item=request key=s}
			$('#additionalInformationFacility_{$s}').dialog(
		{literal}
				{
					width: 300,
					autoOpen: false,
					resizable: true,
					dragable: true,			
					modal: true
				});
		{/literal}{/foreach}
		
		{foreach from=$setupRequest.company item=request key=k}
			$('#additionalInformationCompany_{$k}').dialog(
		{literal}
				{
					width: 300,
					autoOpen: false,
					resizable: true,
					dragable: true,			
					modal: true
				});
		{/literal}
		{/foreach}
		{literal}
		});
</script>			
{/literal}	

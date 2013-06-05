{foreach from=$facilityList item=item key=key}
	<div style="display: none;" id="company_{$key}">
	{foreach from=$item item=facility}
		<input type="hidden" name="facility_{$facility.id}" value="{$facility.name|escape}"/>
	{/foreach}
	</div>
{/foreach}
<form id="controlCategoriesList" method="get" action="">

<div align="center" class="control_panel_padd">
<div class="control_panel" class="logbg" align="left">
<div class="control_panel_tl">
<div class="control_panel_tr">
<div class="control_panel_bl">
<div class="control_panel_br">
<div class="control_panel_center">

<table  cellpadding="0" cellspacing="0" class="controlCategoriesList" style="height:30px">

	{*  <tr>
		{if $itemsCount > 0}
		<td rowspan=3 class="control_list" style="width:130px">
			<span style='display:inline-block'>
				Select:
				<a onclick="CheckAll(this)" class="id_company1" >All</a>
				 /
				<a onclick="unCheckAll(this)" class="id_company1">None</a>
			</span>
		</td>
		{/if}
	</tr>*}
	<tr>

		<td>
		{if $request.category neq "salesdocs"}
		<div style="float:left; width:80px">

			{if ($request.category == "users" || $request.category == "company")}
				<div class="add_button button_alpha">
					<input type="submit" name="action" value="addItem">
				</div>
			{elseif $request.category=="tables" && $request.bookmark != 'tabs'}
				<div class="add_button button_alpha">
					<input type="submit" name="action" value="addItem"  >
				</div>
			{elseif $request.category=="product"}
				<div class="add_button button_alpha">
					<input type="submit" name="action" value="addItem"  >
				</div>
			{elseif $request.category=="accessory"}
				<div class="add_button button_alpha">
					<input type="submit" name="action" value="addItem"  >
				</div>
			{elseif $request.category=="pfps"}
				<div class="add_button button_alpha">
					<input type="submit" name="action" value="addItem"  >
				</div>
			{elseif $request.category=="salescontacts" && $request.bookmark == "contacts"}
				<div class="add_button button_alpha">
					<input type="submit" name="action" value="addItem"  >
				</div>
            {elseif $request.category=="logbook"}
                <div class="add_button button_alpha">
                    <input type="submit" name="action" value="addItem"  >
                </div>
			{/if}

		</div>
		{else}
			<input type="button" class="button" value="Add" onclick="location.href='admin.php?action=addItem&category=salesdocs&salesDocsCategory={$request.salesDocsCategory}'">
		{/if}

		{if $request.category eq 'salesdocs' and $itemsCount gt 0}
			<input type="button" class="button" name="action" value="Edit" onclick="location.href='admin.php?action=edit&category=salesdocs&salesDocsCategory={$request.salesDocsCategory}'">
		{/if}
		{if $itemsCount > 0 && $request.category neq 'logging'}
			{if $request.category neq 'salesdocs'}
				<div style="float:left; width:80px">
				<div class="delete_button button_alpha">
					<input type="submit" name="action" value="deleteItem" >
				</div>
				</div>
			{else}
				<input type="button" class="button" value="Delete" onclick="location.href='admin.php?action=deleteItem&category=salesdocs&itemsCount={$itemsCount}&salesDocsCategory={$request.salesDocsCategory}'">
			{/if}
		{/if}

		{if $request.category eq "pfps"}
			{if !($request.subBookmark == "custom" ||  $request.subBookmark == "") }
				<input type="button" class="button" value="Give access to company" onclick="location.href='admin.php?action=accessToCompany&category=pfpLibrary&bookmark=pfps&supplier={$request.subBookmark|escape:'url'}'"/>
			{/if}
		{/if}


	{if $request.category eq "product"}

		{*{if $itemsCount > 0}
		<div style="float:left; width:80px">
		<div class="button_alpha group_button">
			<input type="submit" name="action" value="groupProducts">
		</div>
		</div>
		{/if}*}

		</td>

	</tr>
	<tr>

		<td>
		<select name="companyID" onchange="getFacility(value);hideButton();">
			<option value="All companies" {if $currentCompany == 0} selected {/if}>All companies {if $currentCompany == 0}(selected){/if}</option>
			{section name=i loop=$companyList}
				<option value="{$companyList[i].id}" {if $companyList[i].id == $currentCompany} selected {/if}>{$companyList[i].name|escape} {if $companyList[i].id == $currentCompany}(selected){/if}</option>
			{/section}
		</select>
		<select name="facilityID" id="facilityID" disabled="disabled" onchange="hideButton()"></select>

		{if $request.category != 'product'}
		<select name="supplierID">
			<option value="All suppliers" {if $currentSupplier == 0} selected {/if}>All suppliers {if $currentSupplier == 0}(selected){/if}</option>
			{section name=i loop=$supplierList}
				<option value="{$supplierList[i].supplier_id}" {if $supplierList[i].supplier_id == $currentSupplier} selected {/if}>{$supplierList[i].supplier}{if $supplierList[i].supplier_id == $currentSupplier}(selected){/if}</option>
			{/section}
		</select>
		{/if}
		<input type="button" class="button" name="subaction" value="Filter" onclick="submitFunc('browseCategory','Filter')">
		<br>
		
		{if $itemsCount > 0}
			{if $currentCompany == 0}
				<input id='productAssign' type="button" class="button" name="subaction" value="Assign to company" onclick="submitFunc('browseCategory','Assign to company')" >
		{else}
		<input id='productAssign' type="button" class="button" name="subaction" value="Unassign product(s)" onclick="submitFunc('browseCategory','Unassign product(s)')" >{/if}
		{/if}
		&nbsp;&nbsp;&nbsp;&nbsp;
		{if $itemsCount > 0}
			{if $currentFacility == 0 and $currentCompany == 0}
		<input type="button" id="assign2facility" class="button" style="display: none;" name="subaction" value="Assign to facility" onclick="submitFunc('browseCategory','Assign to facility')"/>
			{else}
		<input type="button" id="unassign2facility" class="button" style="display: none;" name="subaction" value="Unassign product(s) from facility" onclick="submitFunc('browseCategory','Unassign product(s) from facility')"/>
			{/if}
		{/if}
	{elseif $request.category eq 'accessory'}
<!-- ACCESSORY -->
		</td>

	</tr>
	<tr>

		<td>
		<select name="jobberID">
			<option value="All jobbers" {if $currentJobber == 0} selected {/if}>All jobbers {if $currentJobber == 0}(selected){/if}</option>
			{section name=i loop=$jobbers}
				<option value="{$jobbers[i]->jobber_id}" {if $jobbers[i]->jobber_id == $currentJobber} selected {/if}>{$jobbers[i]->name} {if $jobbers[i]->jobber_id == $currentJobber}(selected){/if}</option>
			{/section}
		</select>

		<input type="button" class="button" name="subaction" value="Filter" onclick="submitFunc('browseCategory','Filter')">
		<br>
		{if $itemsCount > 0}

		<input type="button" class="button" name="subaction" value="Assign to jobber" onclick="submitFunc('browseCategory','Assign to jobber')">

		<input type="button" class="button" name="subaction" value="Unassign GOM(s)" onclick="submitFunc('browseCategory','Unassign GOM(s)')" >
		{/if}
    {elseif $request.category eq 'pfps'}
    <!--PFPS-->
		</td>

	</tr>
	<tr>

		<td>
		<select name="companyId">
			<option value="All companies" {if $currentCompany == 0} selected {/if}>All companies {if $currentCompany == 0}(selected){/if}</option>
			{section name=i loop=$companyList}
				<option value="{$companyList[i].id}" {if $companyList[i].id == $currentCompany} selected {/if}>{$companyList[i].name|escape} {if $companyList[i].id == $currentCompany}(selected){/if}</option>
			{/section}
		</select>
        <input type="button" class="button" name="subaction" value="Filter" onclick="submitFunc('filter','Filter')">
		<br>
        
		
    {if $itemsCount > 0}
        {if $currentCompany == 0}
				<input id='pfpAssign' type="button" class="button" name="subaction" value="Assign to company" onclick="submitFunc('assignPfpToComapny','Assign to company')" >
        {else}
                <input id='pfpAssign' type="button" class="button" name="subaction" value="Unassign product(s)" onclick="submitFunc('assignPfpToComapny','Unassign product(s)')" >
        {/if}
    {/if}
<!-- ACCESSORY -->
	{elseif $request.category eq 'logging'}
<!-- LOGGING -->
		</td>

	</tr>
	<tr>

		<td>
		<div style="float:left;">
			<select name="user_id" id="userID">
			<option value="All users" {if $currentUser == 0} selected {/if}>All users {if $currentUser == 0}(selected){/if}</option>
			{section name=i loop=$userList}
				<option value="{$userList[i].user_id}" {if $userList[i].user_id == $currentUser} selected {/if}>{$userList[i].accessname} {if $userList[i].user_id == $currentUser}(selected){/if}</option>
			{/section}
		</select><br>
		<input type="button" class="button" name="subaction" value="Filter" onclick="submitFunc('browseCategory','Filter')">
&nbsp;&nbsp;&nbsp;&nbsp;
		</div>

		<div style="float:left;padding-left: 5px;">
		<select name="company_id" id="selectCompany">
			<option value="All companies" {if $currentCompany == 0} selected {/if}>All companies {if $currentCompany == 0}(selected){/if}</option>
			{section name=i loop=$companyList}
				<option value="{$companyList[i].id}" {if $companyList[i].id == $currentCompany} selected {/if}>{$companyList[i].name|escape} {if $companyList[i].id == $currentCompany}(selected){/if}</option>
			{/section}
		</select>
		<select id="selectFacility" name="facility_id" style="display: none;">
			{if isset($facility)}
				{section name=i loop=$facility}
					<option value="{$facility[i].id}" {if $facility[i].id == $reg_field.facility_id} selected='selected' {/if} >{$facility[i].name|escape}</option>
				{/section}
			{/if}

		</select>
		<select id="selectDepartment" name="department_id" style="display: none;">
			{if isset($department)}
				{section name=i loop=$department}
					<option value="{$department[i].id}" {if $department[i].id == $reg_field.department_id} selected='selected' {/if} >{$department[i].name|escape}</option>
				{/section}
			{/if}
		</select>
		<br>
		<input type="button" class="button" name="subaction" value="Filter" onclick="submitFunc('browseCategory','Filter')">
		<br>
</div>
<!-- LOGGING -->
	{/if}

			<div id='hiddens'>
			{if $request.category eq 'tables'}
				<input type="hidden" name="category" value="{$request.bookmark}">
			{elseif $request.category eq 'issue'}
				<input type="hidden" name="category" value="{$request.category}">
			{elseif $request.category == 'salescontacts' and $request.bookmark == 'contacts'}
				<input type="hidden" name="category" value="contacts">
				<input type="hidden" name="bookmark" value="contacts">
				<!--  <input type="hidden" name="category" value="{$request.category}"> -->
				{if $smarty.request.subBookmark}
					<input type="hidden" name="subBookmark" value="{$smarty.request.subBookmark}">
				{/if}
			{elseif $request.category == 'salesdocs'}
				<input type="hidden" name="category" value="salesdocs">
			{elseif $request.category == 'pfps'}
				<input type="hidden" name="category" value="{$request.bookmark}">
				<input type="hidden" name="bookmark" value="{$request.category}">
				<input type="hidden" name="subBookmark" value="{$request.subBookmark}">
				<input type="hidden" name="letterpage" value="{$request.letterpage}">

				<input type="hidden" name="productCategory" value="{$request.productCategory}">
			{elseif $request.category == 'product'}
				<input type="hidden" name="category" value="{$request.category}">
				<input type="hidden" name="subBookmark" value="{$request.subBookmark}">
				<input type="hidden" name="page" value="{$request.page}">
				<input type="hidden" name="letterpage" value="{$request.letterpage}">

				<input type="hidden" name="productCategory" value="{$request.productCategory}">

			{else}
				<input type="hidden" name="category" value="{$request.category}">
				{if $request.bookmark}<input type="hidden" name="bookmark" value="{$request.bookmark}">{/if}
			{/if}
			{if $request.category neq "salesdocs"}
				<input type="hidden" name="itemsCount" value="{$itemsCount}">
			{/if}
			</div>
		</td>
	</tr>
</table>
</div></div></div></div></div></div></div>

<input type="hidden" id="current_facility" value="{$currentFacility}"/>

{literal}
	<script type='text/javascript'>
		$(document).ready(function() {
			$("select[name='companyID']").change();
			$("select[name='facilityID'] option[value='"+$("input#current_facility").val()+"']").attr("selected", "selected");
			$('#assign2facility').hide();
			$('#unassign2facility').hide();
		});

		function submitFunc(action,subaction) {
			$('#hiddens').append('<input type="hidden" name="subaction" value="'+subaction+'">');
			$('#hiddens').append('<input type="hidden" name="action" value="'+action+'">');
			{/literal}
			{if $request.category eq "product"}
			{literal}
			$('#hiddens').append('<input type="hidden" name="sort" value="{$sort}">');
				if (action == 'browseCategory') {
					//$('#hiddens').append('<input type="hidden" name="category" value="product">');
					$('input[name="category"]').val('product');
				}
				{/literal}{/if}
			{literal}
			$('#controlCategoriesList').submit();
		}

		function getFacility(company) {
			var content = "";
			if (company == 'All companies') {
				$("select[name='facilityID']").attr('disabled', 'disabled');
				$("input#assign2facility").css("display","none");
				$("input#unassign2facility").css("display","none");
				$("select[name='facilityID']").find('option').remove();
			} else {
				$("select[name='facilityID']").removeAttr('disabled');
				$("select[name='facilityID']").find('option').remove();
				$("div#company_"+company+" input").each(
					function() {
						content += '<option value="'+$(this).attr('name').split('_').reverse()[0]+'">'+($(this).attr('value'))+'</option>';
					}
				);
				if (content != "") {
					var content_0 = '<option value="All facilities">All facilities</option>';
					content = content_0 + content;
					$("select[name='facilityID']").append(content);
					//$("input#assign2facility").css("display","inline-block");
					//$("input#unassign2facility").css("display","inline-block");
				} else {
					$("select[name='facilityID']").attr('disabled', 'disabled');
					$("input#assign2facility").css("display","none");
					$("input#unassign2facility").css("display","none");
				}
			}
		}
			function hideButton(){
				
				if($("#facilityID :selected").val() == 'All facilities'
						|| $("#facilityID :selected").val() == undefined){
					$('#assign2facility').hide();
					$('#unassign2facility').hide();
					}else{
						$('#assign2facility').show();
					$('#unassign2facility').show();
						}
				}
	</script>
{/literal}
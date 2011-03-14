{if $color eq "green"}
{include file="tpls:tpls/notify/greenNotify.tpl" text=$message}
{/if}
{if $color eq "orange"}
{include file="tpls:tpls/notify/orangeNotify.tpl" text=$message}
{/if}
{if $color eq "blue"}
{include file="tpls:tpls/notify/blueNotify.tpl" text=$message}
{/if}
<div class="padd7">
    <form method='POST' action='{$sendFormAction}'>
        <table class="users addCompany" cellpadding="0" cellspacing="0" align="center">
            <tr class="users_u_top_size users_top">
                <td class="users_u_top" width="30%">
                    <span>{if $currentOperation eq "addItem"}Adding for a new company{else}Editing company{/if}</span>
                </td>
                <td class="users_u_top_r">
                </td>
            </tr>
			
            <tr>
                <td class="border_users_l border_users_b us_gray">
                    Company name:
                </td>
                <td class="border_users_l border_users_b border_users_r">
                    <div class="floatleft">
                        <input type='' name='name' value='{$data.name}'>
                    </div>{if $validStatus.summary eq 'false'}
                    {if $validStatus.name eq 'failed'}
                    {*ERORR*}
                    <div class="error_img">
                        <span class="error_text">Error!</span>
                    </div>
                    {*/ERORR*}
                    {elseif $validStatus.name eq 'alredyExist'}
                    <div class="error_img">
                        <span class="error_text">Entered name is alredy in use!</span>
                    </div>
                    {/if}
                    {/if}
                </td>
            </tr>
			
            <tr>
                <td class="border_users_l border_users_b us_gray">
                    Address:
                </td>
                <td class="border_users_l border_users_b border_users_r">
                    <div class="floatleft">
                        <input type='text' name='address' value='{$data.address}'>
                    </div>{if $validStatus.summary eq 'false'}
                    {if $validStatus.address eq 'failed'}
                    {*ERORR*}
                    <div class="error_img">
                        <span class="error_text">Error!</span>
                    </div>
                    {*/ERORR*}
                    {/if}
                    {/if}
                </td>
            </tr>
			
            <tr>
                <td class="border_users_l border_users_b us_gray">
                    City:
                </td>
                <td class="border_users_l border_users_b border_users_r">
                    <div class="floatleft">
                        <input type='text' name='city' value='{$data.city}'>
                    </div>{if $validStatus.summary eq 'false'}
                    {if $validStatus.city eq 'failed'}
                    {*ERORR*}
                    <div class="error_img">
                        <span class="error_text">Error!</span>
                    </div>
                    {*/ERORR*}
                    {/if}
                    {/if}
                </td>
            </tr>
			
            <tr>
                <td class="border_users_l border_users_b us_gray">
                    County:
                </td>
                <td class="border_users_l border_users_b border_users_r">
                    <div class="floatleft">
                        <input type='text' name='county' value='{$data.county}'>
                    </div>{if $validStatus.summary eq 'false'}
                    {if $validStatus.county eq 'failed'}
                    {*ERORR*}
                    <div class="error_img">
                        <span class="error_text">Error!</span>
                    </div>
                    {*/ERORR*}
                    {/if}
                    {/if}
                </td>
            </tr>		
           		
            <tr>
                <td class="border_users_l border_users_b us_gray">
                    Zip/Postal code:
                </td>
                <td class="border_users_l border_users_b border_users_r">
                    <div class="floatleft" style="padding:0px;">
                        <div style="float:left;">
                            <input type="text" name="zip" value="{$data.zip}">
                        </div>{if $validStatus.summary eq 'false'}
                        {if $validStatus.zip eq 'failed'}
                        {*ERORR*}
                        <div class="error_img">
                            <span class="error_text">Error!</span>
                        </div>
                        {*/ERORR*} 
                        {/if}  
                        {/if} 
                    </div>
                </td>
            </tr>
			
            <tr>
                <td class="border_users_l border_users_b us_gray">
                    Country:
                </td>
                <td class="border_users_l border_users_b border_users_r">
                    <div class="floatleft">
                        <select name="country" id="selectCountry" onchange="getStateList(this)">
                            {section name=i loop=$country}<option value='{$country[i].id}' {if $country[i].id  eq $data.country}selected="selected"{/if}> {$country[i].name}  </option>
                            {/section}
                        </select>
                    </div>
                </td>
            </tr>
			
            <tr>
                <td class="border_users_l border_users_b us_gray">
                    Phone:
                </td>
                <td class="border_users_l border_users_b border_users_r">
                    <div class="floatleft">
                        <input type='text' name='phone' value='{$data.phone}'>
                    </div>{if $validStatus.summary eq 'false'}
                    {if $validStatus.phone eq 'failed'}
                    {*ERORR*}
                    <div class="error_img">
                        <span class="error_text">Error!</span>
                    </div>
                    {*/ERORR*}
                    {/if}
                    {/if}
                </td>
            </tr>
			
            <tr>
                <td class="border_users_l border_users_b us_gray">
                    Fax:
                </td>
                <td class="border_users_l border_users_b border_users_r">
                    <div class="floatleft">
                        <input type='text' name='fax' value='{$data.fax}'>
                    </div>{if $validStatus.summary eq 'false'}
                    {if $validStatus.fax eq 'failed'}
                    {*ERORR*}
                    <div class="error_img">
                        <span class="error_text">Error!</span>
                    </div>
                    {*/ERORR*}
                    {/if}
                    {/if}
                </td>
            </tr>
			
            <tr>
                <td class="border_users_l border_users_b us_gray">
                    Email: 
                </td>
                <td class="border_users_l border_users_b border_users_r">
                    <div class="floatleft">
                        <input type='text' name='email' value='{$data.email}'>
                    </div>{if $validStatus.summary eq 'false'}
                    {if $validStatus.email eq 'failed'}
                    {*ERORR*}
                    <div class="error_img">
                        <span class="error_text">Error!</span>
                    </div>
                    {*/ERORR*}
                    {/if}
                    {/if}
                </td>
            </tr>
            <!--</tr>-->
			
            <tr>
                <td class="border_users_l border_users_b us_gray">
                    Contact:
                </td>
                <td class="border_users_l border_users_b border_users_r">
                    <div class="floatleft">
                        <input type='text' name='contact' value='{$data.contact}'>
                    </div>{if $validStatus.summary eq 'false'}
                    {if $validStatus.contact eq 'failed'}
                    {*ERORR*}
                    <div class="error_img">
                        <span class="error_text">Error!</span>
                    </div>
                    {*/ERORR*}
                    {/if}
                    {/if}
                </td>
            </tr>
			
            <tr>
                <td class="border_users_l us_gray border_users_b">
                    Title:
                </td>
                <td class="border_users_l border_users_b border_users_r">
                    <div class="floatleft">
                        <input type='text' name='title' value='{$data.title}'>
                    </div>{if $validStatus.summary eq 'false'}
                    {if $validStatus.title eq 'failed'}
                    {*ERORR*}
                    <div class="error_img">
                        <span class="error_text">Error!</span>
                    </div>
                    {*/ERORR*}
                    {/if}
                    {/if}
                </td>
            </tr>
			
			<tr>
				<td class="border_users_l border_users_b us_gray" height="23px">
					VOC UnitType:
				</td>
				
				<td class="border_users_r border_users_l border_users_b">
					<select name="selectVocUnitType" id="selectVocUnitType">
					{section name=i loop=$vocUnitTypeList}
						<option value="{$vocUnitTypeList[i].unittype_id}" {if $vocUnitTypeList[i].unittype_id eq $data.voc_unittype_id}selected="selected"{/if}>{$vocUnitTypeList[i].name}</option>
					{/section}
					</select>
				</td>
			</tr>
            
            <tr>
                <td class="border_users_l border_users_b us_gray" height="23px">
                    Default UnitType:
                </td>
                <td class="border_users_r border_users_l border_users_b">
                    <!--<form>-->
                        <input type="button" value="Set" onclick="showUnittype();return false;">
                    <!--</form>-->
                </td>
            </tr>
			<tr>
                <td class="border_users_l border_users_b us_gray" height="23px">
                    Default AP Methods:
                </td>
                <td class="border_users_r border_users_l border_users_b">                    
                        <input type="button" value="Set"onclick="showAPMethods();return false;" >                    
                </td>
            </tr>
			
            <tr>
                <td height="20" class="users_u_bottom">
                </td>
                <td height="20" class="users_u_bottom_r">
                </td>
            </tr>
        </table>
       <div align="right" class="buttonpadd">
           {if $request.action == 'addItem'}<input type='button' name='cancel' class="button button_big" value='Cancel' onclick='location.href="?action=browseCategory&category=root"'>{elseif $request.action == 'edit'}<input type='button' name='cancel' class="button button_big" value='Cancel' onclick='location.href="?action=browseCategory&category={$request.category}&id={$request.id}"'>{/if}<input type='submit' name='save' class="button button_big" value='Save'>
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
		
{*SELECT_APMethods_POPUP*}
	<div id="APMethodsList" title="Select Default AP Methods" style="display:none;">
                    <table width="350px" cellpadding="0" cellspacing="0" class="popup_table" align="center" >
                        <tr>
                            <td class="control_list" colspan="3" style="border-bottom:0px solid #fff;padding-left:0px">
                                  Select: <a onclick="CheckClassOfUnitTypes(document.getElementById('popup_table_APMethod'))" name="allRules" class="id_company1">All</a>
                                /<a onclick="unCheckClassOfUnitTypes(document.getElementById('popup_table_APMethod'))" name="allRules" class="id_company1">None</a>
                            </td>
                        </tr>
                    </table>
                    <table width="350px" cellpadding="0" cellspacing="0" class="popup_table" align="center" id="popup_table_APMethod">
                        <tr class="table_popup_rule">                            
                            <td align="center" style="width:150px">
                                Select
                            </td>                            
                            <td>
                                Description
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" style="padding:0px;border-bottom:0px;"> 
									<table cellpadding=0 cellspacing=0 width="100%">
									{section name=i loop=$APMethodList}
                                   
                                    <tr name="APMethodlist" id="row_{$smarty.section.i.index}">                                        
                                        <td align="center" style="width:150px">
                                           <input type="checkbox" id="APMethodID[]" name="APMethodID[]" value="{$APMethodList[i].apmethod_id}"
												  {section  name=j loop=$defaultAPMethodlist}
													{$defaultAPMethodlist[j]}
													{if $APMethodList[i].apmethod_id  == $defaultAPMethodlist[j]}
													 checked 
													{/if}
												{/section}
											>											
                                        </td>
                                        
                                        <td>
                                            {$APMethodList[i].description}
                                        </td>
                                    </tr>                                    								
                                    {/section}                                                       			
								</table>
                            </td>
                        </tr>
                    </table>
                    <input id="APMethodName" type="hidden" name="APMethodName" value="{$request.category}">
					{if $request.action == 'edit'}
						<input id="editAPMethodName" type="hidden" name="editAPMethodName" value="{$companyID}">
					{/if} 
					<input id="APMethodCount" type="hidden" name="APMethodCount" value="{$smarty.section.i.index}">
	</div>
{*END OF POPUP*}
		
{*SELECT_DEFAULT_UNIT_TYPES_POPUP*}
<div id="UnitTypelist" title="Select Default Unit Types" style="display:none;">
                    <table width="750px" cellpadding="0" cellspacing="0" class="popup_table" align="center" >
                        <tr>
                            <td class="control_list" colspan="3" style="border-bottom:0px solid #fff;padding-left:0px">
                                Select: <a onclick="CheckClassOfUnitTypes(document.getElementById('popup_table_unittype'))" name="allRules" class="id_company1">All</a>
                                /<a onclick="unCheckClassOfUnitTypes(document.getElementById('popup_table_unittype'))" name="allRules" class="id_company1">None</a>
                           </td>
                        </tr>
                    </table>
					<div style="overflow:auto;height:400px;">
                    <table width="750px" cellpadding="0" cellspacing="0" class="popup_table" align="center" id="popup_table_unittype">
                        <tr class="table_popup_rule">
                            <td style="width:100px">
                                Class
                            </td>
                            <td align="center" style="width:150px">
                                Select
                            </td>
                            <td style="width:20%">
                                UnitType
                            </td>
                            <td>
                                Description
                            </td>
                        </tr>
                        <tr>
                            <td colspan="4"  style="padding:0px;border-bottom:0px;">
                                {section name=k loop=$classlist}
                                <table id="class_{$smarty.section.k.index}" name="class_{$smarty.section.k.index}" width="100%" cellpadding=0 cellspacing=0>
                                    <tr>
                                        <td style="width:100px;">
                                            <strong>{$classlist[k].description}</strong>
                                        </td>
                                        <td style="width:150px" align="center">
                                            <a onclick="CheckClassOfUnitTypes(document.getElementById('class_{$smarty.section.k.index}'))">all</a>/<a onclick="unCheckClassOfUnitTypes(document.getElementById('class_{$smarty.section.k.index}'))">none</a>
                                        </td>
                                        <td colspan="2">
                                        </td>
                                    </tr>
                                    {section name=i loop=$unitTypelist}
                                    {if ($classlist[k].id == $unitTypelist[i].unit_class_id)}
                                    <tr name="unitTypelist" id="row_{$smarty.section.i.index}">
                                        <td style="width:100px">
                                        </td>
                                        <td align="center" style="width:150px">
                                            <input type="checkbox" id="unitTypeID[]" name="unitTypeID[]" value="{$unitTypelist[i].unittype_id}"
												{section  name=j loop=$defaultUnitTypelist}
													{$defaultUnitTypelist[j]}
													{if $unitTypelist[i].unittype_id  == $defaultUnitTypelist[j]}
													 checked 
													{/if}
												{/section}
											>											
                                        </td>
                                        <td id="UnitTypeName_{$smarty.section.i.index}" style="width:20%">
                                            {$unitTypelist[i].name}
                                        </td>
                                        <td>
                                            {$unitTypelist[i].unittype_desc}
                                        </td>
                                    </tr>
                                    {/if}									
                                    {/section}									
                                </table>
                                {/section}
								
                            </td>
                        </tr>
                    </table>
                    <input id="categoryName" type="hidden" name="categoryName" value="{$request.category}">{if $request.action == 'edit'}<input id="companyID" type="hidden" name="companyID" value="{$companyID}">{/if} <input id="unitCount" type="hidden" name="unitCount" value="{$smarty.section.i.index}">
				</div>
</div>
{*END OF POPUP*}

{*DATA TO SAVE FROM POPUPS*}
<div style="display:none;">
	<div id="unittype_data">
		{section  name=j loop=$defaultUnitTypelist}
			<input type="checkbox" id="unitTypeID[]" name="unitTypeID[]" value="{$defaultUnitTypelist[j]}" checked >
		{/section}
	</div>
	<div id="apmethod_data">
		{section  name=j loop=$defaultAPMethodlist}
			<input type="checkbox" id="APMethodID[]" name="APMethodID[]" value="{$defaultAPMethodlist[j]}" checked >
		{/section}
	</div>
</div>
{*/END OF DATA*}
    </form>
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
            <tr class="users_header_orange">
                <td width="30%">
                    <div class="users_header_orange_l"><div><span>{if $request.action eq "addItem"}Adding for a new jobber{else}Editing jobber{/if}</span></div></div>
                </td>
                <td>
                	<div class="users_header_orange_r"><div>&nbsp;</div></div>
                </td>
            </tr>
			
            <tr>
                <td class="border_users_l border_users_b us_gray">
                    Jobber name:
                </td>
                <td class="border_users_l border_users_b border_users_r">
                    <div class="floatleft">
                        <input type='' name='name' value='{$data.name}' maxlength="96">
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
                        <input type='text' name='address' value='{$data.address}' maxlength="384">
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
                        <input type='text' name='city' value='{$data.city}' maxlength="192">
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
                        <input type='text' name='county' value='{$data.county}' maxlength="192">
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
                            <input type="text" name="zip" value="{$data.zip}" maxlength="32">
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
                    State:
                </td>
                <td class="border_users_l border_users_r border_users_b">
                    <div class="floatleft" style="padding:0px;">
                        <div style="float:left;">
                            <select name="selectState" id="selectState" {if $selectMode  eq true}  style="display: block" {else}  style="display: none"{/if}>
							{section name=i loop=$state}
                                <option value='{$state[i].id}' {if $state[i].id  eq $data.state}  selected="selected" {/if}> {$state[i].name}  </option>
                            {/section}
                            </select>
                            <input type='text' name='textState' id='textState' value='{if $selectMode ne true}{$data.state}{/if}' {if $selectMode  eq true}  style="display: none" {else}  style="display: block" {/if} maxlength="96">
                        </div>{if $validStatus.summary eq 'false'}
                        {if $validStatus.state eq 'failed'}
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
                    Phone:
                </td>
                <td class="border_users_l border_users_b border_users_r">
                    <div class="floatleft">
                        <input type='text' name='phone' value='{$data.phone}' maxlength="32">
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
                        <input type='text' name='fax' value='{$data.fax}' maxlength="32">
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
                        <input type='text' name='email' value='{$data.email}' maxlength="128">
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
                        <input type='text' name='contact' value='{$data.contact}' maxlength="384">
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
                        <input type='text' name='title' value='{$data.title}' maxlength="192">
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
                    Supplier:
                </td>
                <td class="border_users_r border_users_l border_users_b">

					{if isset($supplier)}
						 <div class="floatleft">
                          <input type="button" value="Choose Supplier" onclick="showSupplier(); return false;">
						 </div>
					{/if}
					{if $validStatus.summary eq 'false'}
                    {if $validStatus.supplier eq 'failed'}
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
                <td height="20" class="users_u_bottom">
                </td>
                <td height="20" class="users_u_bottom_r">
                </td>
            </tr>
        </table>
       <div align="right" class="buttonpadd">
           {if $request.action == 'addItem'}
           		<input type='button' name='cancel' class="button button_big" value='Cancel' onclick='location.href="?action=browseCategory&category=root"'>
			{elseif $request.action == 'edit'}
				<input type='button' name='cancel' class="button button_big" value='Cancel' onclick='location.href="?action=browseCategory&category={$request.category}&bookmark=clients&jobberID={$request.jobberID}&supplierID={$request.supplierID}"'>
			{/if}
				<input type='submit' name='save' class="button button_big" value='Save'>
       </div> 
{if $request.action == 'edit'}<input id="jobberID" type="hidden" name="jobberID" value="{$request.jobberID}">{/if} 
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
		

{*SELECT_SUPPLIER_POPUP*}
<div id="Supplierlist" title="Select Supplier" style="display:none;">
                    <table width="750px" cellpadding="0" cellspacing="0" class="popup_table" align="center" >
                        <tr>
                            <td class="control_list" colspan="3" style="border-bottom:0px solid #fff;padding-left:0px">
                                Select: <a onclick="CheckClassOfUnitTypes(document.getElementById('popup_table_jobber'))" name="allRules" class="id_company1">All</a>
                                /<a onclick="unCheckClassOfUnitTypes(document.getElementById('popup_table_jobber'))" name="allRules" class="id_company1">None</a>
                           </td>
                        </tr>
                    </table>
					<div style="overflow:auto;height:400px;">
                    <table width="750px" cellpadding="0" cellspacing="0" class="popup_table" align="center" id="popup_table_jobber">
                        <tr class="table_popup_rule">

                            <td align="center" style="width:40px">
                                Select
                            </td>
                            <td style="padding: 5px;">
                                Supplier Name
                            </td>

                        </tr>
                        <tr>
                            <td colspan="2"  style="padding:0px;border-bottom:0px;">
							
                              
                                <table id="class_{$smarty.section.k.index}" name="class_{$smarty.section.k.index}" width="100%" cellpadding=0 cellspacing=0>

									
									
                                    {section name=i loop=$supplier }
                                   
                                    <tr name="unitTypelist" id="row_{$smarty.section.i.index}">

                                        <td align="center" style="width:40px">
                                            <input type="checkbox" id="supplier[]" name="supplier[]" value="{$supplier[i].original_id}"
												{section  name=j loop=$jobberSupplier}
													
													{if $supplier[i].original_id  == $jobberSupplier[j].supplier_id}
													 checked 
													{/if}
												{/section}
											>											
                                        </td>
                                        <td id="SupplierName_{$smarty.section.i.index}" style="padding: 5px;">
                                            {$supplier[i].supplier}
                                        </td>

                                    </tr>
                                  							
                                    {/section}									
                                </table>

								
                            </td>
                        </tr>
                    </table>
                    <input id="categoryName" type="hidden" name="categoryName" value="{$request.category}">


    </div>
</div>
{*END OF POPUP*}


{*DATA TO SAVE FROM POPUPS*}
<div style="display:none;">
	<div id="supplier_data">
{literal}		
<script>addSupplierData();</script>
{/literal}	
	</div>

</div>
{*/END OF DATA*}
    </form>
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
                    <div class="users_header_orange_l"><div><span>{if $currentOperation eq "addItem"}Adding for a new company{else}Editing company{/if}</span></div></div>
                </td>
                <td>
                	<div class="users_header_orange_r"><div>&nbsp;</div></div>
                </td>
            </tr>
			
            <tr>
                <td class="border_users_l border_users_b us_gray">
                    Company name:
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
					VOC UnitType:
				</td>
				
				<td class="border_users_r border_users_l border_users_b">
					lb
					<input type="hidden" name="selectVocUnitType" value="2" maxlength="11">
					
				</td>
			</tr>
            
            <tr>
                <td class="border_users_l border_users_b us_gray" height="23px">
                    Default UnitType:
                </td>
                <td class="border_users_r border_users_l border_users_b">
                    <!--<form>-->
                        <input type="button" value="Set" onclick="showUnittype(); return false;">
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
           {if $request.action == 'addItem'}
           		<input type='button' name='cancel' class="button button_big" value='Cancel' onclick='location.href="?action=browseCategory&category=root"'>
			{elseif $request.action == 'edit'}
				<input type='button' name='cancel' class="button button_big" value='Cancel' onclick='location.href="?action=browseCategory&category={$request.category}&id={$request.id}"'>
			{/if}
				<input type='submit' name='save' class="button button_big" value='Save'>
       </div> 
		
		
		 {*SELECT_APMethods_POPUP*}
		<div id="APMethodsList" style="text-align:center;height:700px;overflow:auto;display:none;">
			<div style="width:500px">
				<div class="popup_table_t_l">
                    <div class="popup_table_t_r">
                        <div class="popup_table_t_center">
                        </div>
                    </div>
                </div>
                <div class="popup_table_center">
                    
                    <table width="450px" cellpadding="0" cellspacing="0" class="popup_table" align="center" >
                        <tr>
                            <td class="control_list" style="border-bottom:0px solid #fff;padding-left:0px">
                                  Select: <a onclick="CheckClassOfUnitTypes(document.getElementById('popup_table_APMethod'))" name="allRules" class="id_company1">All</a>
                                /<a onclick="unCheckClassOfUnitTypes(document.getElementById('popup_table_APMethod'))" name="allRules" class="id_company1">None</a>
                            </td>
                           <td colspan="2" style="border-bottom:0px solid #fff">
                                <div style="float:right;padding-right:5px">
                                    <a href="#" onClick="cancelPopupAPMethod();">X</a>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3">
                                <h1 class="titleinfo_popup">Select Default AP Methods</h1>
                            </td>
                        </tr>
                    </table>
                    <table width="450px" cellpadding="0" cellspacing="0" class="popup_table" align="center" id="popup_table_APMethod">
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
                      <input type="button" name="saveAPMethods" class="button" value="Save" onClick="Popup.hide('APMethodsList');">
					  <input type="button" class="button" value="Cancel" onClick="cancelPopupAPMethod()">
                    <input id="APMethodName" type="hidden" name="APMethodName" value="{$request.category}">
					{if $request.action == 'edit'}
						<input id="editAPMethodName" type="hidden" name="editAPMethodName" value="{$companyID}">
					{/if} 
					<input id="APMethodCount" type="hidden" name="APMethodCount" value="{$smarty.section.i.index}">
                </div>
                <div class="popup_table_b_l">
                    <div class="popup_table_b_r">
                        <div class="popup_table_b_center">
                        </div>
                    </div>
                </div>
            </div>
		</div>
		
        {*SELECT_DEFAULT_UNIT_TYPES_POPUP*}
        <div id="UnitTypelist" style="text-align:center;height:700px;display:none;">
            <div style="width:800px">
                <div class="popup_table_t_l">
                    <div class="popup_table_t_r">
                        <div class="popup_table_t_center">
                        </div>
                    </div>
                </div>
                <div class="popup_table_center">
                    <!--<form id="selectUnitTypeForm" name="selectUnitTypeForm" action="modules/ajax/saveDefaultUnitTypelist.php" method="post">-->
                    <table width="750px" cellpadding="0" cellspacing="0" class="popup_table" align="center" >
                        <tr>
                            <td class="control_list" style="border-bottom:0px solid #fff;padding-left:0px">
                                Select: <a onclick="CheckClassOfUnitTypes(document.getElementById('popup_table_unittype'))" name="allRules" class="id_company1">All</a>
                                /<a onclick="unCheckClassOfUnitTypes(document.getElementById('popup_table_unittype'))" name="allRules" class="id_company1">None</a>
                           </td>
                           <td colspan="2" style="border-bottom:0px solid #fff">
                                <div style="float:right;padding-right:5px">
                                    <a href="#" onClick="cancelPopupUnittype()">X</a>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3">
                                <h1 class="titleinfo_popup">Select Default Unit Types</h1>
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
								
								{*density
									<table id="" name="" width="100%">
                                    <tr>
                                        <td style="width:100px;">
                                            <strong>Density</strong>
                                        </td>
                                        <td style="width:150px" align="center">
                                            <a onclick="CheckClassOfUnitTypes(document.getElementById('class_{$smarty.section.k.index}'))">all</a>/<a onclick="unCheckClassOfUnitTypes(document.getElementById('class_{$smarty.section.k.index}'))">none</a>
                                        </td>
                                        <td colspan="2">
                                        </td>
                                    </tr>                                    
                                    <tr name="unitTypelist" id="">
                                        <td style="width:100px">
                                        </td>
                                        <td align="center" style="width:150px">
                                            <input type="checkbox" id="unitTypeID[]" name="unitTypeID[]" value="">
                                        </td>
                                        <td id="" style="width:20%">
                                            g/cm3
                                        </td>
                                        <td>
                                            g/cm3
                                        </td>
                                    </tr>
									<tr name="unitTypelist" id="">
                                        <td style="width:100px">
                                        </td>
                                        <td align="center" style="width:150px">
                                            <input type="checkbox" id="unitTypeID[]" name="unitTypeID[]" value="">
                                        </td>
                                        <td id="" style="width:20%">
                                            lbs/gal
                                        </td>
                                        <td>
                                            lbs/gal
                                        </td>
                                    </tr>                                   						
                                </table>
								*}
								
                            </td>
                        </tr>
                    </table>
					</div>
                    <input type="button" name="saveUnitTypes" class="button" value="Save" onClick="Popup.hide('UnitTypelist');">
					<input type="button" class="button" value="Cancel" onClick="cancelPopupUnittype()">
                    <input id="categoryName" type="hidden" name="categoryName" value="{$request.category}">{if $request.action == 'edit'}<input id="companyID" type="hidden" name="companyID" value="{$companyID}">{/if} <input id="unitCount" type="hidden" name="unitCount" value="{$smarty.section.i.index}">
                </div>
                <div class="popup_table_b_l">
                    <div class="popup_table_b_r">
                        <div class="popup_table_b_center">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

{literal}
<script type='text/javascript'>	
	function showUnittype()
	{	
		$('input[id^=unitTypeID]').each(function(el)
		{
			if($(this).attr('checked')==true)
				$(this).attr('temp','true')
		});
		Popup.showModal('UnitTypelist');
	}
	
	function showAPMethods()
	{
		$('input[id^=APMethodID]').each(function(el)
		{
			if($(this).attr('checked')==true)
				$(this).attr('temp','true')
		});
		Popup.showModal('APMethodsList');
	}
	
	function cancelPopupAPMethod()
	{	
		var num=0;
		$('input[id^=APMethodID]').each(function(el)
		{
			var val=$(this).attr('temp');
			if (val=='true')
				$(this).attr('checked',true);
			else	
				$(this).attr('checked',false);						
			num++;			
		});		
		Popup.hide('APMethodsList');
	}
		
	function cancelPopupUnittype()
	{	
		var num=0;
		$('input[id^=unitTypeID]').each(function(el)
		{
			var val=$(this).attr('temp');			
			if (val=='true')
				$(this).attr('checked',true);	
			else
				$(this).attr('checked',false);						
			num++;			
		});		
		Popup.hide('UnitTypelist');
	}		
</script>	
{/literal}


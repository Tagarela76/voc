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
<div class="padd7">
	<form name="addFacility">

		<table class="users addCompany" cellpadding="0" cellspacing="0" align="center">
			
			<tr class="users_u_top_size users_top">
				<td class="users_u_top">
					<span >{if $request.action eq "addItem"}Adding for a new facility{else}Editing facility{/if}</span>
				</td>
				<td class="users_u_top_r" width="300">
				</td>				
			</tr>

			<tr>
				<td class="border_users_l border_users_b us_gray" width="15%">
						EPA ID number:
				</td>
				<td class="border_users_l border_users_b border_users_r">
					<div class="floatleft" >
						<input type='text' name='epa' value='{$data.epa}'>
					</div>
						{*ERROR*}					
                        <div id="error_epa" class="error_img" style="display:none;"><span class="error_text">Error!</span></div>	{*ASK_ALLA!*}
                        {*/ERROR*}                    					
				</td>
			</tr>
			
			<tr>			
				<td class="border_users_l border_users_b us_gray" height="20">
					VOC monthly limit:
				</td>
				<td class="border_users_l border_users_b border_users_r">
					<div class="floatleft">	<input type='text' name='voc_limit' value='{$data.voc_limit}'></div>														
			     				{*ERROR*}					
								  <div id="error_voc_limit" class="error_img" style="display:none;"><span class="error_text">Error!</span></div>
							    {*/ERROR*}								
				</td>
			</tr>
			
			<tr>			
				<td class="border_users_l border_users_b us_gray" height="20">
					VOC annual limit:
				</td>
				<td class="border_users_l border_users_b border_users_r">
					<div class="floatleft">	<input type='text' name='voc_annual_limit' value='{$data.voc_annual_limit}'></div>														
			     				{*ERROR*}					
								  <div id="error_voc_annual_limit" class="error_img" style="display:none;"><span class="error_text">Error!</span></div>
							    {*/ERROR*}								
				</td>
			</tr>
			<tr>			
				<td class="border_users_l border_users_b us_gray" height="20">
					NOX monthly limit:
				</td>
				<td class="border_users_l border_users_b border_users_r">
					<div class="floatleft">	<input type='text' name='monthly_nox_limit' {if $data.monthly_nox_limit != ''} value='{$data.monthly_nox_limit}' {else} value='30' {/if}></div>														
			     				{*ERROR*}					
								  <div id="error_monthly_nox_limit" class="error_img" style="display:none;"><span class="error_text">Error!</span></div>
							    {*/ERROR*}								
				</td>
			</tr>			
			<tr>
	            <td class="border_users_l border_users_b us_gray" height="20">
					Facility name:
				</td>
				<td class="border_users_l border_users_b border_users_r">
					<div align="left" class="floatleft"><input type='text' name='name' value='{$data.name}'></div>
								{*ERROR*}		
								  <div id="error_name" class="error_img" style="display:none;"><span class="error_text">Error!</span></div>							
								  <div id="error_name_alredyExist" class="error_img" style="display:none;"><span class="error_text">Such facility is already exist!</span></div>
								{*ERROR*}								
							</td>
			</tr>
						
			<tr>
				<td class="border_users_l border_users_b us_gray" height="20">
					Address:
				</td>
				<td class="border_users_l border_users_b border_users_r">
					<div align="left" class="floatleft"><input type='text' name='address' value='{$data.address}'></div>
							{*ERROR*}
                                 <div id="error_address" class="error_img" style="display:none;"><span class="error_text">Error!</span></div>
                          	{*/ERROR*}
				</td>
			</tr>
						
			<tr>
				<td class="border_users_l border_users_b us_gray" height="20">
					City:
				</td>
				<td class="border_users_l border_users_b border_users_r">
					<div align="left" class="floatleft">	<input type='text' name='city' value='{$data.city}'></div>
						    {*ERROR*}
                                  <div id="error_city" class="error_img" style="display:none;"><span class="error_text">Error!</span></div>
                            {*/ERROR*}							
				</td>
			</tr>
						
			<tr>
				<td class="border_users_l border_users_b us_gray" height="20">
					County:
				</td>
				<td class="border_users_l border_users_b border_users_r">
					<div align="left" class="floatleft">	<input type='text' name='county' value='{$data.county}'></div>
							 {*ERROR*}
                                  <div id="error_county" class="error_img" style="display:none;"><span class="error_text">Error!</span></div>
                             {*/ERROR*}				
				</td>
			</tr>
						
			{*STATE FIELD*}
			<tr>
				<td class="border_users_l border_users_b us_gray" height="20">
					State:
				</td>
				<td class="border_users_l border_users_b border_users_r">
					<div align="left" >
						<div class="floatleft"> 
							<select name="selectState" id="selectState" {if $selectMode eq true} style="display: block" {else} style="display: none" {/if}>
									{section name=i loop=$state}
										<option value='{$state[i].id}' {if $state[i].id eq $data.state} selected="selected" {/if}> {$state[i].name} </option>
									{/section}
							</select>
								
							<input type='text' name='textState' id='textState' value='{if $selectMode ne true}{$data.state}{/if}' {if $selectMode eq true} style="display: none" {else} style="display: black" {/if}>
						</div>
							{*ERROR*}
                               <div id="error_state" class="error_img" style="display:none;"><span class="error_text">Error!</span></div>
                            {*/ERROR*}									
					</div>
				</td>
			</tr>
						
			<tr>
				<td class="border_users_l border_users_b us_gray" height="20">
					Zip/Postal code:
				</td>
				<td class="border_users_l border_users_b border_users_r">
					<div align="left" style="padding:0px;">
						<div class="floatleft" >    
				               <input type="text" name="zip" value="{$data.zip}">
				        </div>				        	       						
										{*ERROR*}
											  <div id="error_zip" class="error_img" style="display:none;"><span class="error_text">Error!</span></div>
										{*/ERROR*}	                
					</div>
				</td>	
			</tr>
						
			<tr>
				<td class="border_users_l border_users_b us_gray" height="20">
					Country:
				</td>
				<td class="border_users_l border_users_b border_users_r">
					<div>								
						<select name="country" id="selectCountry" onchange="getStateList(this)">
						{section name=i loop=$country}
							<option value='{$country[i].id}' {if $country[i].id eq $data.country}selected="selected"{/if}> {$country[i].name} </option>
						{/section}
						</select>
					</div>
				</td>
			</tr>
			
			<tr>
				<td class="border_users_l border_users_b us_gray" height="20">
					Phone:
				</td>
				<td class="border_users_l border_users_b border_users_r">
					<div  class="floatleft" >
						<input type='text' name='phone' value='{$data.phone}'>
				    </div>				           
				    				{*ERROR*} 
                                  <div id="error_phone" class="error_img" style="display:none;"><span class="error_text">Error!</span></div>
                                  {*/ERROR*}                          
				</td>
			</tr>
						
			<tr>
				<td class="border_users_l border_users_b us_gray" height="20">
					Fax:
				</td>
				<td class="border_users_l border_users_b border_users_r">
					<div  class="floatleft" >
						  <input type='text' name='fax' value='{$data.fax}'> 
					</div>
						    {*ERROR*} 
                                  <div id="error_fax" class="error_img" style="display:none;"><span class="error_text">Error!</span></div>
                            {*/ERROR*} 
				</td>
			</tr>

			<tr>
				<td class="border_users_l border_users_b us_gray" height="20">
					Email:
				</td>
				<td class="border_users_l border_users_b border_users_r">
					<div  class="floatleft" >		
						<input type='text' name='email' value='{$data.email}'>
					</div>
							{*ERROR*}
                                  <div id="error_email" class="error_img" style="display:none;"><span class="error_text">Error!</span></div>
                            {*/ERROR*}
				</td>
			</tr>
					
			<tr>
				<td class="border_users_l border_users_b us_gray" height="20">
					Contact:					
				</td>
				<td class="border_users_l border_users_b border_users_r">
					<div  class="floatleft">
						 <input type='text' name='contact' value='{$data.contact}'> 
					</div>
							{*ERROR*}
                                 <div id="error_contact" class="error_img" style="display:none;"><span class="error_text">Error!</span></div>
                            {*/ERROR*}
				</td>
			</tr>
			
			<tr>
				<td class="border_users_l border_users_b us_gray" height="20">
					Title:
				</td>
				<td class="border_users_l border_users_b border_users_r">
					<div class="floatleft" >
						 <input type='text' name='title' value='{$data.title}'>
					</div>
							{*ERROR*}
                               <div id="error_title" class="error_img" style="display:none;"><span class="error_text">Error!</span></div>
                            {*/ERROR*}
				</td>
			</tr>
            <tr>
                <td class="border_users_l border_users_b us_gray" height="23px">
                    Jobber:
                </td>
                <td class="border_users_r border_users_l border_users_b">

					{if isset($jobberList)}
						 <div class="floatleft">
                          <input type="button" value="Choose Jobber" onclick="showJobber(); return false;">
						 </div>
					{/if}
							{*ERROR*}
                                 <div id="error_jobber" class="error_img" style="display:none;"><span class="error_text">Error!</span></div>
                            {*/ERROR*}				

                </td>
            </tr>						
			<tr>
				<td class="users_u_bottom">
			   	</td>
			    <td height="20" class="users_u_bottom_r">
				</td>
			</tr>						
			
		</table>
{*SELECT_JOBBER_POPUP*}
<div id="Jobberlist" title="Select Jobber" style="display:none;">
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
                                Jobber Name
                            </td>

                        </tr>
                        <tr>
                            <td colspan="2"  style="padding:0px;border-bottom:0px;">
							
                              
                                <table id="class_{$smarty.section.k.index}" name="class_{$smarty.section.k.index}" width="100%" cellpadding=0 cellspacing=0>

									
									
                                    {section name=i loop=$jobberList}
                                   
                                    <tr name="unitTypelist" id="row_{$smarty.section.i.index}">

                                        <td align="center" style="width:40px">
                                            <input type="checkbox" id="jobber[]" name="jobber[]" value="{$jobberList[i]->jobber_id}"
												{section  name=j loop=$facilityJobber}
													
													{if $jobberList[i]->jobber_id  == $facilityJobber[j].jobber_id}
													 checked 
													{/if}
												{/section}
											onclick="checkJobbers(this.value);">											
                                        </td>
                                        <td id="JobberName_{$smarty.section.i.index}" style="padding: 5px;">
                                            {$jobberList[i]->name}
                                        </td>

                                    </tr>
                                  							
                                    {/section}									
                                </table>

								
                            </td>
                        </tr>
                    </table>
                    <input id="categoryName" type="hidden" name="categoryName" value="{$request.category}">


    </div>
Note: Different jobbers can't supply the same products!
					
</div>
{*END OF POPUP*}		
{*DATA TO SAVE FROM POPUPS*}
<div style="display:none;">
	<div id="jobber_data">
	
	</div>
	{literal}		
	<script>addJobberData();</script>
	{/literal}
</div>
{*/END OF DATA*}		
		<table cellpadding="5" cellspacing="0" align="center" width="95%">
			<tr>
				<td>
				
				{*buttons*}
				<div align="right">					
					<input type='button' name='cancel' class="button" value='Cancel' 
						{if $request.action eq "addItem"} onClick="location.href='?action=browseCategory&category=company&id={$request.id}'"
						{elseif $request.action eq "edit"} onClick="location.href='?action=browseCategory&category=facility&id={$request.id}&bookmark=department'"
						{/if}
					>
					<input type='button' name='save' class="button" value='Save' onClick="saveFacilityDetails();">
				</div>
		
				{*hidden*}
				<input type='hidden' name='action' value='{$request.action}'>								
				{if $request.action eq "addItem"}
					<input type='hidden' name='company_id' value='{$request.id}'>
				{/if}		
				{if $request.action eq "edit"}
					<input type="hidden" name="id" value="{$request.id}">
				{/if}
				{*if $request.action eq "updateItem"}
					<input type="hidden" name="id" value="{$request.id}">
				{/if*}
				</td>
			</tr>
		</table>						
		
</div>

{include file="tpls:tpls/pleaseWait.tpl" text=$pleaseWaitReason}		
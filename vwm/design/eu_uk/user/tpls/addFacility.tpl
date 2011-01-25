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
				<td class="users_u_bottom">
			   	</td>
			    <td height="20" class="users_u_bottom_r">
				</td>
			</tr>						
			
		</table>
		
		
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
				
				{*choke for states  *}
				<input type='hidden' name="selectState" id="selectState" value="false">
				<input type='hidden' name='textState' id='textState' value='false'>
				</td>
			</tr>
		</table>						
		
</div>

{include file="tpls:tpls/pleaseWait.tpl" text=$pleaseWaitReason}		
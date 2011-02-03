<table class="top_block" width="100%" cellpadding="0" cellspacing="0">
	<tr>
		<td class="mg_left20" width="40%"  bgcolor="" valign="top">
		{*ЕРЕСЬ,ПРОВЕРИТЬ_И_УДАЛИТЬ*}									
			{if $categoryName eq "root"}
				<div >
					<h2 class="logininfo" style="font-family:Arial;padding-top:10px;">	Welcome to VOC-WEB-MANAGER!</h2>
				</div>
			{elseif $categoryName eq "company"}
				<a href="{$urlCompanyList}" class="id_company_link ">Company name</a> 
			{elseif $categoryName eq "facility"}
				<a href="{$urlCompanyList}" class="id_company_link">Company name</a> > 
				<a href="{$urlFacilityList}" class="id_company_link">Facility name </a>
			{elseif $categoryName eq "department"}
				<a href="{$urlCompanyList}" class="id_company_link ">Company name</a> > 
				<a href="{$urlFacilityList}" class="id_company_link">Facility name </a>> 
				<a href="{$urlDepartmentList}" class="id_company_link">Department name</a>
			{/if}
			
			{*========================================*}
			{*Company name > Faciality name > Department name*}
			<br><br>
			{if $categoryName eq "root"}
			{else}
				{if $validStatus ne true && $action ne "showAddItem" && $action ne "viewDetails" && $action ne "deleteItem" && $action ne "showEdit"}						
					{include file="tpls:tpls/controlBrowseCategory.tpl"}
				{/if}													
			{/if}
			{*========================================*}		
						
		</td>
						
		<td align="left">
		</td>
						
		<td  width="35%"valign="top" align="right">
			<div style="display:block;height:40%;width:300;" align="rignt">								
				
		
				<div   style="margin-top:15px;margin-right:10px;float:right;">
					<span class="nameCompany" >
						<p>{$accessname}</p>
					</span>
				</div>
			</div>
			{*		
            <div style="display:block;height:60%;width:300;float:right;" align="rignt">
                   <input type='text' name='' value='' style="margin-top:15px;margin-right:8px;float:right;">    
                   
            </div>                     
		</td>
						                                   
		<td valign="top" width="5%">
			<div  class="margintop10" align="center">	<input type="button" class="button" value="Search"> </div>
			<div  class="margintop10" align="center">
				  <span class="textbold " >
						<a href="#" style="text-decoration:underline;font-size:11px">Help</a>
				  </span>
				</div>
		</td>*}
		  
		<td   class=" padd_bot10  " valign="top" width="5%">
			<div  class="margintop10" align="center">	<input type="button" class="button" value="Logout" onclick="location.href='?action=logout'"> </div>			
			<div  class="margintop10" align="center">	<input type="button" class="button" value="Stats" onclick="location.href='?action=stats'"> </div>
		</td>
						
						
						
		
         </tr>
</table>
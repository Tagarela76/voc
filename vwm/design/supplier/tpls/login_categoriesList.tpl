<table class="top_block" width="100%" cellpadding="0" cellspacing="0">
	<tr>
		<td class="padd7" width="40%"  bgcolor="" valign="top">	
			{if $request.category eq "root"}

				{if !$permissions.root.view}
					<div>
						<h1 class="logininfo">Welcome to VOC-WEB-MANAGER!</h1>
					</div>
				{else}
					<a href="{$urlRoot}" class="id_company_link ">All jobbers</a>
				{/if}

            {elseif $request.category eq "sales"}

				{if $permissions.root.view}
					<a href="{$urlRoot}" class="id_company_link ">All jobbers</a>
					>
				{/if}
				{if !$permissions.sales.view}
					<!--a href="{$urlCompany}" class="id_company_link">{$jobberDetails.name}</a-->
					<span class="id_company_link ">{$jobberDetails.name}</span>
				{else}
					<span class="id_company_link ">{$jobberDetails.name}</span>
				{/if}


            {/if}



            <br>
            <br>

			{*Contacts*}
            {if $request.category == "company" || $request.category == "sales"}
				{if $request.action != 'addItem' && $request.action != 'deleteItem'}
					<i>{$jobberDetails.address}</i>
					<br>
					<i>{$jobberDetails.contact}&nbsp;( {$jobberDetails.phone} )</i>
					<br>
				{/if}
            {/if}

			{if $request.category != "root" && $request.action=="browseCategory"}
				{include file="tpls:tpls/controlBrowseCategory.tpl}
			{/if}
            {/*Contacts*}

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
			{if $request.category != "root"}
			<div  class="margintop10" align="center">	<input type="button" class="button" value="Users" onclick="location.href='?action=browseCategory&category=usersSupplier&jobberID={$request.jobberID}&supplierID={$request.supplierID}'"> </div>
			{/if}
			<!--div  class="margintop10" align="center">	<input type="button" class="button" value="Stats" onclick="location.href='?action=stats'"> </div-->
		</td>
						
						
						
		
         </tr>
</table>
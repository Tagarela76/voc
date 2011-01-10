{literal}	
	<script type="text/javascript">
		function checker(name,check)
		{			
			$("td[id = "+name+"] input:checkbox").attr('checked',check);
		}	
	</script>
{/literal}


{if $color eq "green"}
		{include file="tpls:tpls/notify/greenNotify.tpl" text=$message}
	{/if}
	{if $color eq "orange"}
		{include file="tpls:tpls/notify/orangeNotify.tpl" text=$message}
	{/if}
	{if $color eq "blue"}
		{include file="tpls:tpls/notify/blueNotify.tpl" text=$message}
	{/if}
	{if $color eq "blue2" && $itemsCount == 0}
		{include file="tpls:tpls/notify/blueNotify.tpl" text=$message}
	{/if}

<form id="modularControl" method="post" action="" >

<div align="center" class="control_panel_padd" >	
<div class="control_panel" class="logbg" align="left">
<div class="control_panel_tl">
<div class="control_panel_tr"><div class="control_panel_bl"><div class="control_panel_br">
<div class="control_panel_center">

<table  cellpadding="0" cellspacing="0" class="controlCategoriesList" style="height:30px;" >
	
	<tr>		
		<td rowspan=3 class="control_list" style="width:130px">
				Select: 
				<a onclick="CheckAll(this)" class="id_company1" >All</a>									
				 /
				<a onclick="unCheckAll(this)" class="id_company1">None</a>
		</td>
		<td>
			<div style="float:left; width:80px">
			
				<input type="submit" class="button" name="modularButton" value="save" >
			
			</div>
		</td>
		<td>
			<div style="float:left; width:80px">			
				<input type="submit" class="button" name="modularButton" value="cancel" >			
			</div>
		</td>
		
		{if $showInstall eq 'true'}
			<td>
				<div style="float:left; width:80px">				
					<input type="button" class="button" name="installButton" value="install modules" onclick='location.href="modules_install.php"'>			
				</div>
			</td>
		{/if}		
	</tr>
</table>
</div></div></div></div></div></div></div>
<input type='hidden' name='action' value="browseCategory">
<input type='hidden' name='categoryID' value="modulars">

	
<div class="padd7">
	
	<table  class="users" height="200"  cellspacing="0" cellpadding="0" align="center">
           {*TABLE HEADER*}
           <tr height="27" class="users_top_violet">
           		<td  class="users_u_top_violet" style='width:200px'>
           			Company
           		</td>
				<td width='50px'>Select</td>
				
		   		{section name=title loop=$modules}	
					<td width="40px" {if $smarty.section.title.last} class="users_u_top_r_violet" {/if}> {$modules[title]->name} </td>
				{/section}
				{if $smarty.section.title.total==0}<td width="40px"  class="users_u_top_r_violet" > &nbsp; </td>{/if}
									
			</tr>
			<tr class="users_top_lightgray users_u_top_size border_users_b ">
				<td ><div>&nbsp;</div></td>
				<td > <div>&nbsp;</div></td>
				{section name=select loop=$modules}	
					<td class="color_gray">
						<a onclick="checker('col_{$smarty.section.select.index}',true)">all</a>/<a onclick="checker('col_{$smarty.section.select.index}',false)">none</a>
			 		</td>
				{/section}
				
				{if $smarty.section.title.total==0}
					<td > <div>&nbsp;</div></td>
				{/if}		
			</tr>				 
								 
			
			{section name=company loop=$companyList}						
									
			<tr  height="10px" class="hov_company" id="row_{$smarty.section.company.index}">			
									
				<td  class="border_users_l border_users_r border_users_b">			      	
							{$companyList[company].name}					
				</td>
				
				<td width="50px" class=" border_users_r border_users_b"> <span style='display:inline-block;'><a onclick="CheckClassOfUnitTypes(document.getElementById('row_{$smarty.section.company.index}'))">all</a>/<a onclick="unCheckClassOfUnitTypes(document.getElementById('row_{$smarty.section.company.index}'))">none</a></span> </td>
		   		
		   		{section name=checks loop=$modules}
		   			{assign var = 'modName' value= $modules[checks]->name}
					{assign var = 'companyID' value=$companyList[company].id}	
					
					<td  class=" border_users_r border_users_b" id="col_{$smarty.section.checks.index}">
						<input type="checkbox" name="modularID[]" value ="chbox_{$companyList[company].id}_{$modules[checks]->id}"
							{if $defaultModuleList.$modName.$companyID eq "1" }
									checked							
							{/if}													
						>										
			 		</td>
				{/section}			
			</tr>
			{/section}	
				
			{if $smarty.section.company.total==0}
			<tr >
				<td   class="border_users_l border_users_r border_users_b" colspan="{math equation="x + y" x=$modules|@count y=2}"  align='center'> isn't companies </td>
			</tr>
			{else}		
			<tr >
				<td   class="border_users_l border_users_r " colspan="{math equation="x + y" x=$modules|@count y=2}" > &nbsp; </td>
			</tr>
			{/if}
	
	{*TABLE PRETTY BOTTOM*}				
	<tr>
		<td  height="20" class="users_u_bottom">&nbsp;</td>
		<td colspan="{$modules|@count}"  class="border_users"></td>
		<td   class="users_u_bottom_r"></td>
	</tr>
</table>
</div>
</form>
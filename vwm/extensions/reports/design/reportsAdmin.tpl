</form>
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

<form id="reportControl" method="post" action="" >

<div align="center" class="control_panel_padd" >	
<div class="control_panel" class="logbg" align="left">
<div class="control_panel_tl">
<div class="control_panel_tr"><div class="control_panel_bl"><div class="control_panel_br">
<div class="control_panel_center">

<table  cellpadding="0" cellspacing="0" class="controlCategoriesList" >
	
	<tr>
		
		<td rowspan=3 class="control_list" style="width:130px">
				Select: 
				<a onclick="CheckAll(this)" class="id_company1" >All</a>									
				 /
				<a onclick="unCheckAll(this)" class="id_company1">None</a>
				
		</td>
		<td>
			<div style="float:left; width:80px">
			
				<input type="submit" class="button" name="reportButton" value="save" >
			
			</div>
		</td>
		<td>
			<div style="float:left; width:80px">
			
				<input type="submit" class="button" name="reportButton" value="cancel" >
			
			</div>
		</td>
		
	</tr>
	
	
</table>
</div></div></div></div></div></div></div>
<input type='hidden' name='action' value="browseCategory">
<input type='hidden' name='categoryID' value="reports">

	
<div class="padd7">
	
	<table  class="users" height="200"  cellspacing="0" cellpadding="0" align="center">
           {*TABLE HEADER*}
           <tr height="27" class="users_top_violet">
           		<td width="20%" class="users_u_top_violet"></td>
				<td width="50px">Select</td>
		   		{section name=title loop=$reportList}	
					<td width="40px" {if $smarty.section.title.last} class="users_u_top_r_violet" {/if}> {$reportList[title]->name} </td>
				{/section}
									
			</tr>
			<tr class="users_top_lightgray users_u_top_size border_users_b border_users_r">
				<td class="border_users_l"><div>&nbsp;</div></td>
				<td class=""> <div>&nbsp;</div></td>
				{section name=select loop=$reportList}	
					<td class="color_gray">
						<a onclick="checker('col_{$smarty.section.select.index}',true)">all</a>/<a onclick="checker('col_{$smarty.section.select.index}',false)">none</a>
			 		</td>
				{/section}		
			</tr>				 
								 
			
			{section name=company loop=$companyList}						
									
			<tr  height="10px" class="hov_company" id="row_{$smarty.section.company.index}">			
									
				<td  class="border_users_l border_users_r border_users_b">			      	
							{$companyList[company]->name|escape}
				</td>
				
				<td width="40px" class=" border_users_r border_users_b"> <a onclick="CheckClassOfUnitTypes(document.getElementById('row_{$smarty.section.company.index}'))">all</a>/<a onclick="unCheckClassOfUnitTypes(document.getElementById('row_{$smarty.section.company.index}'))">none</a> </td>
		   		
		   		{section name=checks loop=$reportList}
		   			{assign var = 'reportID' value= $reportList[checks]->report_id}
					{assign var = 'companyID' value=$companyList[company]->company_id}	
					
					<td  class=" border_users_r border_users_b" id="col_{$smarty.section.checks.index}">
						<input type="checkbox" name="reportID[]" value ="chbox_{$companyList[company]->company_id}_{$reportList[checks]->report_id}"
							{if $defaultReportsList.$companyID.$reportID eq "1" }
									checked							
							{/if}													
						>										
			 		</td>
				{/section}		
				
			</tr>
			{/section}		
		
		
			<tr >
				<td   class="border_users_l border_users_r " colspan="{math equation="x + y" x=$reportList|@count y=2}" > &nbsp; </td>
			</tr>

	
	{*TABLE PRETTY BOTTOM*}				
	<tr>
		<td  height="20" class="users_u_bottom">&nbsp;</td>
		<td colspan="{$reportList|@count}"  class="border_users"></td>
		<td class="users_u_bottom_r"></td>
	</tr>
</table>
</div>
</form>
{literal}	
	<script type="text/javascript">
		function checker(name,check)
		{			
			$("td[id = "+name+"] input:checkbox").attr('checked',check);
		}	
	</script>
{/literal}
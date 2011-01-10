{if $color eq "green"}
	{include file="tpls:tpls/notify/greenNotify.tpl" text=$message}
{/if}
{if $color eq "orange"}
	{include file="tpls:tpls/notify/orangeNotify.tpl" text=$message}
{/if}
{if $color eq "blue"}
	{include file="tpls:tpls/notify/blueNotify.tpl" text=$message}
{/if}
</form>

	<table width="97%" align="center">		
		<tr>	
			{if $periodType=='month'}
			<td>&nbsp;</td>
			{/if}
					
			<td align="center"  class="link_bookmark">		
				<a href="?action=browseCategory&category=facility&id={$request.id}&bookmark=solventplan&tab=month" name="monthlyAnchor" {if $request.tab eq 'month'}class="active_link" {/if} >monthly</a> 		
				<a href="?action=browseCategory&category=facility&id={$request.id}&bookmark=solventplan&tab=quarter" name="quartelyAnchor" {if $request.tab eq 'quarter'}class="active_link" {/if} >quarterly</a> 		
				<a href="?action=browseCategory&category=facility&id={$request.id}&bookmark=solventplan&tab=semi-year" name="semiannuallyAnchor" {if $request.tab eq 'semi-year'}class="active_link" {/if} >semi-­annually</a>		
				<a href="?action=browseCategory&category=facility&id={$request.id}&bookmark=solventplan&tab=year" name="yearlyAnchor" {if $request.tab eq 'year'}class="active_link" {/if} >yearly</a>
			</td>
		</tr>
		<tr>
			
			{if $periodType=='month'}
			<form id="solventPlanEdit" name="solventPlanEdit" action="?action=edit&category=solventplan&tab=direct&facilityID={$request.id}" method="post" >			
			<td>
				<input type='button' id='edit' class='button' value='Edit' onclick="location.href='?action=edit&category=solventplan&tab=direct&facilityID={$request.id}&mm={$period.month}&yyyy={$period.year}'">
				{*<input type='submit' id='edit' class='button' value='Edit'>
				<input type='hidden' name='selectMonth' value='{$period.month}'/>
				<input type='hidden' name='selectYear' value='{$period.year}'/>*}
			</td>
			</form>
			{/if}
			
			<form id="solventPlanForm" name="solventPlanForm" action="?action=browseCategory&category=facility&id={$request.id}&bookmark=solventplan&tab={$request.tab}" method="post" >			
			<td align="center">
				{if $periodType == 'month'}
					<select name="selectMonth" {*onChange="document.forms['solventPlanForm'].submit();"*}>
						<option value="1" {if $period.month =='01'}selected='selected'{/if}>January</option>
						<option value="2" {if $period.month =='02'}selected='selected'{/if}>February</option>
						<option value="3" {if $period.month =='03'}selected='selected'{/if}>March</option>
						<option value="4" {if $period.month =='04'}selected='selected'{/if}>April</option>
						<option value="5" {if $period.month =='05'}selected='selected'{/if}>May</option>
						<option value="6" {if $period.month =='06'}selected='selected'{/if}>June</option>
						<option value="7" {if $period.month == '07'}selected='selected'{/if}>July</option>
						<option value="8" {if $period.month =='08'}selected='selected'{/if}>August</option>
						<option value="9" {if $period.month =='09'}selected='selected'{/if}>September</option>
						<option value="10" {if $period.month =='10'}selected='selected'{/if}>October</option>
						<option value="11" {if $period.month =='11'}selected='selected'{/if}>November</option>
						<option value="12" {if $period.month =='12'}selected='selected'{/if}>December</option>	
					</select>
				{/if}
				
				{if $periodType == 'quarter'}
					<select name="selectQuarter" {*onChange="document.forms['solventPlanForm'].submit();"*}>
						<option value="1" {if $period.quarter =='01'}selected='selected'{/if}>Quarter 1</option>
						<option value="2" {if $period.quarter =='02'}selected='selected'{/if}>Quarter 2</option>
						<option value="3" {if $period.quarter =='03'}selected='selected'{/if}>Quarter 3</option>
						<option value="4" {if $period.quarter =='04'}selected='selected'{/if}>Quarter 4</option>
					</select>
				{/if}

				{if $periodType == 'semi-year'}
					<select name="selectSemiyear" {*onChange="document.forms['solventPlanForm'].submit();"*}>
						<option value="1" {if $period.period =='01'}selected='selected'{/if}>first half-year</option>
						<option value="2" {if $period.period =='02'}selected='selected'{/if}>second half-year</option>					
					</select>
				{/if}			
				
				<select name="selectYear" {*onChange="document.forms['solventPlanForm'].submit();"*}>
					{section name=i loop=10}
						{math assign=yearEquation equation="y-x" x=$smarty.section.i.index y=$curYear}
						<option value='{$yearEquation}' {if $yearEquation ==$period.year}selected='selected'{/if}>{$yearEquation}</option>
					{/section}
				</select>
				
				<input type='submit' name='setPeriod' class="button" value='View' />
			</td>
			</form>				
		</tr>
	</table>	
	<br>

	
<table class="users" align="center" cellpadding="0" cellspacing="0">
	<tr class="users_u_top_size users_top_blue">
		<td class="users_u_top_blue">
			<span >Solvent Inputs and Outputs</span>
		</td>
		<td class="users_u_top_r_blue">
			&nbsp;
		</td>
	</tr>
	
	<tr class="users_u_top_size users_top_lightgray" >
		<td>Name</td>			
		<td>Value ({$unittype})</td>			
	</tr>
	
	<tr>
		<td class="border_users_l border_users_b border_users_r" height="20">I1 – Total Solvent Input</td>			
		<td class="border_users_b border_users_r">{$data->getTotalInput()}&nbsp;</td>
	</tr>
	
	{section name=i loop=$fields}
	<tr>
	{assign var=val value=$fields[i]}
		<td class="border_users_l border_users_b border_users_r" height="20">{$fields[i]} – {$data->outputNames.$val}</td>			
		<td class="border_users_b border_users_r">{$data->$val}&nbsp;</td>
	</tr>
	{/section}

	<tr>
		<td class="border_users_l border_users_b border_users_r" height="20">C – CONSUMPTION</td>			
		<td class="border_users_b border_users_r">{$data->consumption}&nbsp;</td>
	</tr>
	<tr>
		<td class="border_users_l border_users_b border_users_r" height="20">F – FUGITIVE EMISSION</td>			
		<td class="border_users_b border_users_r">{$data->fugitiveEmission}&nbsp;</td>
	</tr>
	
	{if $periodType == 'year'}
	<tr>
		<td class="border_users_l border_users_b border_users_r" height="20">ANNUAL ACTUAL SOLVENT EMISSION</td>			
		<td class="border_users_b border_users_r">{$data->annualActualSolventEmission}&nbsp;</td>
	</tr>
	{/if}
	
	<tr>
		<td height="20" class="users_u_bottom">&nbsp;</td>
		<td  height="20" class="users_u_bottom_r">&nbsp;</td>
	</tr>	
</table>	




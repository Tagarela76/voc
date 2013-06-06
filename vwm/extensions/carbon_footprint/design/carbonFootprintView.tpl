{if $color eq "green"}
	{include file="tpls:tpls/notify/greenNotify.tpl" text=$message}
{/if}
{if $color eq "orange"}
	{include file="tpls:tpls/notify/orangeNotify.tpl" text=$message}
{/if}
{if $color eq "blue"}
	{include file="tpls:tpls/notify/blueNotify.tpl" text=$message}
{/if}

<form id="carbonFootprintForm" name="carbonForm" action="?action=browseCategory&category=facility&id={$request.id}&bookmark=carbonfootprint&tab={$request.tab}" method="post" >
{*<input type='hidden' name='action' value='browseCategory'>
<input type='hidden' name='category' value='facility'>
<input type='hidden' name='id' value='{$request.id}'>
<input type='hidden' name='bookmark' value='carbonfootprint'>*}

<table width="97%" align="center">
		<tr>
			<td class="link_bookmark_left">
			
		   </td>
			<td align="right"  class="link_bookmark">		
				
			</td>
{*INDICATORS*}
<td rowspan="2" width="313px" valign="top">
        <div align="right" style="display:inline; zoom:1;">
            <table cellpadding="0" cellspacing="0">
                <tr>
                    <td>
                        <div style="display:inline">
                            {include file="tpls:carbon_footprint/design/yearlyTco2Indicator.tpl"}
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div style="display:inline">
                            {include file="tpls:carbon_footprint/design/monthlyTco2Indicator.tpl"}
                        </div>
                    </td>
                </tr>
            </table>
        </div>
</td>
{*/ INDICATORS*}

		</tr>
		<tr valign="middle">
		<td class="link_bookmark_left">
				<a href="?action=browseCategory&category=facility&id={$request.id}&bookmark=carbonfootprint&tab=setLimit" name="setLimitsAnchor">Set Limits</a>
		</td>
			<td  style='text-align:center;'>
				{if $periodType == 'month'}
					<select name="selectMonth" {*onChange="document.forms['carbonForm'].submit();"*}>
						<option value="1" {if $period.month =='01'}selected='selected'{/if}>January</option>
						<option value="2" {if $period.month =='02'}selected='selected'{/if}>February</option>
						<option value="3" {if $period.month =='03'}selected='selected'{/if}>March</option>
						<option value="5" {if $period.month =='05'}selected='selected'{/if}>April</option>
						<option value="4" {if $period.month =='04'}selected='selected'{/if}>May</option>
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
					<select name="selectQuarter" {*onChange="document.forms['carbonForm'].submit();"*}>
						<option value="1" {if $period.quarter =='01'}selected='selected'{/if}>Quarter 1</option>
						<option value="2" {if $period.quarter =='02'}selected='selected'{/if}>Quarter 2</option>
						<option value="3" {if $period.quarter =='03'}selected='selected'{/if}>Quarter 3</option>
						<option value="4" {if $period.quarter =='04'}selected='selected'{/if}>Quarter 4</option>
					</select>
				{/if}

				{if $periodType == 'semi-year'}
					<select name="selectSemiyear" {*onChange="document.forms['carbonForm'].submit();"*}>
						<option value="1" {if $period.period =='01'}selected='selected'{/if}>first half-year</option>
						<option value="2" {if $period.period =='02'}selected='selected'{/if}>second half-year</option>					
					</select>
				{/if}			
				
				<select name="selectYear" {*onChange="document.forms['carbonForm'].submit();"*}>
					{section name=i loop=10}
						{math assign=yearEquation equation="y-x" x=$smarty.section.i.index y=$curYear}
						<option value='{$yearEquation}' {if $yearEquation ==$period.year}selected='selected'{/if}>{$yearEquation}</option>
					{/section}
				</select>
				
				<input type='submit' class='button' name='setPeriod' value='View' />
			</td>				
		</tr>
		
	</table>	
	<br>
</form>

{*MONTHLY*}
{if $periodType == 'month'}

<div align="center" class="control_panel_padd" style="padding:5px;">	
	<div class="control_panel" class="logbg" align="left">
	<div class="control_panel_tl">
	<div class="control_panel_tr"><div class="control_panel_bl"><div class="control_panel_br">
	<div class="control_panel_center" style="margin: 5px;">
	
	<table  cellpadding="0" cellspacing="0" class="controlCategoriesList"  style="padding: 5px; " valign="middle">
	<tr><td>	
			<form id="carbonFootprintAdd" method="post" action="?action=addItem&category=carbonfootprint&facilityID={$request.id}" >
				<input type='hidden' name='selectMonth' value='{$period.month}' />
				<input type='hidden' name='selectYear' value='{$period.year}' />
				<input type ="submit" name="Add" value="Add" class="button" />&nbsp;
			</form>
			
			
	</td>
	<td>
		<form id="carbonFootprintDelete" method="post" action="?action=deleteItem&category=carbonfootprint&facilityID={$request.id}" >	
			
			<input type ="submit" name="Delete" value="Delete" class="button" />
	</td>
	</tr>	
	</table>

	</div></div	></div></div></div></div></div>	


<div id="monthly">

	<table class="users" align="center" cellpadding="0" cellspacing="0">
		<tr class="users_header_yellowgreen">
			<td colspan="2">
				<div class="users_header_yellowgreen_l"><div>Direct Emissions (fuel consumption)</div></div>
			</td>
			<td colspan="5">
				<div class="users_header_yellowgreen_r" ><div>&nbsp;</div></div>
			</td>
		</tr>
	
		<tr class="users_u_top_size users_top_lightgray" >
			<td width="60">
				<a id='all' href='#' onclick='allSelected()' style='color:black'>All</a>/<a id='none' style='color:black' href='#' onclick='noneSelected()'>None</a>
			</td>
			<td width="300">Fuel</td>
			<td>Description</td>
			<td>Quantity</td>			
			<td>Estimation adjustment</td>
			<td>Unit Type</td>
			<td>tCO2</td>		
		</tr>
		{section name=i loop=$directEmissionsList}					
		
		<tr>
			<td class="border_users_l border_users_b border_users_r"><input type="checkbox" name="checkCarbonFootprint[]" value="{$directEmissionsList[i]->id}"></td>
			<td class="border_users_b border_users_r"> 
				<a {if $permissions.viewItem}href="?action=edit&category=carbonfootprint&tab=direct&facilityID={$request.id}&id={$directEmissionsList[i]->id}"{/if}>
					<div style="width:100%;">
					{$directEmissionsList[i]->emissionFactor->name}
					</div>
				</a>
			</td>
			<td class="border_users_b border_users_r">
				<a {if $permissions.viewItem}href="?action=edit&category=carbonfootprint&tab=direct&facilityID={$request.id}&id={$directEmissionsList[i]->id}"{/if}>
					<div style="width:100%;">
					{$directEmissionsList[i]->description}
					</div>
				</a>
			</td>
			<td class="border_users_b border_users_r">
				<a {if $permissions.viewItem}href="?action=edit&category=carbonfootprint&tab=direct&facilityID={$request.id}&id={$directEmissionsList[i]->id}"{/if}>
					<div style="width:100%;">
						{$directEmissionsList[i]->quantity}
					</div>
				</a>
			</td>			
			<td class="border_users_b border_users_r">
				<a {if $permissions.viewItem}href="?action=edit&category=carbonfootprint&tab=direct&facilityID={$request.id}&id={$directEmissionsList[i]->id}"{/if}>
					<div style="width:100%;">
						{$directEmissionsList[i]->adjustment}
					</div>
				</a>
			</td>
			<td class="border_users_b border_users_r">
				<a {if $permissions.viewItem}href="?action=edit&category=carbonfootprint&tab=direct&facilityID={$request.id}&id={$directEmissionsList[i]->id}"{/if}>
					<div style="width:100%;">
						{$directEmissionsList[i]->unittype_id}
					</div>
				</a>
			</td>
			<td class="border_users_b border_users_r">
				<a {if $permissions.viewItem}href="?action=edit&category=carbonfootprint&tab=direct&facilityID={$request.id}&id={$directEmissionsList[i]->id}"{/if}>
					<div style="width:100%;">
						{$directEmissionsList[i]->tco2}
					</div>
				</a>
			</td>
			{*  <td class="border_users_b border_users_r">
				<a href="?action=edit&category=carbonfootprint&tab=direct&facilityID={$request.id}&id={$directEmissionsList[i]->id}">edit</a>
			</td>*}
		</tr>
		{/section}
		
		<tr class="users_u_top_size users_top_lightgray" >
			<td>TOTAL</td>
			<td colspan='5'></td>			
			<td>{$totalDirectEmissions}</td>			
		</tr>
		<tr>
            <td height="20" class="users_u_bottom">
            </td>
            <td colspan="6" height="27" class="users_u_bottom_r" align="right">
            	<div style="float:right">	</div>
	</form>	
            </td>
        </tr>
	</table>	

<form id="carbonFootprintEdit" method="post" action="?action=edit&category=carbonfootprint&facilityID={$request.id}&tab=indirect" >
<div align="center" class="control_panel_padd" style="padding:5px;padding-bottom:0px;">	
	<div class="control_panel" class="logbg" align="left">
	<div class="control_panel_tl">
	<div class="control_panel_tr"><div class="control_panel_bl"><div class="control_panel_br">
	<div class="control_panel_center" style="margin: 5px;">
	
	<table  cellpadding="0" cellspacing="0" class="controlCategoriesList"  style="" valign="middle">
	<tr>
	<td height="27" align="left">            	
				<input type ="submit" name="Edit" value="Edit" class="button"/>				
            </td>
	</tr>	
	</table>

</div></div	></div></div></div></div></div>	
	
	
	<input type='hidden' name='selectMonth' value='{$period.month}'>
	<input type='hidden' name='selectYear' value='{$period.year}'>	
	<div class=br_10></div>
	<table class="users" align="center" cellpadding="0" cellspacing="0">
		<tr class="users_u_top_size users_top_yellowgreen">
			<td class="users_u_top_yellowgreen" colspan="2">
				<span>Indirect Emissions (electricity and heat)</span>
			</td>
			<td class="users_u_top_r_yellowgreen" colspan="3">
				&nbsp;
			</td>
		</tr>
		
		<tr class="users_u_top_size users_top_lightgray" >
			<td>Electricity consumed (kWh)</td>
			<td>Estimation Adjustment</td>
			<td>Certificate Value</td>
			<td>Credit Value</td>
			<td>tCO2</td>
		</tr>
		
		<tr>
			<td class="border_users_l border_users_b border_users_r">{$indirectEmission->quantity}</td>
			<td class="border_users_b border_users_r">{$indirectEmission->adjustment}</td>
			<td class="border_users_b border_users_r">{$indirectEmission->certificate_value}</td>
			<td class="border_users_b border_users_r">{$indirectEmission->credit_value}</td>
			<td class="border_users_b border_users_r">{$indirectEmission->tco2}</td>
		</tr>
		<tr>
            <td height="20" class="users_u_bottom">
            </td>
            <td colspan="6" height="27" class="users_u_bottom_r" align="right">            	
						
            </td>
		</tr>	
						
	</table>
						
			
</div>	
</form>	
{/if}
{*/ MONTHLY*}

{*NO MONTHLY*}
{if $periodType != 'month'}
<div id="noMonthly">	
	{*
	<div align="center" class="control_panel_padd" >	
	<div class="control_panel" class="logbg" align="left">
	<div class="control_panel_tl">
	<div class="control_panel_tr"><div class="control_panel_bl"><div class="control_panel_br">
	<div class="control_panel_center">
	<table  cellpadding="0" cellspacing="0" class="controlCategoriesList" >	
		<input type ="button" name="Add" value="Add" class="button">&nbsp
		<input type ="button" name="Delete" value="Delete" class="button">	
	</table>
	</div></div	></div></div></div></div></div>	
	*}
	
	<table class="users" align="center" cellpadding="0" cellspacing="0">
		<tr class="users_u_top_size users_top_yellowgreen">
			<td class="users_u_top_yellowgreen" colspan="2">
				<span >Direct Emissions (fuel consumption)</span>
			</td>
			<td class="users_u_top_r_yellowgreen" colspan="2">
				&nbsp;
			</td>
		</tr>
	
		<tr class="users_u_top_size users_top_lightgray" >
			<td>Fuel</td>			
			<td>Quantity</td>
			<td>Default Unit Type</td>
			{*<td>Estimation adjustment</td>*}
			<td>tCO2</td>
		</tr>
		{section name=i loop=$directEmissionsList}
		<tr>
			<td class="border_users_l border_users_b border_users_r">{$directEmissionsList[i]->emissionFactor->name}</td>			
			<td class="border_users_b border_users_r">{$directEmissionsList[i]->quantity}</td>
			<td class="border_users_b border_users_r">{$directEmissionsList[i]->unittype_id}</td>
			{*<td class="border_users_b border_users_r">{$directEmissionsList[i]->adjustment}</td>*}
			<td class="border_users_b border_users_r">{$directEmissionsList[i]->tco2}</td>
		</tr>
		{/section}
		
		<tr class="users_u_top_size users_top_lightgray" >
			<td>TOTAL</td>			
			<td></td>
			<td></td>
			{*<td></td>*}
			<td>{$totalDirectEmissions}</td>
		</tr>
				        <tr><td height="20" class="users_u_bottom">&nbsp;</td><td colspan="3" height="20" class="users_u_bottom_r">&nbsp;</td></tr>	
	</table>	
	<br>
    {*
    <div align="center" class="control_panel_padd">
        <div class="control_panel" class="logbg" align="left">
            <div class="control_panel_tl">
                <div class="control_panel_tr">
                    <div class="control_panel_bl">
                        <div class="control_panel_br">
                            <div class="control_panel_center">
                                <table cellpadding="0" cellspacing="0" class="controlCategoriesList">
                                    <input type ="button" name="Edit" value="Edit" class="button;" style="height:20px">&nbsp 
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> 
	*}
	
	<br />		
	<table class="users" align="center" cellpadding="0" cellspacing="0">
		<tr class="users_u_top_size users_top_yellowgreen">
			<td class="users_u_top_yellowgreen" width="30%">
				<span>Indirect Emissions (electricity and heat)</span>
			</td>
			<td class="users_u_top_r_yellowgreen">
				&nbsp;
			</td>
		</tr>
		
		<tr class="users_u_top_size users_top_lightgray" >
			<td>Electricity consumed (kWh)</td>			
			<td>tCO2</td>
		</tr>
		
		<tr>
			<td class="border_users_l border_users_b border_users_r">{$indirectEmission->quantity}</td>			
			<td class="border_users_b border_users_r">{$indirectEmission->tco2}</td>
		</tr>
		        <tr><td height="20" class="users_u_bottom">&nbsp;</td><td colspan="2" height="20" class="users_u_bottom_r">&nbsp;</td></tr>					
	</table>
</div>
{/if}	
{*/ NO MONTHLY*}
<br>
    <div align="center" class="control_panel_padd">
        <div class="control_panel" class="logbg" align="left">
            <div class="control_panel_tl">
                <div class="control_panel_tr">
                    <div class="control_panel_bl">
                        <div class="control_panel_br">
                            <div class="control_panel_center">
	<div class="padd7 warning_text bold" style="display:table;height:27px;width:100%">
	<div class="floatleft">TOTAL TCO2</div>
	<div class="floatright" style="margin-right:10px;">{$total}</div>
	</div>			
  							</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>	
{literal}
	<script type='text/javascript'>
		function allSelected()
		{		
			$('input:checkbox').attr('checked',true);			
		}
		function noneSelected()
		{
			$('input:checkbox').attr('checked',false);
		}				
	</script>
{/literal}

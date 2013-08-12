{if $unprocessedRequests > 0}<div align="center"><a href="admin.php?action=vps&vpsAction=browseCategory&itemID=DBPRequests" style="color:black;">Defined Billing Plan Requests <b>({$unprocessedRequests})</b></a></div> {/if}


 {*shadow_table*}	
	             <table cellspacing="0" cellpadding="0" align="center" width="100%">
                         <tr>
                               <td valign="top" class="report_uploader_t_l_orange"></td>
                               <td valign="top" class="report_uploader_t_orange"></td>
                               <td valign="top" class="report_uploader_t_r_orange"></td>
						</tr>
						  <tr>
							   <td valign="top" class="report_uploader_l_orange"></td>
                               <td valign="top" class="report_uploader_c_orange">
	           {*shadow_table*}

	{*-------------------------BILLING_PLANS--------------------------*}

{*HIDE_IF_EDIT_ANOTHER_TABLE*}	
{if $edit != "limits" && $edit != "moduleBillingPlans"}
	
	<div>
	{if !$disableCurrencySelect}
		Select currency:
		<form method="POST" action = "admin.php?action=vps&vpsAction=browseCategory&itemID=billing">
			<select name='currencySelect' onchange="this.form.submit();">
			{foreach from=$currencies item=c}
				{if $curentCurrency.id != $c.id}
					<option value='{$c.id}'>{$c.iso} &mdash; {$c.description}</option>
				{else}
					<option value='{$c.id}' selected>{$c.iso} &mdash; {$c.description}</option>
				{/if}
			{/foreach}
			</select>
		</form>
	{else}
	Current currency: {$curentCurrency.iso} - {$curentCurrency.description}
	{/if}
	</div>
	
	{if $edit != "availableBillingPlans"}	
	<div>
		Click at  price to edit it
	</div>
	{else}	
		<form action="admin.php" method="get">
		<input type='hidden' name='currency' value='{$curentCurrency.id}' />
		<input value="{$b2cID}" type='hidden' name='b2cID' />
	{/if}			
	
	
		<table width="100%"  cellspacing="0" cellpadding="5" >
			<tr class="billingPlans_hd_orange">
				<td>EMISSION SOURCES</td>
				<td>ONE TIME SETUP CHARGE</td>
				<td colspan="3">SELF COMPLIANCE TRACKING & REPORTING SOLUTIONS</td>
				<td colspan="3">GYANT COMPLIANCE TRACKING & REPORTING SOLUTIONS</td>
			</tr>		
			<tr class="billingPlansMonths_orange">
				<td>&nbsp;</td><td>&nbsp;</td>
				{section name=i loop=$months}
					<td>											
						{$months[i]}
						{if $months[i] eq "1"} month {else} months {/if}						
					</td>
				{/section}
				{section name=i loop=$months}
					<td style="border-right:0px solid #fff;">											
						{$months[i]}
						{if $months[i] eq "1"} month {else} months {/if}						
					</td>
				{/section}	
			</tr>
			{section name=i loop=$sources}
				<tr class="billingPlans hov_company_biling">
				
					<td class="billingPlans_bot" style="border-left:1px solid #fff;">						
						{$sources[i].bplimit}
					</td>
					
					<td class="billingPlans_bot">
						{if $oneTimeCharge4bplimit eq $sources[i].bplimit}
							<input type="hidden" name="bplimit" value="{$sources[i].bplimit}">
							{$curentCurrency.sign} <input type="text" name="newPrice" value="{$sources[i].one_time_charge}">
						{elseif $edit eq "availableBillingPlans"}
							{$curentCurrency.sign} {$sources[i].one_time_charge}
						{else}
							<a href="admin.php?action=vps&vpsAction=showEdit&itemID=availableBillingPlans&oneTimeCharge4bplimit={$sources[i].bplimit}&currency={$curentCurrency.id}" style="color:black"><div>{$curentCurrency.sign} {$sources[i].one_time_charge}</div></a>						
						{/if}
					</td>
					
					{section name=j loop=$availablePlans}											
						{if $sources[i].bplimit eq $availablePlans[j].bplimit}
							<td class="billingPlans_bot">
								{if $editBillingPlanID eq $availablePlans[j].billingID}
									<input type="hidden" name="billingID" value="{$availablePlans[j].billingID}">						
									{$curentCurrency.sign} <input type="text" name="newPrice" value="{$availablePlans[j].price}">
								{elseif $edit eq "availableBillingPlans"}
									{$curentCurrency.sign} {$availablePlans[j].price}
								{else}																						
									<a href="admin.php?action=vps&vpsAction=showEdit&itemID=availableBillingPlans&id={$availablePlans[j].billingID}&currency={$curentCurrency.id}&b2cID={$availablePlans[j].b2c_id}" style="color:black"><div>{$curentCurrency.sign} {$availablePlans[j].price}</div></a>
								{/if}															
							</td>
						{/if}
					{/section}									
				</tr>
			{/section}
			<tr class="billingPlans">
				<td class="billingPlans_bot" style="border-left:1px solid #fff;">4</td>
				<td class="billingPlans_bot" colspan="{$monthsCount*2+1}" align="center">If you want to use 4 and more emission sources, you should contact VOC-WEB-MANAGER's Administrator.</td>				
			</tr>							
											
			<tr>
				<td colspan="{$monthsCount}"></td>
				<td style="padding:15px 0 0 0"></td>
			</tr>
		</table>	
		
		{if $edit != "availableBillingPlans"}	
		<br>
		<br>
		{else}
			<input type="submit" class="button" value="Save"><input type="button" class="button" value="Cancel" onClick="location.href='admin.php?action=vps&vpsAction=browseCategory&itemID=billing'">				
			<input type="hidden" name="action" value="vps">
			<input type="hidden" name="vpsAction" value="editItem">
			<input type="hidden" name="itemID" value="{$edit}">
		</form>
		{/if}		
		
{/if}	


{*-------------------------BILLING_PLANS_FOR_MODULES--------------------------*}

{*HIDE_IF_EDIT_ANOTHER_TABLE*}		
{if $edit != "limits" && $edit != "availableBillingPlans"}
	
	{if $edit != "moduleBillingPlans"}	
	<div>
		Click at  price to edit it
	</div>
	{else}	
	<form action="admin.php" method="get">
	<input type='hidden' name='currencyID' value='{$curentCurrency.id}' />
	<input type='hidden' name='editBillingPlanID' value='{$editBillingPlanID}' />
	
	{/if}	
		
		{*MODULES*}
		<table width="100%"  cellspacing="0" cellpadding="5" >
			<tr class="billingPlans_hd_orange">
				<td>MODULES</td>
				<td>ONE TIME SETUP CHARGE</td>
				<td colspan="3">SELF COMPLIANCE TRACKING & REPORTING SOLUTIONS</td>
				<td colspan="3">GYANT COMPLIANCE TRACKING & REPORTING SOLUTIONS</td>
			</tr>		
			<tr class="billingPlansMonths_orange">
				<td>&nbsp;</td><td>&nbsp;</td>
				{section name=i loop=$months}
					<td>											
						{$months[i]}
						{if $months[i] eq "1"} month {else} months {/if}						
					</td>
				{/section}
				{section name=i loop=$months}
					<td style="border-right:0px solid #fff;">											
						{$months[i]}
						{if $months[i] eq "1"} month {else} months {/if}						
					</td>
				{/section}	
			</tr>
			{section name=i loop=$modules}
				<tr class="billingPlans hov_company_biling">
				
					<td class="billingPlans_bot" style="border-left:1px solid #fff;">						
						{$modules[i].name}
					</td>
					
					<td class="billingPlans_bot">
						{$curentCurrency.sign} 0.00
					</td>					
					
					{*SELF*}
					{section name=m loop=$months}
						{section name=j loop=$moduleBillingPlans}											
							{if ($moduleBillingPlans[j].module_id eq $modules[i].id) && ($moduleBillingPlans[j].type eq 'self') && ($moduleBillingPlans[j].month_count eq $months[m])}
								<td class="billingPlans_bot">								
									{if $editBillingPlanID eq $moduleBillingPlans[j].id}
										<input type="hidden" name="id" value="{$moduleBillingPlans[j].id}">						
										{$curentCurrency.sign} <input type="text" name="newPrice" value="{$moduleBillingPlans[j].price}">
									{elseif $edit eq "moduleBillingPlans"}
										{$curentCurrency.sign} {$moduleBillingPlans[j].price}

									{else}																						
										<a href="admin.php?action=vps&vpsAction=editItem&itemID=moduleBillingPlans&id={$moduleBillingPlans[j].id}&currencyID={$curentCurrency.id}" style="color:black"><div>{$curentCurrency.sign} {$moduleBillingPlans[j].price}</div></a>
									{/if}															
								</td>
							{/if}
						{/section}								
					{/section}
					{*/SELF*}
					
					{*GIANT*}
					{section name=m loop=$months}
						{section name=j loop=$moduleBillingPlans}											
							{if ($moduleBillingPlans[j].module_id eq $modules[i].id) && ($moduleBillingPlans[j].type eq 'gyant') && ($moduleBillingPlans[j].month_count eq $months[m])}
								<td class="billingPlans_bot">								
									{if $editBillingPlanID eq $moduleBillingPlans[j].id}
										<input type="hidden" name="id" value="{$moduleBillingPlans[j].id}">						
										{$curentCurrency.sign} <input type="text" name="newPrice" value="{$moduleBillingPlans[j].price}">
									{elseif $edit eq "moduleBillingPlans"}
										{$curentCurrency.sign} {$moduleBillingPlans[j].price}
									{else}
										{if $moduleBillingPlans[j].price}																						
											<a href="admin.php?action=vps&vpsAction=editItem&itemID=moduleBillingPlans&id={$moduleBillingPlans[j].id}&currencyID={$curentCurrency.id}" style="color:black"><div>{$curentCurrency.sign} {$moduleBillingPlans[j].price}</div></a>										
										{/if}
									{/if}																								
								</td>							
							{/if}
							
						{/section}		
					{/section}
					{*/GIANT*}	
					
												
				</tr>
			{/section}
			<tr class="billingPlans">
				<td class="billingPlans_bot" style="border-left:1px solid #fff;"></td>
				<td class="billingPlans_bot" colspan="{$monthsCount*2+1}" align="center">If you want to use 4 and more emission sources, you should contact VOC-WEB-MANAGER's Administrator.</td>				
			</tr>							
											
			<tr>
				<td colspan="{$monthsCount}"></td>
				<td style="padding:15px 0 0 0"></td>
			</tr>
		</table>
		{*/MODULES*}
		
		{if $edit != "moduleBillingPlans"}	
		<br>
		<br>
		{else}
			<input type="submit" class="button" value="Save" name="Save"><input type="button" class="button" value="Cancel" onClick="location.href='admin.php?action=vps&vpsAction=browseCategory&itemID=billing'">				
			<input type="hidden" name="action" value="vps">
			<input type="hidden" name="vpsAction" value="editItem">
			<input type="hidden" name="itemID" value="{$edit}">
		</form>
		{/if}		
		
{/if}	
	
		{*-----------------------EXTRA----------------------*}
	
{*HIDE_IF_EDIT_ANOTHER_TABLE*}		
{if $edit != "availableBillingPlans" && $edit != "moduleBillingPlans"}
	
	{if $edit != "limits"}	
	<div>
		Click at  field to edit it <b>extra!</b>
	</div>
	{else}	
	<form action="admin.php" method="get">
	<input type="hidden" name="currencyID" value="{$curentCurrency.id}" />
	{/if}			
		
	
	<table width="100%"  cellspacing="0" cellpadding="5" >
			<tr class="billingPlans_hd_orange">
				<td>EMISSION SOURCES</td>				
				<td colspan="4">SELF COMPLIANCE TRACKING & REPORTING SOLUTIONS</td>
				<td colspan="4">GYANT COMPLIANCE TRACKING & REPORTING SOLUTIONS</td>
			</tr>		
			<tr class="billingPlansMonths_orange">		
			<td></td>		
				{section name=i loop=2}
					<td colspan="2">											
						Extra SDS						
					</td>
					<td colspan="2" style="border-right:0px solid #fff;">											
						Extra memory						
					</td>					
				{/section}				
			</tr>
			<tr class="billingPlans">	
			<td class="billingPlans_bot" style="border-left:1px solid #fff;">&nbsp;</td>												
				{section name=i loop=2}
					<td class="billingPlans_bot">											
						Default Limit						
					</td>
					<td class="billingPlans_bot">											
						+{$extraLimits.info.MSDS.increase_step} {$extraLimits.info.MSDS.unit_type}						
					</td>
					<td class="billingPlans_bot" >											
						Default Limit						
					</td>
					<td class="billingPlans_bot" >											
						+{$extraLimits.info.memory.increase_step} {$extraLimits.info.memory.unit_type}						
					</td>					
				{/section}
			</tr>
			
			{section name=i loop=$extraLimits.sources}
			<tr class="billingPlans hov_company_biling">
				<td class="billingPlans_bot" style="border-left:1px solid #fff;">
					{$extraLimits.sources[i]}
				</td>
				{section name=j loop=$extraLimits}
					{if $extraLimits[j].bplimit eq $extraLimits.sources[i] && $extraLimits[j].name eq "MSDS"}
					<td class="billingPlans_bot">
						{if $limitPriceID eq $extraLimits[j].limit_price_id && $subItemID eq "defaultLimit"}
							<input type="hidden" name="limitPriceID" value="{$extraLimits[j].limit_price_id}">						
							<input type="text" name="newDefaultLimit" value="{$extraLimits[j].default_limit}">
						{elseif  $edit eq "limits"}
							{$extraLimits[j].default_limit}
						{else}
							<a href="admin.php?action=vps&vpsAction=showEdit&itemID=limits&subItemID=defaultLimit&limitPriceID={$extraLimits[j].limit_price_id}" style="color:black"><div>{$extraLimits[j].default_limit}</div></a>
						{/if}
					</td>
					<td class="billingPlans_bot">
						{if $limitPriceID eq $extraLimits[j].limit_price_id && $subItemID eq "increaseCost"}
							<input type="hidden" name="limitPriceID" value="{$extraLimits[j].limit_price_id}">						
							{$curentCurrency.sign} <input type="text" name="newIncreaseCost" value="{$extraLimits[j].increase_cost}">
						{elseif  $edit eq "limits"}
							{$curentCurrency.sign} {$extraLimits[j].increase_cost}
						{else}
							<a href="admin.php?action=vps&vpsAction=showEdit&itemID=limits&subItemID=increaseCost&limitPriceID={$extraLimits[j].limit_price_id}&currencyID={$curentCurrency.id}" style="color:black"><div>{$curentCurrency.sign} {$extraLimits[j].increase_cost}</div></a>
						{/if}
					</td>
					{/if}
					{if $extraLimits[j].bplimit eq $extraLimits.sources[i] && $extraLimits[j].name eq "memory"}
					<td class="billingPlans_bot">
						{if $limitPriceID eq $extraLimits[j].limit_price_id && $subItemID eq "defaultLimit"}
							<input type="hidden" name="limitPriceID" value="{$extraLimits[j].limit_price_id}">						
							<input type="text" name="newDefaultLimit" value="{$extraLimits[j].default_limit}">
						{elseif  $edit eq "limits"}
							{$extraLimits[j].default_limit}
						{else}
							 <a href="admin.php?action=vps&vpsAction=showEdit&itemID=limits&subItemID=defaultLimit&limitPriceID={$extraLimits[j].limit_price_id}" style="color:black"><div>{$extraLimits[j].default_limit}</div></a>							
						{/if}						
					</td>
					<td class="billingPlans_bot">
						{if $limitPriceID eq $extraLimits[j].limit_price_id && $subItemID eq "increaseCost"}
							<input type="hidden" name="limitPriceID" value="{$extraLimits[j].limit_price_id}">						
							{$curentCurrency.sign} <input type="text" name="newIncreaseCost" value="{$extraLimits[j].increase_cost}">
						{elseif  $edit eq "limits"}
							{$curentCurrency.sign} {$extraLimits[j].increase_cost}
						{else}
							<a href="admin.php?action=vps&vpsAction=showEdit&itemID=limits&subItemID=increaseCost&limitPriceID={$extraLimits[j].limit_price_id}&currencyID={$curentCurrency.id}" style="color:black"><div>{$curentCurrency.sign} {$extraLimits[j].increase_cost}</div></a>
						{/if}						
					</td>
					{/if}
				{/section}
			</tr>
			{/section}							
			<tr>
				<td colspan="{$monthsCount}"></td>
				<td style="padding:15px 0 0 0"></td>
			</tr>
		</table>
		
		{if $edit != "limits"}	
		<br>
		<br>
		{else}
			<input type="submit" class="button" value="Save"><input type="button"  class="button" value="Cancel" onClick="location.href='admin.php?action=vps&vpsAction=browseCategory&itemID=billing'">				
			<input type="hidden" name="action" value="vps">
			<input type="hidden" name="vpsAction" value="editItem">
			<input type="hidden" name="itemID" value="{$edit}">
		</form>
		{/if}		
		
{/if}
	
		{*---------------------------DEFINED------------------------------------*}
		
{if $edit != "availableBillingPlans" && $edit != "limits" &&  $edit !="moduleBillingPlans"}	
			
	<div>		
		<div align="center"><b>DEFINED BILLING PLANS</b> (for 4 and more emission sources)</div>
		<input type="button" class="button" onclick="location.href='admin.php?action=vps&vpsAction=showAddItem&itemID=definedBillingPlans'" value="Add"/>		
		Click at  field to edit it 
	</div>
	<table width="100%"  cellspacing="0" cellpadding="3" >
			<tr class="billingPlans_hd_orange">
				<td>CUSTOMER</td>				
				<td>EMISSION SOURCES</td>
				<td>MONTHS</td>
				<td>ONE TIME SETUP CHARGE</td>
				<td>COST</td>
				<td>TYPE</td>
				<td>SDS LIMIT</td>
				<td>EXTRA SDS COST</td>
				<td>MEMORY LIMIT</td>
				<td>EXTRA MEMORY COST</td>
			</tr>
			<tr class="billingPlansMonths_orange"><td colspan="10" style="border-right:0px solid #fff;">&nbsp;</td></tr>
			{section name=i loop=$definedPlans}
			<tr class="billingPlans hov_company_biling" style="cursor:pointer" onclick="location.href='admin.php?action=vps&vpsAction=showEdit&itemID=definedBillingPlans&customerID={$definedPlans[i].customer_id}&currencyID={$curentCurrency.id}';">
				<td class="billingPlans_bot" style="border-left:1px solid #fff;">{$definedPlans[i].customerName}</td>				
				<td class="billingPlans_bot">{$definedPlans[i].bplimit}</td>
				<td class="billingPlans_bot">{$definedPlans[i].months_count}</td>
				<td class="billingPlans_bot">{$curentCurrency.sign} {$definedPlans[i].one_time_charge}</td>
				<td class="billingPlans_bot">{$curentCurrency.sign} {$definedPlans[i].price}</td>
				<td class="billingPlans_bot">{$definedPlans[i].type}</td>
				<td class="billingPlans_bot">{$definedPlans[i].limits.MSDS.default_limit}</td>
				<td class="billingPlans_bot">{$curentCurrency.sign} {$definedPlans[i].limits.MSDS.increase_cost}</td>
				<td class="billingPlans_bot">{$definedPlans[i].limits.memory.default_limit}</td>
				<td class="billingPlans_bot">{$curentCurrency.sign} {$definedPlans[i].limits.memory.increase_cost}</td>
			</tr>
			{/section}		
									
			<tr>
				<td colspan="{$monthsCount}"></td>
				<td style="padding:15px 0 0 0"></td>
			</tr>
		</table>
		
		
	{/if}	

	       {*/shadow_table*}	
					         </td>
					          <td valign="top" class="report_uploader_r_orange"></td>
			           </tr>
				        <tr>          
                             <td valign="top" class="report_uploader_b_l_orange"></td>
                             <td valign="top" class="report_uploader_b_orange"></td>
                             <td valign="top" class="report_uploader_b_r_orange"></td>                           				
				       </tr>
		      </table>
		
		      {*/shadow_table*}	
	
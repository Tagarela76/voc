{*SET MONTH IN STRING FORMAT *}
	{if $month=='01'}
		{assign value='January' var=monthString}
	{/if}
	{if $month=='02'}
		{assign value='February' var=monthString}
	{/if}
	{if $month=='03'}
		{assign value='March' var=monthString}
	{/if}
	{if $month=='05'}
		{assign value='May' var=monthString}
	{/if}
	{if $month=='04'}
		{assign value='April' var=monthString}
	{/if}
	{if $month=='06'}
		{assign value='June' var=monthString}
	{/if}
	{if $month=='07'}
		{assign value='July' var=monthString}
	{/if}
	{if $month=='08'}
		{assign value='August' var=monthString}
	{/if}
	{if $month=='09'}
		{assign value='September' var=monthString}
	{/if}
	{if $month=='10'}
		{assign value='October' var=monthString}
	{/if}
	{if $month=='11'}
		{assign value='November' var=monthString}
	{/if}
	{if $month=='12'}
		{assign value='December' var=monthString}
	{/if}
{*/SET MONTH IN STRING FORMAT *}
</form>

{*mantis 1299*}
<form id="solventPlanForm" name="solventPlanForm" method="get" >
	<table align="center">
		<tr>			
			<td align="center">				
					<input type="hidden" name="action" value="edit"/>
					<input type="hidden" name="category" value="solventplan"/>
					<input type="hidden" name="tab" value="direct"/>
					<input type="hidden" name="facilityID" value="{$request.facilityID}"/>
					<select name="mm" {*onChange="document.forms['solventPlanForm'].submit();"*}>
						<option value="1" {if $month =='01'}selected='selected'{/if}>January</option>
						<option value="2" {if $month =='02'}selected='selected'{/if}>February</option>
						<option value="3" {if $month =='03'}selected='selected'{/if}>March</option>
						<option value="4" {if $month =='04'}selected='selected'{/if}>April</option>
						<option value="5" {if $month =='05'}selected='selected'{/if}>May</option>
						<option value="6" {if $month =='06'}selected='selected'{/if}>June</option>
						<option value="7" {if $month == '07'}selected='selected'{/if}>July</option>
						<option value="8" {if $month =='08'}selected='selected'{/if}>August</option>
						<option value="9" {if $month =='09'}selected='selected'{/if}>September</option>
						<option value="10" {if $month =='10'}selected='selected'{/if}>October</option>
						<option value="11" {if $month =='11'}selected='selected'{/if}>November</option>
						<option value="12" {if $month =='12'}selected='selected'{/if}>December</option>	
					</select>
				
				<select name="yyyy" {*onChange="document.forms['solventPlanForm'].submit();"*}>
					{section name=i loop=10}
						{math assign=yearEquation equation="y-x" x=$smarty.section.i.index y=$curYear}
						<option value='{$yearEquation}' {if $yearEquation ==$year}selected='selected'{/if}>{$yearEquation}</option>
					{/section}
				</select>
				
				<input type='submit' class="button" value='Go to' />												
			</td>
		</tr>
	</table>
</form>		
{*/mantis 1299*}

<form name='editSolventPlan' enctype="multipart/form-data"  action='?action=edit&category=solventplan&facilityID={$request.facilityID}&mm={$month}&yyyy={$year}' method='post'>
<table class="users" align="center" cellpadding="0" cellspacing="0">
	<tr class="users_u_top_size users_top">
		<td class="users_u_top_yellowgreen">
			<span >Edit Solvent Inputs and Outputs for {$monthString} {$year}</span>
		</td>
		<td class="users_u_top_r_yellowgreen">
			&nbsp;
		</td>
	</tr>
	
	<tr class="users_u_top_size users_top_lightgray" >
		<td>Name</td>			
		<td>Value ({$unittype})</td>			
	</tr>
	
	<tr>
		<td class="border_users_l border_users_b border_users_r" height="20">I1 – Total Solvent Input</td>			
		<td class="border_users_b border_users_r">
		<div align='left' >
			{$input}&nbsp
		</div>
		{*ERORR*}
			{if $validation.summary=='failed'}													
				{if $validation.input!=null}
					<div class="error_img"><span class="error_text">{$validation.input}</span></div>
				{/if}					
			{/if}
		{*/ERORR*} 
		</td>
	</tr>
	
	{section name=i loop=$fields}
	<tr>
	{assign var=val value=$fields[i]}
		<td class="border_users_l border_users_b border_users_r" height="20">{$fields[i]} – {$data->outputNames.$val}</td>			
		<td class="border_users_b border_users_r">
			<div align='left'>
				<input type='text' name='{$val}' value='{$data->$val}' />&nbsp;
			</div>
			{*ERORR*}
			{if $validation.summary=='failed'}													
				{if $validation.$val!=null}
					<div class="error_img"><span class="error_text">{$validation.$val}</span></div>
				{/if}					
			{/if}
			{*/ERORR*} 
		</td>
	</tr>
	{/section}	
	
	<tr>
		<td height="20" class="users_u_bottom">&nbsp;</td>
		<td  height="20" class="users_u_bottom_r">&nbsp;</td>
	</tr>	
</table>	

<br>

<input type='hidden' name='selectMonth' value='{$month}'/>
<input type='hidden' name='selectYear' value='{$year}'/>

<div align='right'>
	<input type='submit' name='save' value='Save' class='button' />
	<input type='button' name='cancel' value='Cancel' class="button" onclick='location.href="?action=browseCategory&category=facility&id={$request.facilityID}{if $noOutputs neq true}&bookmark=solventplan&tab=month{else}&bookmark=department{/if}"' />
	<span style='padding-right:50'>&nbsp;</span>	
</div>
</form>
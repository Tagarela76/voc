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
				
			</td>
		</tr>
		<tr>
			
			{if $periodType=='month'}
			<form id="solventPlanEdit" name="solventPlanEdit" action="?action=edit&category=solventplan&tab=direct&facilityID={$request.id}" method="post" >			
			<td>
				{*<input type='button' id='edit' class='button' value='Edit' onclick="location.href='?action=edit&category=solventplan&tab=direct&facilityID={$request.id}&mm={$period.month}&yyyy={$period.year}'">
				<input type='submit' id='edit' class='button' value='Edit'>
				<input type='hidden' name='selectMonth' value='{$period.month}'/>
				<input type='hidden' name='selectYear' value='{$period.year}'/>*}
			</td>
			</form>
			{/if}
			
			<td></td>			
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




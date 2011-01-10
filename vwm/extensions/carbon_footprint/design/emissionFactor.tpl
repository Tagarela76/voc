<div class="padd7" align="center">
	{*PAGINATION*}
		{include file="tpls:tpls/pagination.tpl"}
	{*/PAGINATION*}
	<table class="users" width="100%" cellspacing="0" cellpadding="0">
		<thead>
			<tr class="users_u_top_size users_top_blue">
				<td class="users_u_top_blue">Emission Factor</td>
				<td>Unit type</td>
				<td class="users_u_top_r_blue">Value</td>
			</tr>
		</thead>
		<tbody>
			{if $emissionFactors|@count > 0}
				{foreach from=$emissionFactors item=emissionFactor}
				<tr class="hov_company"  height="20">
					<td class="border_users_l border_users_b"><a href="{$url}{$emissionFactor->id}"><div style="width:100%;">{$emissionFactor->name}</div></a></td>
					<td class="border_users_b border_users_l"><a href="{$url}{$emissionFactor->id}"><div style="width:100%;">{$unittype->getNameByID($emissionFactor->unittype_id)}</div></a></td>
					<td class="border_users_b border_users_l border_users_r"><a href="{$url}{$emissionFactor->id}"><div style="width:100%;">{$emissionFactor->emission_factor}</div></a></td>					 
				</tr>
				{/foreach}
			{else}
				<tr>
					<td colspan="3" class="border_users_l border_users_r" align="center">No emission factors</td>
				</tr>
			{/if}			
		</tbody>
		<tfoot>
			<tr>
	            <td class="users_u_bottom"></td>
        	    <td colspan="2" height="30" class="users_u_bottom_r"></td>
        	</tr>
		</tfoot>
	</table>
	
	{*PAGINATION*}
		{include file="tpls:tpls/pagination.tpl"}
	{*/PAGINATION*}	
</div>


</form>
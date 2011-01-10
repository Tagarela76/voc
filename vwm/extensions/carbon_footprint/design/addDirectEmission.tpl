<div>	
	<form action={if $request.action == 'edit'}"?action=edit&category=carbonfootprint&tab=direct&facilityID={$request.facilityID}&id={$request.id}"{else}"?action=addItem&category=carbonfootprint&facilityID={$request.facilityID}"{/if} method="POST">
		<br>
		<br>
		<table class="users" align="center" cellpadding="0" cellspacing="0">
			<tr class="users_u_top_size users_top_yellowgreen">
				<td class="users_u_top_yellowgreen" colspan="3">
					<span >Add fuel consumption</span>
				</td>
				<td class="users_u_top_r_yellowgreen" colspan="2">
					&nbsp;
				</td>
			</tr>
			<tr class="users_u_top_size users_top_lightgray">
				<td>Fuel</td>
				<td>Description</td>
				<td>Quantity</td>
				<td>Adjustment</td>
				<td>Unit type</td>
			</tr>
			<tr>
				<td class="border_users_l border_users_b border_users_r">					
					
						{if $request.action=='edit'}
							<input type='hidden' id='fuel' name='fuel' value={$data->emission_factor_id} />{$data->emissionFactor->name}
						{else}
						<select id= 'fuel' name='fuel' >
						{section name=i loop=$directEmissionsList}
							<option value={$directEmissionsList[i]->id}>{$directEmissionsList[i]->name}</option>
						{/section}
						</select>
						{/if}
					
				</td>
				<td class="border_users_b border_users_r">
					
					<input type='text' name='description' value='{$data->description}' />
					<br>
					{*ERORR*}
					{if $validation.summary=='failed'}
						{if $validation.description!=null}					
							<div class="error_img"><span class="error_text">{$validation.description}</span></div>						
						{/if}
					{/if}	
					{*/ERORR*}				
				</td>
				<td class="border_users_b border_users_r">
					<input type='text' name='quantity' value='{$data->quantity}'/>
					<br>
					{*ERORR*}
					{if $validation.summary=='failed'}						
						{if $validation.quantity!=null}
							<div class="error_img"><span class="error_text">{$validation.quantity}</span></div>
						{/if}
					{/if}
					{*/ERORR*}
				</td>
				<td class="border_users_b border_users_r">
					<input type='text' name='adjustment' value='{$data->adjustment}'/>{*  &nbsp;<span id='defaultUnittype'></span>*}
					<br>
					{*ERORR*}
					{if $validation.summary=='failed'}						
						{if $validation.adjustment!=null}
							<div class="error_img"><span class="error_text">{$validation.adjustment}</span></div>
						{/if}					
					{/if}
					{*/ERORR*}
				</td>
				<td class="border_users_b border_users_r">
					<select name='unittype' >		
					{if $request.action == 'edit'}
						{section name=i loop=$unittypeList}
							<option value='{$unittypeList[i].id}' {if $data->unittype_id == $unittypeList[i].id}selected{/if} >{$unittypeList[i].name}</option>
						{/section}
					{/if}										
					</select>
				</td>
			</tr>	
				<tr>
            <td height="20" class="users_u_bottom">
            </td>
            <td colspan="6" height="27" class="users_u_bottom_r" align="right">            	
						
            </tr>		
		</table>	
		
		
		<input type='hidden' name='selectMonth' value='{$month}'/>
		<input type='hidden' name='selectYear' value='{$year}'/>
		

	
		
		<table  cellpadding="0" cellspacing="0" align="center" width="100%">	
			<tr>
				<td align='right' class="padd7">				
					<input type='submit' name='save' value='Save' class='button' />
					<input type='button' name='cancel' value='Cancel' class="button" onclick='location.href="?action=browseCategory&category=facility&id={$request.facilityID}&bookmark=carbonfootprint&tab=month"' />		&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp				
				</td> 
			</tr>			
		</table>
			
		
	</form>
</div>
{if $request.action != 'edit' }
<script type="text/javascript">
	var unittypeList='{$unittypeList}';
	var selectedUnittype='{$data->unittype_id}';
	var selectedEmissionFactorId='{$data->emission_factor_id}';
</script>
<script type="text/javascript" src="modules/js/jquery-ui-1.8.2.custom/js/jquery-1.4.2.min.js"></script>
<script type="text/javascript" src="modules/js/addDirectEmission.js"></script>
{/if}
{*тут нужно все оформить как на темплейте, подгружать переданные ранее заполненные значения, отображать ошибки*}
<div>	
	<form action="?action=edit&category=carbonfootprint&facilityID={$request.facilityID}&tab=indirect" method="POST">
			<br>
		<br>
		<table class="users" align="center" cellpadding="0" cellspacing="0">
			<tr class="users_u_top_size users_top_yellowgreen">
				<td class="users_u_top_yellowgreen" colspan="2">
					<span >Edit electricity consumption</span>
				</td>
				<td class="users_u_top_r_yellowgreen" colspan="2">
					&nbsp;
				</td>
			</tr>
			<tr class="users_u_top_size users_top_lightgray">
				<td>Electricity consumed (kWh)</td>
				<td>Estimation Adjustment</td>
				<td>Certificate Value</td>
				<td>Credit Value</td>
			</tr>
			<tr>
		<td class="border_users_l border_users_b border_users_r">
		<input type='text' name='quantity' value='{$data->quantity}' />
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
		<input type='text' name='adjustment' value='{$data->adjustment}' />
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
		<input type='text' name='certificate_value' value='{$data->certificate_value}' />
		<br>
					{*ERORR*}
					{if $validation.summary=='failed'}						
						{if $validation.certificate_value!=null}
							<div class="error_img"><span class="error_text">{$validation.certificate_value}</span></div>
						{/if}					
					{/if}
					{*/ERORR*}
		</td>
		<td class="border_users_b border_users_r">
		<input type='text' name='credit_value' value='{$data->credit_value}' />
		<br>
					{*ERORR*}
					{if $validation.summary=='failed'}						
						{if $validation.credit_value!=null}
							<div class="error_img"><span class="error_text">{$validation.credit_value}</span></div>
						{/if}					
					{/if}
					{*/ERORR*}
	</td>
	</tr>
	<tr>
            <td height="20" class="users_u_bottom">
            </td>
            <td colspan="6" height="27" class="users_u_bottom_r" align="right">            	
						
            </td>
		</tr>		
	</table>	
		

		<input type='hidden' name='selectMonth' value='{$month}' />
		<input type='hidden' name='selectYear' value='{$year}' />
	<div align="center">			<table  cellpadding="0" cellspacing="0"class="padd7"   width="97%">	
			<tr>
				<td  align='right'>	
		<input type='submit' name='save' value='Save' class="button" />
		<input type='button' name='cancel' value='Cancel' class="button" onclick='location.href="?action=browseCategory&category=facility&id={$request.facilityID}&bookmark=carbonfootprint&tab=month"' />	
						</td>
			</tr>			
		</table></div>
	</form>
</div>
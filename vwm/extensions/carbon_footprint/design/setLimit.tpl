{*нужно оформить как на темплейте+отображать ошибки если они были*}
<form name='saveLimits' enctype="multipart/form-data" action="?action=browseCategory&category=facility&id={$request.id}&bookmark=carbonfootprint&tab=setLimit" method="POST">
	<table class="users" align="center" cellpadding="0" cellspacing="0">
		<tr class="users_u_top_size users_top_yellowgreen">
			<td class="users_u_top_yellowgreen" width="30%">
				<span >Set carbon footprint limits</span>
			</td>
			<td class="users_u_top_r_yellowgreen">
				&nbsp;
			</td>
		</tr>
		<tr class="users_u_top_size users_top_lightgray">				
			<td>Monthly limit</td>
			<td></td>			
		</tr>
		<tr>
			<td class="border_users_l border_users_b border_users_r">
				Value (tCO2)
			</td>
			<td class="border_users_b border_users_r">				
				<input type='text' name='monthLimit' value='{$limits.monthly.value}'>
				<br>				
				{*ERORR*}
				{if $validation.summary=='failed'}						
					{if $validation.month_value!=null}
						<div class="error_img"><span class="error_text">{$validation.month_value}</span></div>
					{/if}					
				{/if}
				{*/ERORR*}
			</td>			
		</tr>
		<tr>
			<td class="border_users_l border_users_b border_users_r">
				Show gauge
			</td>
			<td class="border_users_b border_users_r">
				<input type='checkbox' name='showMonthly' {if $limits.monthly.show eq true} checked{/if}>
			</td>			
		</tr>
		<tr class="users_u_top_size users_top_lightgray">				
			<td>Yearly limit</td>
			<td></td>			
		</tr>
		<tr>
			<td class="border_users_l border_users_b border_users_r">
				Value (tCO2)
			</td>
			<td class="border_users_b border_users_r">				
				<input type='text' name='annualLimit' value='{$limits.annual.value}'>	
				<br>				
				{*ERORR*}
				{if $validation.summary=='failed'}						
					{if $validation.annual_value!=null}
						<div class="error_img"><span class="error_text">{$validation.annual_value}</span></div>
					{/if}					
				{/if}
				{*/ERORR*}
			</td>			
		</tr>
		<tr>
			<td class="border_users_l border_users_b border_users_r">
				Show gauge
			</td>
			<td class="border_users_b border_users_r">
				<input type='checkbox' name='showAnnual' {if $limits.annual.show eq true} checked{/if}>
			</td>			
		</tr>
		<tr>
            <td class="users_u_bottom" width="18%">
            </td>
            <td height="23px" class="users_u_bottom_r">
            </td>
        </tr>
	</table>		
	<table  cellpadding="0" cellspacing="0" class="padd7" align='center' width="97%">	
		<tr>
			<td align='right'>				
				<input type='submit' name='save' value='Save' class='button' />
				<input type='button' name='cancel' value='Cancel' class="button" onclick='location.href="?action=browseCategory&category=facility&id={$request.id}&bookmark=carbonfootprint&tab=month"' />
			</td>
		</tr>			
	</table>
</form>
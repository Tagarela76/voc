{if $problems.conflict}
	{$problems.conflict}
{/if}

	


{if $edit}
 {*shadow_table*}	
	             <table cellspacing="0" cellpadding="0" align="center" width="100%">
                         <tr>
                               <td valign="top" class="report_uploader_t_l"></td>
                               <td valign="top" class="report_uploader_t"></td>
                               <td valign="top" class="report_uploader_t_r"></td>
						</tr>
						  <tr>
							   <td valign="top" class="report_uploader_l"></td>
                               <td valign="top" class="report_uploader_c">
	           {*shadow_table*}
<form action="admin.php?action=vps&vpsAction=confirmEdit&itemID={$bookmarkType}" method="post">
<table width="100%" cellpadding=0 cellspacing=0 class="others_vps">
	<tr>
		<td class="other_vps_td">
			PayPal Merchant Email
		</td>
		<td>
			<input type="text" name="paypal_merchant_email" value="{$config.paypal_merchant_email}">
			{if $problems.paypal_merchant_email}
			<div style="color:red">FAILED</div>
			{/if}			
		</td>		
	</tr>
	<tr>
		<td class="other_vps_td">
			PayPal Merchant ID
		</td>
		<td>
			<input type="text" name="paypal_merchant_id" value="{$config.paypal_merchant_id}">			
		</td>		
	</tr>
	<tr>
		<td class="other_vps_td">
			Trial period length
		</td>
		<td>
			<input type="text" name="trial_period" value="{$config.trial_period}"> days
			{if $problems.trial_period}
			<div style="color:red">FAILED</div>
			{/if}
		</td>		
	</tr>
	<tr>
		<td class="other_vps_td">
			Registration at VPS period 
		</td>
		<td>
			<input type="text" name="vps_registration_period" value="{$config.vps_registration_period}"> days
			{if $problems.vps_registration_period}
			<div style="color:red">FAILED</div>
			{/if}
		</td>		
	</tr>
	<tr>
		<td class="other_vps_td">
			Invoice generation period
		</td>
		<td>
			<input type="text" name="invoice_generation_period" value="{$config.invoice_generation_period}"> days
			{if $problems.invoice_generation_period}
			<div style="color:red">FAILED</div>
			{/if}
		</td>		
	</tr>
	
	<tr>
		<td class="other_vps_td">
			Limit invoice suspension period
		</td>
		<td>
			<input type="text" name="limit_suspension_period" value="{$config.limit_suspension_period}"> days
			{if $problems.limit_suspension_period}
			<div style="color:red">FAILED</div>
			{/if}			
		</td>		
	</tr>
	
	<tr class="other_vps_tr">
		<td colspan="2">
			New invoice e-mail:
		</td>
	</tr>
	<tr>
		<td class="other_vps_td">
			 Subject
		</td>
		<td>
			<input type="text" name="invoice_generation_email_subject" value="{$config.invoice_generation_email_subject}">
		</td>
	</tr>
	<tr>
		<td class="other_vps_td">
			Message
		</td>
		<td>
			<textarea name="invoice_generation_email_message">{$config.invoice_generation_email_message}</textarea>
		</td>
	</tr>	
	<tr>
		<td class="other_vps_td">
			First notification period
		</td>
		<td>
			<input type="text" name="first_notification_period" value="{$config.first_notification_period}"> days
			{if $problems.first_notification_period}
			<div style="color:red">FAILED</div>
			{/if}
		</td>
	</tr>
	<tr class="other_vps_tr">
		<td colspan="2">
			First notification e-mail:
		</td>
	</tr>
	<tr>
		<td class="other_vps_td">
			 Subject
		</td>
		<td>
			<input type="text" name="first_notification_email_subject" value="{$config.first_notification_email_subject}">
		</td>
	</tr>
	<tr>
		<td class="other_vps_td">
			Message
		</td>
		<td>
			<textarea name="first_notification_email_message">{$config.first_notification_email_message}</textarea>
		</td>
	</tr>	
	<tr>
		<td class="other_vps_td">
			Second notification period
		</td>
		<td>
			<input type="text" name="second_notification_period" value="{$config.second_notification_period}"> days
			{if $problems.second_notification_period}
			<div style="color:red">FAILED</div>
			{/if}
		</td>
	</tr>
	<tr class="other_vps_tr">
		<td colspan="2">
			Second notification e-mail:
		</td>
	</tr>
	<tr>
		<td class="other_vps_td">
			 Subject
		</td>
		<td>
			<input type="text" name="second_notification_email_subject" value="{$config.second_notification_email_subject}">
		</td>
	</tr>
	<tr>
		<td class="other_vps_td">
			Message
		</td>
		<td>
			<textarea name="second_notification_email_message">{$config.second_notification_email_message}</textarea>
		</td>
	</tr>		
	<tr class="other_vps_tr">
		<td colspan="2">
			Deactivate customer e-mail
		</td>
	</tr>
	<tr>
		<td class="other_vps_td">
			 Subject
		</td>
		<td>
			<input type="text" name="deacivate_email_subject" value="{$config.deacivate_email_subject}">
		</td>
	</tr>
	<tr>
		<td class="other_vps_td">
			Message
		</td>
		<td>
			<textarea name="deacivate_email_message">{$config.deacivate_email_message}</textarea>
		</td>
	</tr>
	
	<tr class="other_vps_tr">
		<td colspan="2">
			Change customer's billing plan e-mail
		</td>
	</tr>
	<tr>
		<td class="other_vps_td">
			 Subject
		</td>
		<td>
			<input type="text" name="change_customer_bp_email_subject" value="{$config.change_customer_bp_email_subject}">			
		</td>
	</tr>
	<tr>
		<td class="other_vps_td">
			Message
		</td>
		<td>
			<textarea name="change_customer_bp_email_message">{$config.change_customer_bp_email_message}</textarea>			
		</td>
	</tr>
	
	<tr class="other_vps_tr">
		<td colspan="2">
			New scheduled customer's billing plan e-mail
		</td>
	</tr>
	<tr>
		<td class="other_vps_td">
			 Subject
		</td>
		<td>
			<input type="text" name="schedule_bp_email_subject" value="{$config.schedule_bp_email_subject}">			
		</td>
	</tr>
	<tr>
		<td class="other_vps_td">
			Message
		</td>
		<td>
			<textarea name="schedule_bp_email_message">{$config.schedule_bp_email_message}</textarea>			
		</td>
	</tr>	
	
	<tr class="other_vps_tr">
		<td colspan="2">
			Change customer's tariffs e-mail
		</td>
	</tr>
	<tr>
		<td class="other_vps_td">
			 Subject
		</td>
		<td>
			<input type="text" name="change_customer_tariffs_email_subject" value="{$config.change_customer_tariffs_email_subject}">			
		</td>
	</tr>
	<tr>
		<td class="other_vps_td">
			Message
		</td>
		<td>
			<textarea name="change_customer_tariffs_email_message">{$config.change_customer_tariffs_email_message}</textarea>			
		</td>
	</tr>
	
	<tr class="other_vps_tr">
		<td colspan="2">
			Change customer's limits e-mail
		</td>
	</tr>
	<tr>
		<td class="other_vps_td">
			 Subject
		</td>
		<td>
			<input type="text" name="change_customer_limit_email_subject" value="{$config.change_customer_limit_email_subject}">			
		</td>
	</tr>
	<tr>
		<td class="other_vps_td">
			Message
		</td>
		<td>
			<textarea name="change_customer_limit_email_message">{$config.change_customer_limit_email_message}</textarea>			
		</td>
	</tr>
	
	<tr class="other_vps_tr">
		<td colspan="2">
			New invoice e-mail
		</td>
	</tr>
	<tr>
		<td class="other_vps_td">
			 Subject
		</td>
		<td>
			<input type="text" name="new_invoice_email_subject" value="{$config.new_invoice_email_subject}">			
		</td>
	</tr>
	<tr>
		<td class="other_vps_td">
			Message
		</td>
		<td>
			<textarea name="new_invoice_email_message">{$config.new_invoice_email_message}</textarea>			
		</td>
	</tr>	
		
</table>
<div align="center"><input type="submit" value="Save"></div>

</form>

  {*/shadow_table*}	
					         </td>
					          <td valign="top" class="report_uploader_r"></td>
			           </tr>
				        <tr>          
                             <td valign="top" class="report_uploader_b_l"></td>
                             <td valign="top" class="report_uploader_b"></td>
                             <td valign="top" class="report_uploader_b_r"></td>                           				
				       </tr>
		      </table>
		
		      {*/shadow_table*}	

{else}
 {*shadow_table*}	
	             <table cellspacing="0" cellpadding="0" align="center" width="100%">
                         <tr>
                               <td valign="top" class="report_uploader_t_l"></td>
                               <td valign="top" class="report_uploader_t"></td>
                               <td valign="top" class="report_uploader_t_r"></td>
						</tr>
						  <tr>
							   <td valign="top" class="report_uploader_l"></td>
                               <td valign="top" class="report_uploader_c">
	           {*shadow_table*}
<table width="100%" align="center" class="others_vps" cellspacing="0" cellpadding="0">
	<tr>
		<td class="other_vps_td" class="other_vps_td">
			PayPal Merchant Email
		</td>
		<td>
			{$config.paypal_merchant_email}			
		</td>		
	</tr>
	<tr>
		<td class="other_vps_td">
			PayPal Merchant ID
		</td>
		<td>
			{$config.paypal_merchant_id}			
		</td>		
	</tr>
	<tr>
		<td class="other_vps_td" class="other_vps_td">
			Trial period length
		</td>
		<td>
			{$config.trial_period} days			
		</td>		
	</tr>
	<tr>
		<td class="other_vps_td">
			Registration at VPS period 
		</td>
		<td>
			{$config.vps_registration_period} days			
		</td>		
	</tr>
	<tr>
		<td class="other_vps_td">
			Invoice generation period
		</td>
		<td>
			{$config.invoice_generation_period} days			
		</td>		
	</tr>
	<tr>
		<td class="other_vps_td">
			Limit invoice suspension period
		</td>
		<td>
			{$config.limit_suspension_period} days			
		</td>		
	</tr>
	<tr class="other_vps_tr">
		<td colspan="2">
			New invoice e-mail:
		</td>
	</tr>
	<tr>
		<td class="other_vps_td">
			 Subject
		</td>
		<td>
			{$config.invoice_generation_email_subject}
		</td>
	</tr>
	<tr>
		<td class="other_vps_td">
			Message
		</td>
		<td>
			<textarea readonly>{$config.invoice_generation_email_message}</textarea>
		</td>
	</tr>	
	<tr>
		<td class="other_vps_td">
			First notification period
		</td>
		<td>
			{$config.first_notification_period} days			
		</td>
	</tr>
	<tr class="other_vps_tr">
		<td colspan="2">
			First notification e-mail:
		</td>
	</tr>
	<tr>
		<td class="other_vps_td">
			 Subject
		</td>
		<td>
			{$config.first_notification_email_subject}
		</td>
	</tr>
	<tr>
		<td class="other_vps_td">
			Message
		</td>
		<td>
			<textarea readonly>{$config.first_notification_email_message}</textarea>
		</td>
	</tr>	
	<tr>
		<td class="other_vps_td">
			Second notification period
		</td>
		<td>
			{$config.second_notification_period} days			
		</td>
	</tr>
	<tr class="other_vps_tr">
		<td colspan="2">
			Second notification e-mail:
		</td>
	</tr>
	<tr>
		<td class="other_vps_td">
			 Subject
		</td>
		<td>
			{$config.second_notification_email_subject}
		</td>
	</tr>
	<tr>
		<td class="other_vps_td">
			Message
		</td>
		<td>
			<textarea readonly>{$config.second_notification_email_message}</textarea>
		</td>
	</tr>		
	<tr class="other_vps_tr">
		<td colspan="2">
			Deactivate customer e-mail
		</td>
	</tr>
	<tr>
		<td class="other_vps_td">
			 Subject
		</td>
		<td>
			{$config.deacivate_email_subject}
		</td>
	</tr>
	<tr>
		<td class="other_vps_td">
			Message
		</td>
		<td>
			<textarea readonly>{$config.deacivate_email_message}</textarea>
		</td>
	</tr>
	
	<tr class="other_vps_tr">
		<td colspan="2">
			Change customer's billing plan e-mail
		</td>
	</tr>
	<tr>
		<td class="other_vps_td" class="other_vps_td">
			 Subject
		</td>
		<td>
			{$config.change_customer_bp_email_subject}
		</td>
	</tr>
	<tr>
		<td class="other_vps_td">
			Message
		</td>
		<td>
			<textarea readonly>{$config.change_customer_bp_email_message}</textarea>
		</td>
	</tr>
	
	<tr class="other_vps_tr">
		<td colspan="2">
			New scheduled customer's billing plan e-mail
		</td>
	</tr>
	<tr>
		<td class="other_vps_td">
			 Subject
		</td>
		<td>
			{$config.schedule_bp_email_subject}
		</td>
	</tr>
	<tr>
		<td class="other_vps_td">
			Message
		</td>
		<td>
			<textarea readonly>{$config.schedule_bp_email_message}</textarea>
		</td>
	</tr>	
	
	<tr class="other_vps_tr">
		<td colspan="2">
			Change customer's tariffs e-mail
		</td>
	</tr>
	<tr>
		<td class="other_vps_td">
			 Subject
		</td>
		<td>
			{$config.change_customer_tariffs_email_subject}
		</td>
	</tr>
	<tr>
		<td class="other_vps_td">
			Message
		</td>
		<td>
			<textarea readonly>{$config.change_customer_tariffs_email_message}</textarea>
		</td>
	</tr>
	
	<tr class="other_vps_tr">
		<td colspan="2">
			Change customer's limits e-mail
		</td>
	</tr>
	<tr>
		<td class="other_vps_td">
			 Subject
		</td>
		<td>
			{$config.change_customer_limit_email_subject}
		</td>
	</tr>
	<tr>
		<td class="other_vps_td">
			Message
		</td>
		<td>
			<textarea readonly>{$config.change_customer_limit_email_message}</textarea>
		</td>
	</tr>
	
	<tr class="other_vps_tr">
		<td colspan="2">
			New invoice e-mail
		</td>
	</tr>
	<tr>
		<td class="other_vps_td">
			 Subject
		</td>
		<td>
			{$config.new_invoice_email_subject}
		</td>
	</tr>
	<tr>
		<td class="other_vps_td">
			Message
		</td>
		<td>
			<textarea readonly>{$config.new_invoice_email_message}</textarea>
		</td>
	</tr>	
</table>
  {*/shadow_table*}	
					         </td>
					          <td valign="top" class="report_uploader_r"></td>
			           </tr>
				        <tr>          
                             <td valign="top" class="report_uploader_b_l"></td>
                             <td valign="top" class="report_uploader_b"></td>
                             <td valign="top" class="report_uploader_b_r"></td>                           				
				       </tr>
		      </table>
		
		      {*/shadow_table*}	
			  <div align="center"><div style="padding:0 0 0 75%"><input type="button" onclick="location.href='admin.php?action=vps&vpsAction=showEdit&itemID=other'" value="Edit" class="button"/></div></div>

{/if}

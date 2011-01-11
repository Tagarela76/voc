{include file="tpls:bookmarks_vps.tpl"}
<div class="padd7">
		<table class="users"  align="center" cellpadding="0" cellspacing="0">
			<tr class="users_top_yellowgreen" >
				<td class="users_u_top_yellowgreen" width="5%" height="27" >
					Invoices
				</td>
				<td class="users_u_top_r_yellowgreen" colspan="11">
				</td>				
			</tr>
			<tr  bgcolor="#e3e3e3">

			<td class="border_users_l border_users_b" >InvNum</td>
			<td class="border_users_l border_users_b" >Items Included</td>
			<td class="border_users_r border_users_b">Setup Charge</td>
			<td class="border_users_r border_users_b">Amount</td>
			<td class="border_users_r border_users_b">Discount</td>
			<td class="border_users_r border_users_b">Total</td>					
			<td class="border_users_r border_users_b">Total Paid</td>
			<td class="border_users_r border_users_b">Total Due</td>
			<td class="border_users_r border_users_b">Date Created</td>
			<td class="border_users_r border_users_b">Suspension Date</td>
			{*<td class="border_users_r border_users_b">Billing Period Date Start</td>
			<td class="border_users_r border_users_b">Billing Period Date End</td>*}
			{if $currentBookmark eq "All"}	
				<td class="border_users_r border_users_b">Status</td>
			{/if}
			{if $currentBookmark eq "Due"}	
				<td class="border_users_r border_users_b">&nbsp;</td>
				<td class="border_users_r border_users_b">Pay from ballance</td>
			{/if}
		</tr>	
		{if $invoiceListCount eq 0}
			<tr>
				<td class="border_users_l border_users_b border_users_r" colspan="12" align="center">
					<h3>No invoices here.</h3>
				</td>
			</tr>
		{else}	
		{section name=i loop=$invoiceList}
		
			{assign var=currencyID value=$invoiceList[i].currency_id}
			
			<tr height="20" class="hov_company_vps">
				<td class="border_users_l border_users_b border_users_r" >
				<a href="vps.php?action=viewDetails&category=invoices&invoiceID={$invoiceList[i].invoiceID}"><div align="left">&nbsp;{$invoiceList[i].invoiceID}</div></a>
				</td>
				<td class="border_users_l border_users_b border_users_r" >
				<a href="vps.php?action=viewDetails&category=invoices&invoiceID={$invoiceList[i].invoiceID}"><div align="left">&nbsp;<b>{$invoiceList[i].items_included}</b></div></a>
				</td>
				<td class="border_users_r border_users_b">
				<a href="vps.php?action=viewDetails&category=invoices&invoiceID={$invoiceList[i].invoiceID}"><div align="left">&nbsp;{$currencies.$currencyID.sign} {$invoiceList[i].oneTimeCharge}</div></a>
				</td>
				<td class="border_users_r border_users_b">
				<a href="vps.php?action=viewDetails&category=invoices&invoiceID={$invoiceList[i].invoiceID}"><div align="left">&nbsp;{$currencies.$currencyID.sign} {$invoiceList[i].amount}</div></a>
				</td>
				<td class="border_users_r border_users_b">
				<a href="vps.php?action=viewDetails&category=invoices&invoiceID={$invoiceList[i].invoiceID}"><div align="left">&nbsp;{$currencies.$currencyID.sign} {$invoiceList[i].discount}</div></a>
				</td>
				<td class="border_users_r border_users_b">
				<a href="vps.php?action=viewDetails&category=invoices&invoiceID={$invoiceList[i].invoiceID}"><div align="left">&nbsp;{$currencies.$currencyID.sign} {$invoiceList[i].total}</div></a>
				</td>
				<td class="border_users_r border_users_b">
				<a href="vps.php?action=viewDetails&category=invoices&invoiceID={$invoiceList[i].invoiceID}"><div align="left">&nbsp;{$currencies.$currencyID.sign} {$invoiceList[i].paid}</div></a>
				</td>
				<td class="border_users_r border_users_b">
				<a href="vps.php?action=viewDetails&category=invoices&invoiceID={$invoiceList[i].invoiceID}"><div align="left">&nbsp;{$currencies.$currencyID.sign} {$invoiceList[i].due}</div></a>
				</td>
				<td class="border_users_r border_users_b">
				<a href="vps.php?action=viewDetails&category=invoices&invoiceID={$invoiceList[i].invoiceID}"><div align="left" >&nbsp;{$invoiceList[i].generationDate}</div></a>
				</td>
				<td class="border_users_r border_users_b">
				<a href="vps.php?action=viewDetails&category=invoices&invoiceID={$invoiceList[i].invoiceID}"><div align="left" >&nbsp;<b>{$invoiceList[i].suspensionDate}</b></div></a>
				</td>
				{*<td class="border_users_r border_users_b">
				<a href="vps.php?action=viewDetails&category=invoices&invoiceID={$invoiceList[i].invoiceID}"><div align="left">&nbsp;{$invoiceList[i].periodStartDate}</div></a>
				</td>
				<td class="border_users_r border_users_b">
				<a href="vps.php?action=viewDetails&category=invoices&invoiceID={$invoiceList[i].invoiceID}"><div align="left">&nbsp;{$invoiceList[i].periodEndDate}</div></a>
				</td>*}
				{if $currentBookmark eq "All"}	
				<td class="border_users_r border_users_b">
				<a href="vps.php?action=viewDetails&category=invoices&invoiceID={$invoiceList[i].invoiceID}"><div align="left" >&nbsp;{$invoiceList[i].status}</div></a>
				</td>
				{/if}
				{if $invoiceList[i].paypal}								
				<td align="right" class="border_users_r border_users_b">
					<div align="left">						
						<form action="https://www.sandbox.paypal.com/cgi-bin/webscr" method="post">	{*sandbox*}
						
							<input type="hidden" name="cmd" value="_xclick">							
							
							<input type="hidden" name="business" value="{$invoiceList[i].paypal.merchantEmail}">							
							<input type="hidden" name="item_name" value="{$invoiceList[i].paypal.itemName}">
							<input type="hidden" name="item_number" value="{$invoiceList[i].paypal.itemNumber}">
							<input type="hidden" name="amount" value="{$invoiceList[i].paypal.amount}">
							<input type="hidden" name="currency_code" value="{$invoiceList[i].paypal.currency_code}">
							<input type="hidden" name="no_shipping" value="{$invoiceList[i].paypal.noShipping}">
							<input type="hidden" name="no_note" value="{$invoiceList[i].paypal.noNote}">
							<input type="hidden" name="custom" value="{$userID}">
														
							<input type="hidden" name="return" value="{$invoiceList[i].paypal.returnURL}">
							<input type="hidden" name="notify_url" value="{$invoiceList[i].paypal.notifyURL}">
							<input type="hidden" name="cancel_return" value="{$invoiceList[i].paypal.cancelURL}">
																					
							<input type="image" src="https://www.paypal.com/en_US/i/btn/btn_paynowCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
							<img alt="" border="0" src="https://www.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1">
						</form>						
					</div>					
				</td>
				{/if}
				{if $currentBookmark eq "Due"}
				<td  class="border_users_r border_users_b" style="text-align:center;">
					{if isset($invoiceList[i].enablePayButton) && $invoiceList[i].enablePayButton neq "disabled"} 
					<input type="button" class="button" value="Pay!" onclick="window.location = 'vps.php?action=payInvoice&category=invoices&invoiceID={$invoiceList[i].invoiceID}'" 
							/>
					{else}
					&nbsp;
					{/if}
				</td>
				{/if}
			</tr>
		{/section}			
		{/if}
											
						
						
						
						<tr>
						                       <td  height="15" colspan="5" class="users_u_bottom">
											   </td><td class="border_users">
											   </td>
											     <td height="15" colspan="6" class="users_u_bottom_r">
											   </td>
						</tr>
			</table>

</body>
</html>
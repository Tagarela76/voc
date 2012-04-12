  {*shadow*}	
	  <table class="report_issue_green" cellspacing="0" cellpadding="0" align="center">
                          <tr>
                               <td valign="top" class="report_issue_top_green">
							   
                               </td>
							   </tr>
							   <tr>
                               <td valign="top" class="report_issue_center_green" align="center">
	 {**}
		<table width="440px" class="dashboard" cellspacing="0" cellpadding="0">
			<tr>
				<td colspan="3" class="dashboard">
					Invoice Stats
				</td>				
			</tr>
			<tr class="hov_vps">
				<td class="pcenter">
					<a href="{$viewListURL}&subCategory=All"><div>Payment History:</div></a>
				</td>
				<td class="pcenter" width="5%" align="center">
					<a href="{$viewListURL}&subCategory=All"><div>[{$allInvoices}]</div></a>
				</td>
				<td class="pcenter" align="center"><b><span>&nbsp;</b></span></td>
			</tr>
		
			<tr class="hov_vps">
				<td class="pcenter">
					<a href="{$viewListURL}&subCategory=Due"><div><span>Total Now Due:</span></div></a>
				</td>
				<td class="pcenter" width="5%" align="center">
					<a href="{$viewListURL}&subCategory=Due" ><div><span>[{$dueInvoices.count}]</span></div></a>
				</td>
				<td class="pcenter"align="center">
					<b><span>{$currentCurrency.sign} {$dueInvoices.total}</span></b>
				</td>
			</tr>
			<tr class="hov_vps">
				<td class="pcenter" colspan="2">
					<div>&nbsp;</div>
				</td>				
				<td class="pcenter"align="center">
					<b>&nbsp;<span></span></b>
				</td>
			</tr>
			<tr class="hov_vps">
				<td class="pcenter" colspan="2">
					<div>Balance:</div>
				</td>				
				<td class="pcenter"align="center">
					<b>{$balance}<span></span></b>
					
				</td>
			</tr>
			<tr class="hov_vps">
				<td class="pcenter" colspan="2">
					<div>&nbsp;</div>
				</td>				
				<td class="pcenter"align="center">
					<b>&nbsp;<span></span></b>
				</td>
			</tr>
			<tr class="hov_vps">
				<td class="pcenter" colspan="3" align="center">
					<div>Next Invoice for Billing period will be generated {$nextInvoiceDate}</div>
				</td>							
			</tr>
		</table>
		{*shadow*}	
					</td>
			   </tr>
				<tr>
                               
                     <td valign="top" class="report_issue_bottom_green">
                    </td>					
				</tr>
		</table>
		
		{**}	
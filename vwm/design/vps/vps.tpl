<html>
	<head>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8">
		<title>{$title}</title>
		<link href="style.css" rel="stylesheet" type="text/css">
	</head>
	
	<body>
	
				<table width="100%" height="100%"  cellspacing="0" cellpadding="0" >			  						  <tr>											
								<td style="height:30px;" class="bgtop" >	
         								 <table  align="right" class="cell1 logo_site_green" >
												<tr>
													<td class="toppdd">
														<table  align="right">
															<tr>
																<td></td>
																<td></td>
																<td></td>
															 </tr>
														</table>
													</td>
												</tr>
											</table>
										</td>
								  </tr>
								  <tr>										
										<td  valign="top">
								
											<table class="cell2"  cellspacing="0" cellpadding="0" height="100%" >
												<tr>
													<td  valign="top" align="center" >
													
															
																		<table width="100%" height="100%"   height="100%" cellspacing="0" cellpadding="0">
																			<tr>
																				{if !$newUserRegistration}
																					{include file="tpls:left_menu_vps.tpl"}
																				{/if}	
													
							
						
																				<td valign="top" class="foot_block">
{*Categories List*}

{include file="tpls:top.tpl"}
{if $category eq "dashboard"}
	                       	{include file="tpls:tpls/dashboard.tpl"}	
{elseif $category eq "invoices"}
	{if $currentBookmark eq "viewDetails"}
	                       	{include file="tpls:tpls/invoiceDetails.tpl"}
	{elseif $currentBookmark eq "payInvoice"}
							{include file="tpls:tpls/areYouSure.tpl"}
	{else}
	                       	{include file="tpls:tpls/invoices.tpl"}
	{/if}
{elseif $category eq "billing"}
	{if $currentBookmark eq "MyBillingPlan"}	
	                       	{include file="tpls:tpls/billingPlanDetails.tpl"}
	{elseif $currentBookmark eq "AvailableBillingPlans"}
							{include file="tpls:tpls/availableBillingPlans.tpl"}
	{elseif $currentBookmark eq "editCategory"}
							{include file="tpls:tpls/areYouSure.tpl"}
	
	{elseif $currentBookmark eq "contactAdmin"}
							{include file="tpls:tpls/contactAdmin.tpl"}
	{/if}
{elseif $category eq "myInfo"}	
	                       	{include file="tpls:tpls/userInfo.tpl"}	                       
{/if}



										</td>
										
									</tr>
									
								</table>
								
							</td>
							{include file="tpls:../user/footer.tpl"}	
						</tr>																
					</table>	
	
	</body>
</html>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
	<head>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8">
		<title>{$title}</title>
		<link href="style.css" rel="stylesheet" type="text/css">
		
	</head>
	<body>

		
								
								<table width="100%" height="100%"  cellspacing="0" cellpadding="0" >			  						  <tr>											
											{include file="tpls:logo.tpl"}											
										<td  valign="top">
{*table center*}										
											<table class="cell2"  cellspacing="0" cellpadding="0" height="100%" >
												<tr>
													<td  valign="top" align="center" >
													
															
																		<table width="100%" height="100%"   height="100%" cellspacing="0" cellpadding="0">
																			<tr>
																				<td>
																				{include file="tpls:tpls/categoriesListLeft.tpl"}	
																				</td>
						
																				<td valign="top"  class="foot_block">
{*Categories List*}
{include file="tpls:tpls/login_categoriesList.tpl"}

{*NOTABS*}
{if $bookmarkType == "areYouSure"}	
	{include file="tpls:tpls/vps/areYouSure.tpl"}
{elseif $bookmarkType == "areYouSureAdd"}	
	{include file="tpls:tpls/vps/areYouSureAdd.tpl"}
{elseif $bookmarkType == "definedBillingPlan"}
	{include file="tpls:tpls/vps/definedBillingPlan.tpl"}
{elseif $bookmarkType == "DBPRequests"}
	{include file="tpls:tpls/vps/DBPRequests.tpl"}
{elseif $bookmarkType == "areYouSureInvoice"}
	{include file="tpls:tpls/vps/areYouSureInvoice.tpl"}
{elseif $bookmarkType == "selectBillingPlan"}
	{include file="tpls:tpls/vps/selectBillingPlan.tpl"}
{else}	
	{include file="tpls:tpls/vps/bookmarksVPS.tpl"}
{/if}
{*/NOTABS*}

{*TABS*}
{if $bookmarkType == "billing"}
	{include file="tpls:tpls/vps/billing.tpl"}
{elseif $bookmarkType == "discounts"}	
	{include file="tpls:tpls/vps/discounts.tpl"}
{elseif $bookmarkType == "customers"}	
	{include file="tpls:tpls/vps/debtors.tpl"}
{elseif $bookmarkType == "other"}	
	{include file="tpls:tpls/vps/other.tpl"}
{/if}
{*/TABS*}
<br>
<br>
								
										</td>
										
									</tr>
									
								</table>
{*/table center*}								
							</td>
							{include file="tpls:footer.tpl"}	
						</tr>																
					</table>
					
			
	</body>
</html>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"> 
<html>
	<head>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8">
		<title>{$title}</title>
		<link href="style.css" rel="stylesheet" type="text/css">
		<script type="text/javascript" src="modules/js/checkBoxes.js"></script>		
	</head>
	<body>					
								
								<table width="100%" height="100%"  cellspacing="0" cellpadding="0" >			  						  <tr>											
											{include file="tpls:logo.tpl"}											
										<td  valign="top">
{table center}										
											<table class="cell2"  cellspacing="0" cellpadding="0" height="100%" >
												<tr>
													<td  valign="top" align="center" >
													
															
																		<table width="100%" height="100%"   height="100%" cellspacing="0" cellpadding="0">
																			<tr>
																				
																				{include file="tpls:tpls/categoriesListLeft.tpl"}	
																				
						
																				<td valign="top" >
{*Categories List*}
{include file="tpls:tpls/login_categoriesList.tpl"}
{*include file="tpls/controlCategoriesList.tpl"*}

{if $categoryID != "issue"}
	{include file="tpls:tpls/controlViewDetailsCategory.tpl"}
{/if}

{if $categoryID=="class"}
	{if $itemID eq "apmethod"}
		{include file="tpls:tpls/viewApmethod.tpl"}
	{elseif $itemID eq "coat"}
		{include file="tpls:tpls/viewCoat.tpl"}
	{elseif $itemID eq "density"}
		{include file="tpls:tpls/viewDensity.tpl"}
	{elseif $itemID eq "country"}
		{include file="tpls:tpls/viewCountry.tpl"}
	{elseif $itemID eq "substrate"}
		{include file="tpls:tpls/viewSubstrate.tpl"}
	{elseif $itemID eq "supplier"}
		{include file="tpls:tpls/viewSupplier.tpl"}
	{elseif $itemID eq "type"}
		{include file="tpls:tpls/viewType.tpl"}
	{elseif $itemID eq "unittype"}
		{include file="tpls:tpls/viewUnittype.tpl"}
	{elseif $itemID eq "msds"}
		{include file="tpls:tpls/viewMsds.tpl"}
	{elseif $itemID eq "lol"}
		{include file="tpls:tpls/viewLol.tpl"}
	{elseif $itemID eq "formulas"}
		{include file="tpls:tpls/viewFormulas.tpl"}
	{elseif $itemID eq "rule"}
		{include file="tpls:tpls/viewRule.tpl"}
	{elseif $itemID eq "components"}
		{include file="tpls:tpls/viewComponents.tpl"}
	{elseif $itemID eq "product"}
		{include file="tpls:tpls/viewProduct.tpl"}
	{elseif $itemID eq "agency"}
		{include file="tpls:tpls/viewAgency.tpl"}
	{elseif $itemID eq "emissionFactor"}
		{include file="tpls:carbon_footprint/design/viewEmissionFactor.tpl"}
	{/if}
{elseif $categoryID=="users"}
	{include file="tpls:tpls/viewUser.tpl"}
{elseif $categoryID eq "issue"}
	{include file="tpls:tpls/viewIssue.tpl"}
{/if}


											
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
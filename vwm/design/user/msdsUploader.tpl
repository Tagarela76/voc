<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
	<head>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8">
		<title>{$title}</title>
		<link href="style.css" rel="stylesheet" type="text/css">	
	</head>
	<body {if $step eq "edit"} onLoad="javascript:document.getElementById('saveButton').disabled = 1;"{/if}>
	

								
								<table width="100%" height="100%"  cellspacing="0" cellpadding="0" >			  						  <tr>											
											{include file="tpls:logo.tpl"}											
										<td  valign="top">
{*table center*}										
											<table class="cell2"  cellspacing="0" cellpadding="0" height="100%" >
												<tr>
													<td  valign="top" align="center" >
													
															
																		<table width="100%" height="100%"   height="100%" cellspacing="0" cellpadding="0">
																			<tr>
																				
																				{include file="tpls:tpls/categoriesListLeft.tpl"}	
																				
						
																				<td valign="top" class="foot_block">
{*Categories List*}
{include file="tpls:tpls/login_categoriesList.tpl"}
{if $step eq "main"}
	{if $basic eq "yes"}
		{include file="tpls:tpls/msdsUploaderBasic.tpl"}
	{else}
		{include file="tpls:tpls/msdsUploader.tpl"}
	{/if}
{elseif $step eq "assign"}
	{include file="tpls:tpls/msdsUploaderAssign.tpl"}
{elseif $step eq "edit"}
	{include file="tpls:tpls/msdsUploaderEdit.tpl"}
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
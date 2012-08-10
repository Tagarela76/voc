<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
	<head>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8">
		<title>{$title}</title>
		<link href="style.css" rel="stylesheet" type="text/css">

		<script type="text/javascript" src="modules/js/PopupWindow.js"></script>
		<script type="text/javascript" src="modules/js/checkBoxes.js"></script>
		<script type="text/javascript" src="modules/js/jquery-ui-1.8.2.custom/js/jquery-1.4.2.min.js"></script>
		<script type="text/javascript" src="modules/js/customizedRuleList.js"></script>
		<script type="text/javascript" src="modules/js/emailNotifications.js"></script>
		<script type="text/javascript" src="modules/js/settings.js"></script>
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


																				<td valign="top"  class="foot_block">
{*Categories List*}
{include file="tpls:tpls/login_categoriesList.tpl"}

	{include file="tpls:tpls/settings.tpl"}




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
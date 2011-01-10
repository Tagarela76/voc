<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
	<head>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8">
		<title>{$title}</title>
		<link href="style.css" rel="stylesheet" type="text/css">
		{literal}			
		
		<script type="text/javascript" src="modules/js/reg_country_state.js"></script>
		<script type="text/javascript" src="modules/js/checkBoxes.js"></script>
		<script type='text/javascript' src='modules/js/jquery-ui-1.8.2.custom/js/jquery-1.4.2.min.js'/>
		<SCRIPT language=JavaScript title="check">			

				function Colorize(Element, CBElement){
					if(document.getElementById) {
						if(Element && CBElement){
							Element.className = ( CBElement.checked ? 'selected' : 'default' );
						}
					}
				}

				function CheckRadioTR(Element){
					if(document.getElementById) {
						CheckTR(Element);
						thisTRs = Element.parentNode.getElementsByTagName('tr');
						for (i = 0; i < thisTRs.length; i++){
							if (thisTRs[i].id != Element.id && thisTRs[i].className != 'header') thisTRs[i].className = 'default';
						}
					}
				}

				function CheckTR(Element){
					if(document.getElementById) {
						thisCheckbox = document.getElementById(Element.id.replace('tr','cb'));
						thisCheckbox.checked = !thisCheckbox.checked;
						Colorize(Element, thisCheckbox);
					}
				}

				function CheckCB(Element){
					if(document.getElementById) {
						if(document.getElementById(Element.id.replace('cb','tr'))){Element.checked = !Element.checked;}
					}
				}
		</SCRIPT>			
		{/literal}

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
																				
															{include file="tpls:tpls/categoriesListLeft.tpl"}	
															<td valign="top" class="foot_block" >
																				
																	{*Categories List*}
																	{include file="tpls:tpls/login_categoriesList.tpl"}																	
																	{include file="tpls:tpls/reports.tpl"}
							
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
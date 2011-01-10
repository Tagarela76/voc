<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
	<head>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8">
		<title>Registration</title>
		<link href="style.css" rel="stylesheet" type="text/css">
		{literal}
			<SCRIPT language=JavaScript title="check">
			
				function CheckAll(Element,Name){
					if(document.getElementById) {
						thisCheckBoxes = Element.parentNode.parentNode.parentNode.getElementsByTagName('input');
						for (i = 1; i < thisCheckBoxes.length; i++){
							if (thisCheckBoxes[i].name == Name){
								thisCheckBoxes[i].checked = Element.checked;
								Colorize(document.getElementById(thisCheckBoxes[i].id.replace('cb','tr')), thisCheckBoxes[i]);
							}
						}
					}
				}

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
	
		<table class="cell1" align="center" >
		
			<tr>
				<td valign="top">
					<table   class="cell1" cellspacing="0" cellpadding="0" >
						<tr>
							<td align="center" >
								<table class="cell1 "  cellspacing="0" cellpadding="0" >
			  						
			  						<tr>	
										<td style="height:25px;" class="bgtop" >
											<table  align="right" class="cell1 logo" >
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
										<td  valign="top" >
											<table class="cell2 bg_center1"  cellspacing="0" cellpadding="0" >
												<tr>
													<td  valign="top" align="center" >
														<div  align="center">
															<table align="center" class="cell1 centermenu" cellspacing="0" cellpadding="0" height="500">
																<tr>
																	<td>
																		<table class="cell2">
																			<tr>
																				<td class="dotted_right lmenu_pdd bg_left" valign="top" width="160">
																					<h3 id="left">
																						Company name
																					</h3>
																					<div align="left" >
																						<ul class="link">
																							<li><a href ="left report.html">Faciality name</a></li>		 
																							<li><a href ="left report.html">Department name 1</a></li>		 
																							<li><a href ="left report.html">Department name 2</a></li>		 
																							<li><a href ="left report.html">Department name 3</a></li>		 
																							<li><a href ="left report.html">Department name 4</a></li>		 
																							<li><a href ="left report.html">Department name 5</a></li>		 
																						</ul>
																					</div>
																				</td>
						
																				<td valign="top">
																					<table class="padd_bot10 cell1" >
																						<tr>
																							<td style="padding-left:20px" class="dotted_bottom padd_bot10 topsmen">
																								<H3 STYLE="FONT-SIZE:14PX">
																									Registering for a new user
																								</H3>
																							</td>
																							<div  class="margintop10"style="margin-right:5px;">
																								<td  width="35"   class="dotted_bottom padd_bot10">
																									<span class="textbold " >
																										Help
																									</span>
																								</td>
																								<td width="70"  class="dotted_bottom padd_bot10">
																									<span class="textbold ">
																										Log info
																									</span>
																									<br>
																									<span class="textbold">
																										<a href="#" > Log out </a>
																									</span>
																								</td>
																							</div>
																						</tr>
																						
																						<tr>
																							<td colspan="3" valign="top">
																								<table class="cell1"><tr><td style="padding-left:10px;" >
																									<BR>
																									<BR>
																									
																									
																									</td><td>
							

		
</div>

</td></tr></table>
</td></tr></table>

{include file="tpls:reg_form.tpl"}			

													</td>
												</tr>
											</table>
										</td>
									</tr>
								</table>
							</td>
						</tr>
	  
						<tr>
							<td>
								<table  cellspacing="0" cellpadding="0" class="footer" width="100%" height="100%" >
									<tr>
										<td align="center">
											<b>G</b>yant Compliance&nbsp;&copy;&nbsp;2008
											<br>
											powered by KaTeT-Software
										</td>
									</tr>
								</table>
							</td>						
						</tr>
						
					</table>
				</td>
			</tr>
		</table>
	</body>
</html>
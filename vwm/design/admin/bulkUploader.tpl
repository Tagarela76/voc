<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
	<head>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8">
		<title>{$title}</title>
		<link href="style.css" rel="stylesheet" type="text/css">
		{literal}
					
		<script type="text/javascript" src="modules/js/reg_country_state.js"></script>
		<script type="text/javascript" src="modules/js/checkBoxes.js"></script>
			
			
			<SCRIPT language=JavaScript title="check">
							
				function Check(theBtn){

					//var matchInputFile = /.csv$/.test(form.maxNumber.value);
					var matchMaxNumber = /^[0-9]+$/.test(form.maxNumber.value);
					var matchThreshold = /^[0-9]+$/.test(form.threshold.value);

					if (matchMaxNumber && matchThreshold){
						if (form.threshold.value>=0 && form.threshold.value<=100){
							if (form.inputFile.value.substr(-3,3)=='csv') {
								form.submit();
							} else {
								document.getElementById('wait').innerHTML="Input file should be CSV format.";
								document.getElementById('wait').style.display="block";					
							}
						} else {
							document.getElementById('wait').innerHTML="Threshold should be between 0 and 100 %.";
							document.getElementById('wait').style.display="block";
						}
					} else {
						document.getElementById('wait').innerHTML="Check input data.";
						document.getElementById('wait').style.display="block";
					}		
				}
			
			</SCRIPT>

			
{/literal}

	</head>
	<body>
	

								
								<table width="100%" height="100%"  cellspacing="0" cellpadding="0"  >			  						  <tr>											
											{include file="tpls:logo.tpl"}											
										<td  valign="top">
{*table center*}										
											<table class="cell2"  cellspacing="0" cellpadding="0" height="100%" >
												<tr>
													<td  valign="top" align="center" >
													
															
																		<table width="100%" height="100%"   height="100%" cellspacing="0" cellpadding="0">
																			<tr>
																				
																				{include file="tpls:tpls/categoriesListLeft.tpl"}	
																				
						
																				<td valign="top"  class="foot_block">
{*Categories List*}
{include file="tpls:tpls/login_categoriesList.tpl"}
{if $validationResult}
	{include file="tpls:tpls/uploadResults.tpl"}
{else}
	{include file="tpls:tpls/bulkUploader.tpl"}
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
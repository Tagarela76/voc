<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html >
	<head>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8">
		<title>{$title}</title>
		
		<link href="style.css" rel="stylesheet" type="text/css">
		<link rel="shortcut icon" href="images/vocicon.ico" type="image/x-icon">		
		<script type="text/javascript" src='modules/js/jquery-ui-1.8.2.custom/js/jquery-1.4.2.min.js'></script>			
	</head>
	
	<body>			
		<table style='background:#373b47; width:100%;height:100%;'  cellspacing="0" cellpadding="0" >			  						  
			<tr >											
				<td style="height:25px;" >
				    <table align="right" class="cell1 logo">
				        <tr>
				            <td class="toppdd">
				                <table align="right">
				                    <tr>
				                        <td>
				                        </td>
				                        <td>
				                        </td>
				                        <td>
				                        </td>
				                    </tr>
				                </table>
				            </td>
				        </tr>
				    </table>
				</td>
			</tr>
			<tr>											
				<td  valign="top">												
					<table style='width:765px;background:#abadb1;' align='center'>		
						<tr >
							<td  colspan='2' height='150' bgcolor="#353a44">
								<h2 class="bigtext"style="color:#0090e1;" align='center'>
									Checking of rights for folders and files
								</h2>
							</td>				
						</tr>
						
						{section name=i loop=$folders}
							<tr>				
								<td class="border_users_l border_users_b" height="20">
									{if $folders[i].check  eq 1}
					 					<span class="ok">&nbsp;</span>
									{else}			
								 		<span class="error">&nbsp;</span>			
									{/if}
									{$folders[i].name}						
								</td>
								<td class="border_users_r border_users_b">
									<div align='right' style="padding-right:15">
									{if $folders[i].check  eq 1}							
										<h2  style="color:green;" >Ok<h2>
									{else}			 		
										<div style='background-color:White;height:50;width:500px;border-width:1px;border-style:groove;overflow-y:scroll; border-color:red;' align='left'>
											{$folders[i].error}
										</div>								
									{/if}
									</div>							
								</td>
							</tr>
						{/section}	
						<tr>
							<td colspan='2' height='50'>
								<div align="right" >
									<form method='POST' action=''>
										<input type='hidden' name='step' value='{$step}'>
										<input type='submit' class='button' value='Next >>'>
										<span style="padding-right:15;">&nbsp;</span>
									</form>								
								</div>
							</td>
						</tr>				
					</table>									
				</td>
			</tr>			
		</table>
	</body>	
</html>
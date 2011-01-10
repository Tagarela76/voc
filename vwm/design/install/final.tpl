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
									Installing is successfully completed
								</h2>
							</td>				
						</tr>
						
						{if $isAdminUser==false}
						<tr>				
							<td class="border_users_l border_users_b" height="35">
									Administrator login:					
							</td>
							<td class="border_users_r border_users_b border_users_l" >
								<div style='background-color:white; width:100px; height:18px; border-style:ridge; border-width:1px; border-color:red;' align='center'>root</div>			
							</td>
						</tr>
						<tr>				
							<td class="border_users_l border_users_b" height="35">
									Administrator password:					
							</td>
							<td class="border_users_r border_users_b border_users_l">						
								<div style='background-color:white; width:100px; height:18px; border-style:ridge; border-width:1px; border-color:red;' align='center'>{$admPassword}</div>		
							</td>
						</tr>
						{/if}
						
						<tr>				
							<td class="border_users_l border_users_b" height="35">
									Web address for Woc Web Manager:					
							</td>
							<td class="border_users_r border_users_b border_users_l">
								<a href='{$address}'>{$address}</a>				
							</td>
						</tr>
						<tr>				
							<td class="border_users_l border_users_b" height="35">
									Web address for admin.php:					
							</td>
							<td class="border_users_r border_users_b border_users_l">
								<a href='{$address}vwm/admin.php'>{$address}vwm/admin.php</a>				
							</td>
						</tr>									
							
					</table>										
				</td>
			</tr>			
		</table>
	</body>	
</html>

{literal}
<script type='text/javascript'>
	var thisDomain=document.domain;
</script>
{/literal}
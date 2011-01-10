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
					<table style='width:765px;background:#abadb1;' align="center">		
						<tr >
							<td  colspan='2' height='150' bgcolor="#353a44">
								<h2 class="bigtext"style="color:#0090e1;" align='center'>
									Module Installer: Confirm the installation of modules
								</h2>
							</td>				
						</tr>
						
								<tr>				
									<td class="border_users_l border_users_b" height="20">
										&nbsp;						
									</td>
								</tr>
		
						
						{if $validation|@count > 0}
							<tr >
								<td  colspan='2' height='20' bgcolor="#353a44">
									<h3 class="bigtext"style="color:#0090e1;" align='center'>
										Next modules can't be installed because of errors:
									</h3>
								</td>				
							</tr>
						
							{foreach from=$validation item=errors key=module}
								<tr>				
									<td class="border_users_l border_users_b" height="20">
										<h4>
											{$module}	
										</h4>					
									</td>
								</tr>
								{foreach from=$errors item=error}
									<tr>				
										<td class="border_users_l border_users_b" height="20" style='padding-left:15px;'>
												{$error}					
										</td>
									</tr>
								{/foreach}
							{/foreach}	
							
							<tr >
								<td  colspan='2' height='20' bgcolor="#353a44">
									<h3 class="bigtext"style="color:#0090e1;" align='center'>
										If you want to install this modules please correct errors.
									</h3>
								</td>				
							</tr>
						{/if}
		
						
						{if $modules2install|@count > 0}
											
							<tr >
								<td  colspan='2' height='20' bgcolor="#353a44">
									<h3 class="bigtext"style="color:#0090e1;" align='center'>
										Are you sure you want to install next modules?
									</h3>
								</td>				
							</tr>
							
							<form name='installModules' method='post'>
								{foreach from=$modules2install item=module}
									<tr>			
										<td class="border_users_l border_users_b" height="20">
											{$module}						
										</td>
									</tr>
								{/foreach}	
								<tr>
									<td colspan='2' height='50'>
										<div align="right">
											
											<input type='hidden' name='step' value='installConfirmed' />
											<input type='hidden' name='modules' value='{$modules}' />
											<input type='button' class='button'  value='<< Back' onclick='location.href="modules_install.php"' />	
											<input type='submit' class='button'  value='Yes' />			
											<span style="padding-right:15;">&nbsp;</span>
											
										</div>
									</td>
								</tr>	
							</form>
						{else}
							<tr>				
								<td colspan='2' height="50">
									<div align="right">
										<input type='button' class='button'  value='<< Back' onclick='location.href="modules_install.php"' />	
									</div>				
								</td>
							</tr>	
						{/if}		
					</table>											
				</td>
			</tr>			
		</table>
	</body>	
</html>
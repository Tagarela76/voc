<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html >
	<head>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8">
		<title>{$title}</title>
		
		<link href="style.css" rel="stylesheet" type="text/css">
		<link rel="shortcut icon" href="images/vocicon.ico" type="image/x-icon">		
		<script type="text/javascript" src='modules/js/checkBoxes.js'></script>			
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
									Module Installer: Choose modules to install
								</h2>
							</td>				
						</tr>
						
								<tr>				
									<td colspan='2' class="border_users_l border_users_b" height="20">
										&nbsp;						
									</td>
								</tr>
						
						<tr >
							<td  colspan='2' height='20' bgcolor="#353a44">
								<h3 style="color:#0090e1;font-size:20;" >
									To install module put module's folder to directory ../extensions/
								</h3>
							</td>				
						</tr>
						
								<tr>				
									<td colspan='2' class="border_users_l border_users_b" height="20">
										&nbsp;						
									</td>
								</tr>
						
						<tr >
							<td  colspan='2' height='20' bgcolor="#353a44">
								<h3 class="bigtext"style="color:#0090e1;" align='center'>
									Already installed modules:
								</h3>
							</td>				
						</tr>
		
						{if $installedModules|@count > 0}
							{foreach from=$installedModules item=module}
								<tr>				
									<td colspan='2' class="border_users_l border_users_b" height="20">
										{$module}						
									</td>
								</tr>
							{/foreach}	
						{else}
							<tr>				
								<td colspan='2' class="border_users_l border_users_b" height="20">
									No modules in the list						
								</td>
							</tr>	
						{/if}
						
						<tr >
						{if $modules2install|@count > 0}
							<td class="control_list" style="width:130px" bgcolor="#353a44">
								<span style='display:inline-block;'>
									Select: 
									<a onclick="CheckAll(this)" class="id_company1" style="color:grey;text-decoration:underline;" >All</a>									
									 /
									<a onclick="unCheckAll(this)" class="id_company1" style="color:grey;text-decoration:underline;" >None</a>
								</span>
							</td>
						{/if}
							<td  colspan='2' height='20' bgcolor="#353a44">
								<h3 class="bigtext" style="color:#0090e1;" align='center'>
									Modules available to install:
								</h3>
							</td>				
						</tr>
						
						{if $modules2install|@count > 0}
							<form name='installModules' method='post'>
								{foreach from=$modules2install item=module}
									<tr>
										<td class="border_users_l border_users_b" height="20" width="40">
											<input type='checkbox' value="{$module}" name="modulesToInstall[]">						
										</td>				
										<td class="border_users_l border_users_b" height="20">
											{$module}						
										</td>
									</tr>
								{/foreach}	
								<tr>
									<td colspan='2' height='50'>
										<div align="right">
											<input type='hidden' name='step' value='install'>
											<input type='button' class='button'  value='<< Back' onclick='location.href="admin.php?action=browseCategory&categoryID=modulars"'/>	
											<input type='submit' class='button'  value='Yes' />			
											<span style="padding-right:15;">&nbsp;</span>
											
										</div>
									</td>
								</tr>	
							</form>
						{else}
							<tr>				
								<td colspan='2' class="border_users_l border_users_b" height="20">
									No modules in the list						
								</td>
							</tr>	
							<tr>
								<td colspan='2' height='50'>
									<div align="right">
											<input type='button' class='button'  value='<< Back' onclick='location.href="admin.php?action=browseCategory&categoryID=modulars"'/>	
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
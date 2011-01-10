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
					<form method='post'>
						<input type='hidden' name='step' value='3'>
						<input type='hidden' name='form' value='isForm'>				
						<table style='width:765px;background:#abadb1;'align="center">		
							<tr >
								<td  colspan='2' height='150' bgcolor="#353a44">
									<h2 class="bigtext"style="color:#0090e1;" align='center'>
										Install settings 
									</h2>
								</td>				
							</tr>
							
							{if $connectingFail}
								<tr>				
									<td class="border_users_l border_users_b border_users_r" colspan='2'>
										{*ERROR*}					
		                        		<div class="error_img"><span class="error_text">Connecting error!</span></div>
									    {*/ERROR*}					
									</td>							
								</tr>
							{/if}
							<tr>				
								<td class="border_users_l border_users_b" height="35">
									Host DB:					
								</td>
								<td class="border_users_r border_users_b border_users_l">
									<input name='hostdb' type='text' value='{$data.hostdb}'>						
								</td>
							</tr>
							
							<tr>				
								<td class="border_users_l border_users_b" height="35">
									Login:					
								</td>
								<td class="border_users_r border_users_b border_users_l">
									<input name='login' type='text' value='{$data.login}'>						
								</td>
							</tr>
							
							<tr>				
								<td class="border_users_l border_users_b" height="35">
									Password:					
								</td>
								<td class="border_users_r border_users_b border_users_l">
									<input name='pwd' type='password' value='{$data.pwd}'>						
								</td>
							</tr>					
							<tr>				
								<td class="border_users_l border_users_b" height="35">
									Name of DB:					
								</td>
								<td class="border_users_r border_users_b border_users_l">
									{if $dbAlreadyExist==true}
									{*ERROR*}					
		                        		<div class="error_img"><span class="error_text">DB with this name already exist!</span></div><br><br>
									{*/ERROR*}
									{/if}	
									{if $dbNoExist==true}
									{*ERROR*}					
		                        		<div class="error_img"><span class="error_text">DB with this name isn't exist!</span></div><br><br>
									{*/ERROR*}
									{/if}	
									<input type='radio' name='dbCombo' value='newDB' {if $data.dbCombo=='newDB'} checked='true'{/if}> To create a new database?<br>
									<input type='radio' name='dbCombo' value='oldDB' {if $data.dbCombo=='oldDB'} checked='true'{/if}> To use the already created database?<br>
									{* <div id='tbls'>
										&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type='checkbox' name='isTables' value='isTables' > To use the already created tables?<br>
									</div> *}
									<input name='namedb' type='text' value='{$data.namedb}'>																		
								</td>
							</tr>
							<tr>				
								<td class="border_users_l border_users_b" height="35">
									Region:					
								</td>
								<td class="border_users_r border_users_b border_users_l">
									<select name='region' style='background-color:White'>
										<option value='us' {if $data.region eq 'us'}selected='selected'{/if}>USA</option>
										<option value='eu_uk' {if $data.region eq 'eu_uk'}selected='selected'{/if}>United Kingdom</option>
									</select>						
								</td>
							</tr>
							
							<tr>
								<td colspan='2' height='50'>
									<div align="right">
										
										<input type='submit' name='next' class='button' value='Install'/>			
										<span style="padding-right:15;">&nbsp;</span>
										
									</div>
								</td>
							</tr>				
						</table>				
					</form>											
				</td>
			</tr>			
		</table>
	</body>	
</html>

	

{*  {literal}
<script type='text/javascript'>
	function setCheckbox()
	{		
		$('input:radio[name=dbCombo]').each(function (el)
		{
			if($(this).attr('value')=='oldDB')
			{	
				if ($(this).attr('checked')==true)
				{
					$('input:checkbox[name=isTables]').attr('disabled',false);
					$('#tbls').css('display','block');
				}
			}
			else
			{
				if ($(this).attr('checked')==true)
				{
					$('input:checkbox[name=isTables]').attr('disabled',true);
					$('#tbls').css('display','none');
				}
			}
		});						
	}
	
	$(function()
	{
		setCheckbox();
		$('input:radio[name=dbCombo]').click(function()
		{
			setCheckbox();
		});					
	});	
	
</script>
{/literal}*}
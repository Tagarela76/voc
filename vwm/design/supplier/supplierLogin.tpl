<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>

<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<title>voc web manager: SI</title>
<link href="style.css" rel="stylesheet" type="text/css">
{literal}
<script type="text/javascript">		
	function iniEverything() {
		setfocus();
		
		var getVars = getUrlVars();
		var errorDiv = document.getElementById('error');
		if (getVars['error'] === 'auth') {
			errorDiv.innerHTML = 'Wrong login or password! Please, try again';
			document.getElementById('error').style.display = 'block';
		} else if (getVars['error'] === 'timeout') {
			errorDiv.innerHTML = 'Timeout. Please, log in again';
			document.getElementById('error').style.display = 'block';			
		} else {
			document.getElementById('error').style.display = 'none';
		}
		 
	}
	
	function setfocus() {
        loginForm.accessname.focus()
    }
	
	//	map[getVar] = getVarValue
	function getUrlVars() {
		var map = {};
		var parts = window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi, function(m,key,value) {
			map[key] = value;
		});
		return map;
}
</script>
{/literal}
</head>

<body onload="iniEverything();">
               {*shadow_table*}	
	             <table class="report_uploader" cellspacing="0" cellpadding="0" align="center" >
                         <tr>
                               <td valign="top" class="report_uploader_t_l"></td>
                               <td valign="top" class="report_uploader_t"></td>
                               <td valign="top" class="report_uploader_t_r"></td>
						</tr>
						  <tr>
							   <td valign="top" class="report_uploader_l"></td>
                               <td valign="top" class="report_uploader_c">
	           {*shadow_table*}

<h1 class="authorization_green">Authorization SupI</h1>
<form method='post' name="loginForm" action='supplier.php'>
<input type='text' name='accessname' class="report_uploader">
<br>
<br>
<input type='password' name='password' class="report_uploader">
<br>
<div id="error" name="error" style="display:none;"></div>
<br>
<div class="floatright"><input type='submit' name='action' value='auth' class="button"></div>
</form>
	          {*/shadow_table*}	
					         </td>
					          <td valign="top" class="report_uploader_r"></td>
			           </tr>
				        <tr>          
                             <td valign="top" class="report_uploader_b_l"></td>
                             <td valign="top" class="report_uploader_b"></td>
                             <td valign="top" class="report_uploader_b_r"></td>                           				
				       </tr>
		      </table>
		
		      {*/shadow_table*}	
	
	</body>
</html>	
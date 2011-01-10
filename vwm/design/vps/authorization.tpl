<html>
<head>
	<title>VOC Payment System</title>
	<link href="style.css" rel="stylesheet" type="text/css">
</head>

<body>
	            {*shadow_table*}	
	             <table class="report_uploader" cellspacing="0" cellpadding="0" align="center" >
                         <tr>
                               <td valign="top" class="report_uploader_t_l_yellowgreen"></td>
                               <td valign="top" class="report_uploader_t_yellowgreen"></td>
                               <td valign="top" class="report_uploader_t_r_yellowgreen"></td>
						</tr>
						  <tr>
							   <td valign="top" class="report_uploader_l_yellowgreen"></td>
                               <td valign="top" class="report_uploader_c_yellowgreen">
                               	<h1 class="authorization_green">Authorization VPS</h1>
	           {*shadow_table*}
	<form action="vps.php" method="POST">
			<input type="hidden" name="backUrl" value="{$backUrl}" />
		{if $status eq "fail"}
			<div style="color:red"> Athorization fail. Please try again. </div><br>
		{elseif $status eq "userAdded"}
			<div style="color:red"> New user added.</div><br>
		{/if}		
		

			<div>
				<div style="width:80px;float:left;">Username</div>
				<input type="text" name="username">
			</div>
		    <div>
		    	<div style="width:80px;float:left;">Password</div> 
				<input type="password" name="password">
			</div>
	
		
		<input type="submit" name="action" value="auth" style="float:right;margin-top:5px"  class="button">
	</form>
	
		          {*/shadow_table*}	
					         </td>
					          <td valign="top" class="report_uploader_r_yellowgreen"></td>
			           </tr>
				        <tr>          
                             <td valign="top" class="report_uploader_b_l_yellowgreen"></td>
                             <td valign="top" class="report_uploader_b_yellowgreen"></td>
                             <td valign="top" class="report_uploader_b_r_yellowgreen"></td>                           				
				       </tr>
		      </table>
		
		      {*/shadow_table*}	
</body>
</html>
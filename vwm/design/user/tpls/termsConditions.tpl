<html style="background:#4C505B">
	<head>
		<title>VOC-WEB-MANAGER: Terms and Conditions</title>
		<link href="style.css" rel="stylesheet" type="text/css">
		<link rel="shortcut icon" href="images/vocicon.ico" type="image/x-icon">
	</head>
	<body  style="background:#4C505B">
		<form enctype="multipart/form-data" method="POST" name="termsConditions"
		{if isset($facilityID)} action="?action=browseCategory&category=facility&id={$facilityID}&bookmark=department"{/if}
	{if isset($companyID)}  action="?action=browseCategory&category=company&id={$companyID}"{/if}>
	<table width="100%" align="center" style="color: white;">
		<tr>
			<td colspan="2" align="center">
				<b class="errors_list">Terms and Conditions</b><br/>
				<a href="../Terms_and_Conditions.pdf">Save "Terms and Conditions.pdf"</a><br/><br/>
			</td>
			<td>
			</td>
		</tr>
		<tr>
			<td colspan="2" align="center" width="100%">
				<iframe src="../Terms_and_Conditions.html" width="650px" height="450px" style="background-color: white;" marginwidth="30px" marginheight="30px">
				</iframe>
			</td>
			<td>
			</td>
		</tr>
		<tr>
			<td align="right" width="50%" style="padding-right: 5px;">
				<br/><input type="submit" class="button" name="agree" value="Agree"/>
			</td>
			<td align="left" width="50%" style="padding-left: 5px;">
				<br/><input type="button" class="button" value="Cancel" onclick="functionCancel();"/>
			</td>
		</tr>
	</table>
</form>
</body>
{literal}
	<script>
		function functionCancel(){
			location = "../voc_web_manager.html";
		}
	</script>
{/literal}
</html>

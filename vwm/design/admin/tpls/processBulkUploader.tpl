{*shadow_table*}	
<table class="" cellspacing="0" cellpadding="0" align="center" >
	<tr>
		<td valign="top" class="report_uploader_t_l"></td>
		<td valign="top" class="report_uploader_t"></td>
		<td valign="top" class="report_uploader_t_r"></td>
	</tr>
	<tr>
		<td valign="top" class="report_uploader_l"></td>
		<td valign="top" class="report_uploader_c">
			{*shadow_table*}

	<center><h1><b>Process Uploader Settings</b></h1></center>

	<form name="form" enctype="multipart/form-data" action="admin.php?action=browseCategoryProcessNew&category=bulkUploader" method="post">
		<input type="hidden" name="MAX_FILE_SIZE" value="1000000" />
		<table>		
			<tr>				
				<td>Input file</td><td><input name="inputFile" type="file"><br>
			</tr>		
			<tr>
				<td>FacilityId</td><td><input name="facilityID" type="text" value="" id='facilityID'></td>
			</tr>
			<!--<tr>
				<td>Work Order Id</td><td><input name="woID" type="text" value="" id='woID'></td>
			</tr>
			<tr>
				<td>Update items</td><td><input name="update" type="checkbox" value="update" checked></td>
			</tr>-->
			<tr>
				<td></td><td><div id="wait" style="none"></div></td>
			</tr>
			<tr>
				<td></td>
				<td>
					<input type="button" value="Start" onClick="Check()"/>
				</td>
			</tr>
		</table>
		{*<input type='hidden' name='action' value='bulkUpload'>
		<input type='hidden' name='categoryID' value='bulkUploader'>*}
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

{literal}
			<SCRIPT language=JavaScript title="check">
							
				function Check(){
					var facilityId, woID;
					
					facilityId = /^[0-9]+$/.test($('#facilityID').val());
					/*woID = /^[0-9, '']+$/.test($('#woID').val());
						
						if($('#woID').val()==''){
							woID = true;
							}*/
					
					if (facilityId){
						
							if (form.inputFile.value.substr(-3,3)=='csv') {
								form.submit();
							} else {
								document.getElementById('wait').innerHTML="Input file should be CSV format.";
								document.getElementById('wait').style.display="block";					
							}
					} else {
						document.getElementById('wait').innerHTML="Check input data.";
						document.getElementById('wait').style.display="block";
					}		
				}
			
			</SCRIPT>
{/literal}
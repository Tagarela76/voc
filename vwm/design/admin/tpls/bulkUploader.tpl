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
			   
<center><h1><b>VOC Bulk Uploader Settings</b></h1></center>

<form name="form" enctype="multipart/form-data" action="admin.php?action=upload&category=bulkUploader" method="post">
	<input type="hidden" name="MAX_FILE_SIZE" value="1000000" />
	<table>		
		<tr>				
			<td>Input file</td><td><input name="inputFile" type="file"><br>
				<a href="samples/VOC_Bulk_Uploader_input_file_sample.html" target="_blank" class="report_uploader">sample 1</a><br>
				<a href="samples/VOC_Bulk_Uploader_input_file_sample2.html" target="_blank"class="report_uploader">sample 2</a><br>
				<a href="samples/BulkUploaderSample.xls" target="_blank"class="report_uploader">xls sample</a></td>			
		</tr>		
		<tr>
			<td>Max number of products</td><td><input name="maxNumber" type="text" value="10000"></td>
		</tr>
		<tr>
			<td>Error threshold</td><td><input name="threshold" type="text" value="20">%</td>
		</tr>
		<tr>
			<td>Update items</td><td><input name="update" type="checkbox" value="update" checked></td>
		</tr>
		<tr>
			<td></td><td><div id="wait" style="none"></div></td>
		</tr>
		<tr>
			<td>Products of company</td>
			<td>
				<select name="companyID">
					{section name=i loop=$companyList}
					<option value="{$companyList[i].id}" {if $companyList[i].id == $currentCompany} selected {/if}>{$companyList[i].name}</option>
					{/section}
				</select>
			</td>
		</tr>		
		<tr>
			<td></td><td><input type="button" value="Start" onClick="Check()">  <input type="submit" value="Startpfp" name="pfp"></td>
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
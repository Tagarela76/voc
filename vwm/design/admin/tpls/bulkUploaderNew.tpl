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

	<center><h1>{$uploaderName} &mdash; Bulk Uploader</h1></center>

	<form name="form" enctype="multipart/form-data" method="post">
		<input type="hidden" name="MAX_FILE_SIZE" value="1000000" />
		<table>		
			<tr>				
				<td>Input file</td><td><input name="inputFile" type="file"></td>			
			</tr>					
			<tr>
				<td>Update items</td><td><input name="update" type="checkbox" value="update" checked></td>
			</tr>						
			<tr>				
				<td>
					<input class="button" type="submit" value="Start"/>					
				</td>
			</tr>
		</table>		
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
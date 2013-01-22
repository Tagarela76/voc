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
				<td>Company</td>
				<td>
					<select id="selectCompany" name="facilityID" onchange="getFacilityList()">
						{foreach from=$companyList item=company}
							<option value='{$company.id}'>
								{$company.name}
							</option>
						{/foreach}
					</select>
				</td>
			</tr>
			<tr>
				<td>Facility</td>
				<td>
					<select id="selectFacility" name="facilityID">
						{if isset($facilityList)}
							{foreach from=$facilityList item=facility}
								<option value='{$facility.facility_id}'>
									{$facility.name}
								</option>
							{/foreach}
						{/if}
					</select>
					<div id='facError' class="error_text" style="display:none">Error</div>
				</td>
			</tr>
			<tr>
				<td></td><td><div id="wait" style="none"></div></td>
			</tr>
			<tr>
				<td></td>
				<td>
					<input type="submit" value="Start" id='saveButton'/>
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


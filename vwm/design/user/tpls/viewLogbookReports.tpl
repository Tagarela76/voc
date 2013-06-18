{*shadow_table*}
<table cellspacing="0" cellpadding="0" align="center" width="90%">
    <tr>
        <td valign="top" class="report_uploader_t_l">
        </td>
        <td valign="top" class="report_uploader_t">
        </td>
        <td valign="top" class="report_uploader_t_r">
        </td>
    </tr>
    <tr>
        <td valign="top" class="report_uploader_l">
        </td>
        <td valign="top" class="report_uploader_c">
            {*shadow_table*}
			<h1 class="titleinfo"><b>Choose Logbook Report Type</b></h1>

            <table cellspacing="0" cellpadding="0" class="choose_report_type" width="100%">
                    <tr class="choose_report_type">
            			<td>
            				<a style="color: black" href="?action=sendLogbookReport&reportType=Logbook&category=logbookReports&id={$facilityId}">Logbook Report&nbsp;</a>
            			</td>
            			<td>
                        	Logbook Report
                   	 	</td>
                	</tr>
                    <tr class="choose_report_type">
                        <td>
                        </td>
                        <td align='right'>
                            <input class="button" align="right" type="button" name="goBack" value="Back" onclick="location.href='?action=browseCategory&category=facility&id={$facilityId}&bookmark=logbook'">
                        </td>
                	</tr>

            </table>
            <br>
            <br>
            {*/shadow_table*}
        </td>
        <td valign="top" class="report_uploader_r">
        </td>
    </tr>
    <tr>
        <td valign="top" class="report_uploader_b_l">
        </td>
        <td valign="top" class="report_uploader_b">
        </td>
        <td valign="top" class="report_uploader_b_r">
        </td>
    </tr>
</table>
{*/shadow_table*}

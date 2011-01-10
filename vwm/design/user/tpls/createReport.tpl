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
			<h1 class="titleinfo"><b>Choose Report Type</b></h1>
			
            <table cellspacing="0" cellpadding="0" class="choose_report_type" width="100%">
                <tr>
                    <td>
                        {*<a style="color: black" href="?action=sendReport&reportType=productQuants&itemID={$itemID}&id={$id}">Product List</a>*}
						<a style="color: black" href="?action=sendReport&reportType=productQuants&category={$request.category}&id={$request.id}">Product List</a>
                    </td>
                    <td>
                        Report description 
                    </td>
                </tr>
                <tr>
                    <td>
                        {*<a style="color: black" href="?action=sendReport&reportType=toxicCompounds&itemID={$itemID}&id={$id}">Toxic Compounds</a>*}
						<a style="color: black" href="?action=sendReport&reportType=toxicCompounds&category={$request.category}&id={$request.id}">Toxic Compounds</a>						
                    </td>
                    <td>
                        Report description
                    </td>
                </tr>
                <tr>
                    <td>
                        {*<a style="color: black" href="?action=sendReport&reportType=vocLogs&itemID={$itemID}&id={$id}">Daily Emissions</a>*}
						<a style="color: black" href="?action=sendReport&reportType=vocLogs&category={$request.category}&id={$request.id}">Daily Emissions</a>
                    </td>
                    <td>
                        Report description
                    </td>
                </tr>
                <tr>
                    <td>
                        {*<a style="color: black" href="?action=sendReport&reportType=mixQuantRule&itemID={$itemID}&id={$id}">Product Usage by Rule Summary</a>*}
						<a style="color: black" href="?action=sendReport&reportType=mixQuantRule&category={$request.category}&id={$request.id}">Product Usage by Rule Summary</a>
                    </td>
                    <td>
                        Report description
                    </td>
                </tr>
                <tr>
                    <td>
                        {*<a style="color: black" href="?action=sendReport&reportType=chemClass&itemID={$itemID}&id={$id}">Chemical Classification Summary Form</a>*}
						<a style="color: black" href="?action=sendReport&reportType=chemClass&category={$request.category}&id={$request.id}">Chemical Classification Summary Form</a>
                    </td>
                    <td>
                        Report description
                    </td>
                </tr>
                <tr>
                    <td>
                        {*<a style="color: black" href="?action=sendReport&reportType=exemptCoat&itemID={$itemID}&id={$id}">Exempt Coating Operations</a>*}
						<a style="color: black" href="?action=sendReport&reportType=exemptCoat&category={$request.category}&id={$request.id}">Exempt Coating Operations</a>
                    </td>
                    <td>
                        Report description
                    </td>
                </tr>
                <tr>
                    <td>
                        {*<a style="color: black" href="?action=sendReport&reportType=projectCoat&itemID={$itemID}&id={$id}">Project Coating Report</a>*}
						<a style="color: black" href="?action=sendReport&reportType=projectCoat&category={$request.category}&id={$request.id}">Project Coating Report</a>
                    </td>
                    <td>
                        Report description
                    </td>
                </tr>
                <tr class="choose_report_type">
                    <td>
                        {*<a style="color: black" href="?action=sendReport&reportType=VOCbyRules&itemID={$itemID}&id={$id}">VOC by Rules</a>*}
						<a style="color: black" href="?action=sendReport&reportType=VOCbyRules&category={$request.category}&id={$request.id}">VOC Summary for each Rule</a>&nbsp;<span style="color:red">New!</span>
                    </td>
                    <td>
                        Report description
                    </td>
                </tr>
                <tr class="choose_report_type">
                    <td>
                        {*<a style="color: black" href="?action=sendReport&reportType=SummVOC&itemID={$itemID}&id={$id}">Monthly Summary Report of total VOC usage</a>*}
                        <a style="color: black" href="?action=sendReport&reportType=SummVOC&category={$request.category}&id={$request.id}">Monthly VOC summary total</a>&nbsp;<span style="color:red">New!</span>
                    </td>
                    <td>
                        Report description
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
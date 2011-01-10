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
            	{foreach from=$reports item=name key=type}
            		<tr class="choose_report_type">
            			<td>
            				<a style="color: black" href="?action=sendReport&reportType={$type}&category={$request.category}&id={$request.id}">{$name}&nbsp;</a>
            			</td>
            			<td>
                        	Report description 
                   	 	</td>
                	</tr>
            	{/foreach}                
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
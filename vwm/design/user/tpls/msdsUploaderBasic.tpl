<div align="center">
    Back to <a style="color:black" href="?action=msdsUploader&step=main&itemID={$request.category}&id={$request.id}&basic=no">main uploader</a>.
</div>
{*shadow_table*} 
<table class="" cellspacing="0" cellpadding="0" align="center">
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
        <td valign="top" class="report_uploader_c_msds">
            {*shadow_table*}
            <center>
                <h1><b>MSDS UPLOADER</b></h1>
            </center>
            MSDS sheets will be assigned to products by name. Sample: "17-033-A.pdf" = product 17-033-A. 
            <form name="form" enctype="multipart/form-data" action="?action=msdsUploaderBasic&itemID={$request.category}&id={$request.id}" method="post">
                <input type="hidden" name="MAX_FILE_SIZE" value="52430000" />
                <table>
                    {section name=i loop=5} 
                    <tr>
                        <td>
                            {$smarty.section.i.index+1}.
                        </td>
                        <td>
                            <div id="div_input_{$smarty.section.i.index}">
                                <input id="input_{$smarty.section.i.index}" name="inputFile[]" type="file" onChange="fileSelected(this)">
                            </div>
                        </td>
                        <td>
                            <input id="clear_input_{$smarty.section.i.index}" type="button" class="button" value="Clear" disabled onClick="clearInput(this)">
                        </td>
                    </tr>
					{/section}
                    <tr>
                        <td colspan="2">
                        </td>
                        <td>
                            <input type="submit" class="button" value="Upload">
                        </td>
                    </tr>
                </table>
				{*<input type='hidden' name='action' value='msdsUploaderBasic'>
				<input type='hidden' name='itemID' value='{$request.category}'>
				<input type='hidden' name='id' value={$request.id}>*}
            </form>
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
<div align="center">
    Back to <a style="color:black" href="?action=msdsUploader&step=main&itemID={$request.category}&id={$request.id}&basic=no">main uploader</a>.
</div>
{literal}
<script>
    function fileSelected(element){
        document.getElementById("clear_" + element.id).disabled = 0;
    }
    
    function clearInput(element){
        inputId = element.id.substring(6, element.id.length);
        document.getElementById("div_" + inputId).innerHTML = document.getElementById("div_" + inputId).innerHTML;
        element.disabled = 1;
    }
</script>
{/literal} 
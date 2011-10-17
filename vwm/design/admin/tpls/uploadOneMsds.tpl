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
			{if $error}<div style="color:red">{$error}</div>{/if}
            Upload MSDS file to product {$productDetails.product_nr}
            <form name="form" enctype="multipart/form-data" action="?action=uploadOneMsds&category=tables&productID={$productDetails.product_id}" method="post">
                <input type="hidden" name="MAX_FILE_SIZE" value="52430000" />
                <table>                    
                    <tr>                        
                        <td>
                            <div>
                                <input id="input" name="inputFile[]" type="file">
                            </div>
                        </td>                        
                    </tr>
                   <tr>                        
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

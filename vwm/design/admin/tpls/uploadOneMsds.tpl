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
                <h1><b>ATTACHMENT UPLOADER</b></h1>
            </center>
			{if $error}<div style="color:red">{$error}</div>{/if}
			Upload <b id="typeOfFile">MSDS</b> file to product {$productDetails.product_nr}
            <form name="form" enctype="multipart/form-data" action="{$formActionUrl}" method="post">
                <input type="hidden" name="MAX_FILE_SIZE" value="52430000" />
                <table>
					<tr>
						<td>
							<input type="radio" name="fileType[]" value="msds" checked onclick="$('#typeOfFile').html('MSDS');"/>&nbsp;MSDS File<br/>
						</td>
						<td>
							<input type="radio" name="fileType[]" value="techsheet" onclick="$('#typeOfFile').html('TECH SHEET');"/>&nbsp;Tech Sheet File<br/>
						</td>
					</tr>
                    <tr>
                        <td colspan="2">
                            <div>
                                <input id="input" name="inputFile[]" type="file">
                            </div>
                        </td>
                    </tr>
					<tr>
                        <td colspan="2">
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

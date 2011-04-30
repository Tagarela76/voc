{if $failedSheets}
<table align="center" cellpadding="0" cellspacing="0" style="margin-top:15px">
    <tr>
        <td>
            <div class="bl_o">
                <div class="br_o">
                    <div class="tl_o">
                        <div class="tr_o">
                            <center>
                                <h3 style="margin:1px">Failed sheets</h3>
                            </center>
                            <table align="center">
                                {section name=i loop=$cntFailed}
                                <tr>
                                    <td>
                                        <b>{$failedSheets[i].msdsName}</b>
                                    </td>
                                    <td>
                                        {$failedSheets[i].reason}
                                    </td>
                                </tr>
                                {/section} 
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </td>
    </tr>
    <tr>
        <td class="tail_orange">
        </td>
    </tr>
</table>
{/if} 
{***************************/err*************************************}
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
            <form name="form" action="" method="get">
                <table align="center" cellpadding="5">
                    <tr>
                        <th>
                            Assigned MSDS sheets:
                        </th>
                        <th>
                            Unassigned MSDS sheets:
                        </th>
                    </tr>
					
                    {section name=i loop=$maxCnt}
                    <tr>
                        <td>
                        
                            {if $recognized[i].name} 
                            {$recognized[i].name} 
                            <select name="product2sheetRec_{$smarty.section.i.index}"  class="addInventory">                            	
                                <option value="">none</option>                                
                                {*section name=j loop=$productList}
									<option value="{$productList[j].product_id}" {if $recognized[i].product_id==$productList[j].product_id}selected{/if}>{$productList[j].formattedProduct} </option>
                                {/section*}                            
                            {if $productList}				
								{foreach from=$productList item=productsArr key=supplier}															
									<optgroup label="{$supplier}">
										{section name=j loop=$productsArr}
											<option value='{$productsArr[j].product_id}' {if $productsArr[j].product_id eq $data.product_id}selected="selected"{/if}> {$productsArr[j].formattedProduct} </option>									
										{/section}
									</optgroup>
								{/foreach}																										
							{/if}
                            </select>

                            	{if $recognized[i].failed}
                            <div style="width:80px;margin:2px 0px 0px 5px;" align="left">
                                <img src='design/user/img/alert1.gif' height=16 style="float:left;">
                                <font style="float:left;vertical-align:bottom;color:red;margin:1px 0px 0px 5px;">
                                    Error!
                                </font>
                            </div>
                            	{/if}
                            	
							<input type="hidden" name="sheetRec_{$smarty.section.i.index}" value="{$recognized[i].name}">
							<input type="hidden" name="sheetRecRealName_{$smarty.section.i.index}" value="{$recognized[i].real_name}">
							{/if}
                        </td>
                        <td>
                            {if $unrecognized[i].name}
                            {$unrecognized[i].name}
                            <select name="product2sheetUnrec_{$smarty.section.i.index}" class="addInventory">
                                <option value="">none</option>
                                {*section name=j loop=$productList}
								<option value="{$productList[j].product_id}">{$productList[j].formattedProduct}</option>
                                {/section*}
                           {if $productList}				
								{foreach from=$productList item=productsArr key=supplier}															
									<optgroup label="{$supplier}">
										{section name=x loop=$productsArr}
											<option value='{$productsArr[x].product_id}' {if $productsArr[x].product_id eq $data.product_id}selected="selected"{/if}> {$productsArr[x].formattedProduct} </option>									
										{/section}
									</optgroup>
								{/foreach}																										
							{/if}
                            </select>
							
                            <input type="hidden" name="sheetUnrec_{$smarty.section.i.index}" value="{$unrecognized[i].name}">
							<input type="hidden" name="sheetUnrecRealName_{$smarty.section.i.index}" value="{$unrecognized[i].real_name}">
							{/if}
                        </td>
                    </tr>
					{/section}
                    <tr>
                        <td>
                            <input type="button" name="button" class="button" value="Back" onclick="location.href='?action=msdsUploader&step=main&itemID={$request.category}&id={$request.id}&basic=yes'">
                        </td>
                        <td>
                            <input type="submit" name="button" class="button" value="Save">
                        </td>
                    </tr>
                </table>
				<input type="hidden" name="sheetRecCount" value="{$cnt.recognized}">
				<input type="hidden" name="sheetUnrecCount" value="{$cnt.unrecognized}">
				<input type='hidden' name='action' value='msdsUploader'>
				<input type='hidden' name='step' value='save'>
				<input type='hidden' name='itemID' value={$request.category}>
				<input type='hidden' name='id' value={$request.id}>
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
	<script type="text/javascript" src='modules/js/jquery-ui-1.8.2.custom/js/jquery-1.4.2.min.js'></script>
	


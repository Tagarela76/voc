{if $color eq "green"}
{include file="tpls:tpls/notify/greenNotify.tpl" text=$message}
{/if}
{if $color eq "orange"}
{include file="tpls:tpls/notify/orangeNotify.tpl" text=$message}
{/if}
{if $color eq "blue"}
{include file="tpls:tpls/notify/blueNotify.tpl" text=$message}
{/if}
<form method="POST" action="?action=addNewProduct&category={$request.category}&id={$request.id}">
    {*shadow*} 
    <table class="report_issue" cellspacing="0" cellpadding="0" align="center">
        <tr>
            <td valign="top" class="report_issue_top">
            </td>
        </tr>
        <tr>
            <td valign="top" class="report_issue_center" align="center">
                {**}
                <h1>New Product Request Form</h1>                
                <table cellspacing="0" cellpadding="0" valign="top" width="440px">                	
                    <tr>
                        <td>
                            Manufacturer/Supplier:
                        </td>
                        <td width=330px>
                            <input type="text" name="productSupplier" value="{$productSupplier}" {*class="reportIssue"*} size=33px>
                        </td>
                        <td>
                            {if $validStatus.summary eq 'false'}
                            {if $validStatus.productSupplier eq 'failed'}
                            {*ERORR*}
                            <div style="width:55px;margin:2px 0px 0px 2px;" align="left">
                                <img src='design/user/img/alert1.gif' height=16 style="float:left;">
                                <font style="float:left;vertical-align:bottom;color:red;margin:1px 0px 0px 0px;">
                                    {*Error!*}
                                </font>
                            </div>
                            {*/ERORR*}
                            {/if}
                            {/if}
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Product ID/Number:
                        </td>
                        <td width=330px>
                            <input type="text" name="productId" value="{$productId}" {*class="reportIssue"*} size=33px>
                        </td>
                        <td>
                            {if $validStatus.summary eq 'false'}
                            {if $validStatus.productId eq 'failed'}
                            {*ERORR*}
                            <div style="width:55px;margin:2px 0px 0px 2px;" align="left">
                                <img src='design/user/img/alert1.gif' height=16 style="float:left;">
                                <font style="float:left;vertical-align:bottom;color:red;margin:1px 0px 0px 0px;">
                                    {*Error!*}
                                </font>
                            </div>
                            {*/ERORR*}
                            {/if}
                            {/if}
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Product Name:
                        </td>
                        <td width=330px>
                            <input type="text" name="productName" value="{$productName}" {*class="reportIssue"*} size=33px>
                        </td>
                        <td>
                            {if $validStatus.summary eq 'false'}
                            {if $validStatus.productName eq 'failed'}
                            {*ERORR*}
                            <div style="width:55px;margin:2px 0px 0px 2px;" align="left">
                                <img src='design/user/img/alert1.gif' height=16 style="float:left;">
                                <font style="float:left;vertical-align:bottom;color:red;margin:1px 0px 0px 0px;">
                                    {*Error!*}
                                </font>
                            </div>
                            {*/ERORR*}
                            {/if}
                            {/if}
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Product Description:
                        </td>
                        <td width=330px>
                            <input type="text" name="productDescription" value="{$productDescription}" {*class="reportIssue"*} size=33px>
                        </td>
                        <td>
                            {if $validStatus.summary eq 'false'}
                            {if $validStatus.productDescription eq 'failed'}
                            {*ERORR*}
                            <div style="width:55px;margin:2px 0px 0px 2px;" align="left">
                                <img src='design/user/img/alert1.gif' height=16 style="float:left;">
                                <font style="float:left;vertical-align:bottom;color:red;margin:1px 0px 0px 0px;">
                                    {*Error!*}
                                </font>
                            </div>
                            {*/ERORR*}
                            {/if}
                            {/if}
                        </td>
                    </tr>
                    <tr>
                        <td>
                            MSDS File:
                        </td>
                        <td {*style="padding:5px 5px 0px 5px"*} align="left" colspan="2">
                            <input type="file" name="file" size=22px> 
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:5px 5px 0px 5px" align="left" colspan="2">
                            <input type="submit" name="productAction" value="Submit" class="button" Style="Float:Right;margin:0 1px">   
                        </td>
                    </tr>
                </table>
				<input type="hidden" name="productReferer" value="{$productReferer}">
				{*<input type="hidden" name="action" value="reportIssue">*} 
				{*shadow*} 
            </td>
        </tr>
        <tr>
            <td valign="top" class="report_issue_bottom">
            </td>
        </tr>
    </table>
    {**} 
</form>

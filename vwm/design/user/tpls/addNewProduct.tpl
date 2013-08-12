{if $color eq "green"}
{include file="tpls:tpls/notify/greenNotify.tpl" text=$message}
{/if}
{if $color eq "orange"}
{include file="tpls:tpls/notify/orangeNotify.tpl" text=$message}
{/if}
{if $color eq "blue"}
{include file="tpls:tpls/notify/blueNotify.tpl" text=$message}
{/if}
<form enctype="multipart/form-data" method="POST" action="?action=addNewProduct&category={$request.category}&id={$request.id}">
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
                            <input type="text" name="productSupplier" value="{$productRequest->supplier}" {*class="reportIssue"*} size=33px>
							{foreach from=$violationList item="violation"}
								{if $violation->getPropertyPath() eq 'supplier'}
								{*ERROR*}					
								<div class="error_img" style="float: left;"><span class="error_text">{$violation->getMessage()}</span></div>
								{*/ERROR*}						    
								{/if}
							{/foreach}								                           
                        </td>                        
                    </tr>					
                    <tr>
                        <td>
                            Product ID/Number:
                        </td>
                        <td width=330px>
                            <input type="text" name="productId" value="{$productRequest->product_id}" {*class="reportIssue"*} size=33px>
							{foreach from=$violationList item="violation"}
								{if $violation->getPropertyPath() eq 'product_id'}
								{*ERROR*}					
								<div class="error_img" style="float: left;"><span class="error_text">{$violation->getMessage()}</span></div>
								{*/ERROR*}						    
								{/if}
							{/foreach}								                           
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Product Name:
                        </td>
                        <td width=330px>
                            <input type="text" name="productName" value="{$productRequest->name}" {*class="reportIssue"*} size=33px>
							{foreach from=$violationList item="violation"}
								{if $violation->getPropertyPath() eq 'name'}
								{*ERROR*}					
								<div class="error_img" style="float: left;"><span class="error_text">{$violation->getMessage()}</span></div>
								{*/ERROR*}						    
								{/if}
							{/foreach}	
                        </td>                        
                    </tr>
                    <tr>
                        <td>
                            Product Description:
                        </td>
                        <td width=330px>
                            <input type="text" name="productDescription" value="{$productRequest->description}" {*class="reportIssue"*} size=33px>
							{foreach from=$violationList item="violation"}
								{if $violation->getPropertyPath() eq 'description'}
								{*ERROR*}					
								<div class="error_img" style="float: left;"><span class="error_text">{$violation->getMessage()}</span></div>
								{*/ERROR*}						    
								{/if}
							{/foreach}	
                        </td>
                    </tr>
                    <tr>
                        <td>
                            SDS File (doc/pdf):
                        </td>
                        <td {*style="padding:5px 5px 0px 5px"*} align="left" colspan="2">
                            <input type="file" name="inputFile[]" size=22px> 
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

{if $color eq "green"}
    {include file="tpls:tpls/notify/greenNotify.tpl" text=$message}
{/if}
{if $color eq "orange"}
    {include file="tpls:tpls/notify/orangeNotify.tpl" text=$message}
{/if}
{if $color eq "blue"}
    {include file="tpls:tpls/notify/blueNotify.tpl" text=$message}
{/if}
{literal}
    <script type="text/javascript">
        function onClickRadio(radio){
            var html;
                html = '<br<tr>'+
                        '<td width="35%" colspan="2">'+
                            'User Name:'+
                        '</td>'+
                        '<td width="55%" colspan="2">'+
                            '<select type="text" name="productSupplier">'+
                        '</td>'+
                        '<td> 1321 </td>'+
                       '</tr>';
            $('#myclass1').append(html);
        }
    </script>
{/literal}
<form enctype="multipart/form-data" method="POST" action="?action=userRequest&category={$request.category}&id={$request.id}">
    {*shadow*} 
    <table class="report_issue" cellspacing="0" cellpadding="0" align="center">
        <tr>
            <td valign="top" class="report_issue_top">
            </td>
        </tr>
        <tr>
            <td valign="top" class="report_issue_center" align="center">
                {**}
                <h2>Username & Password Request Form</h2>
                <table cellspacing="0" cellpadding="0" valign="top" width="440px" border="1"> 
                    <p id="myclass1">
                    <tr colspan="5">
                        <td nowrap width="15%">
                            <input type="radio" name="radioRequest" value="lost"> Lost
                        </td>
                        <td nowrap width="20%">
                            <input type="radio" name="radioRequest" value="cansel" onclick="onClickRadio(value);"> Cancel
                        </td>
                        <td nowrap width="25%">
                            <input type="radio" name="radioRequest" value="username"> Username
                        </td>
                        <td nowrap width="30%">
                            <input type="radio" name="radioRequest" value="password"> Password
                        </td>
                        <td nowrap width="10%">
                        </td>
                    </tr>
                    <tr id="myclass">
                        <td width="35%" colspan="2">
                            User Name:
                        </td>
                        <td width="55%" colspan="2">
                            <select type="text" name="productSupplier">
                        </td>
                        <td>
                            {if $validStatus.summary eq 'false'}
                                {if $validStatus.productSupplier eq 'failed'}
                                    {*ERORR*}
                                    <div width="10%" style="margin:2px 0px 0px 2px;" align="left">
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
                    </p>
                    <tr>
                        <td style="padding:5px 5px 0px 5px" align="left" colspan="5">
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
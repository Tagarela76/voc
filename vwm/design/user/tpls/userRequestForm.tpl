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
        function onClickRadio(radio, categ){
            var outputhtml_lost;
            var outputhtml_cancel;
            var outputhtml_username;
            var outputhtml_password;
                
                outputhtml_lost = '<tr><td width="35%" colspan="2">'+
                            'User Name:'+
                            '</td>'+
                            '<td width="55%" colspan="2">'+
                            '<select type="text" name="username">'+
                            '</td>'+
                            '<td></td></tr>';
                outputhtml_cancel = '<tr><td width="35%" colspan="2">'+
                            'User Name:'+
                            '</td>'+
                            '<td width="55%" colspan="2">'+
                            '<select type="text" name="username">'+
                            '</td>'+
                            '<td></td></tr>';
                outputhtml_username = '<tr><td width="35%" colspan="2">'+
                            'User Name:'+
                            '</td>'+
                            '<td width="55%" colspan="2">'+
                            '<select  type="text" name="username">'+
                            '</td>'+
                            '<td></td></tr>'+
                            '<tr><td width="35%" colspan="2">'+
                            'New User Name:'+
                            '</td>'+
                            '<td width="55%" colspan="2">'+
                            '<input  type="text" name="username">'+
                            '</td>'+
                            '<td></td></tr>'+
                            '<tr><td width="35%" colspan="2"></td>'+
                            '<td width="55%" colspan="2"><input type="checkbox" value="off" onclick="onClickChBox(name, value);" name="newUser"> Create New User</td>'+
                            '<td></td></tr>';                
                outputhtml_password = '<tr><td width="35%" colspan="2">'+
                            'Old Password:'+
                            '</td>'+
                            '<td width="55%" colspan="2">'+
                            '<input  type="password" name="username">'+
                            '</td>'+
                            '<td></td></tr>'+
                            '<tr><td width="35%" colspan="2">'+
                            'New Password:'+
                            '</td>'+
                            '<td width="55%" colspan="2">'+
                            '<input  type="password" name="username">'+
                            '</td>'+
                            '<td></td></tr>'+
                            '<tr><td width="35%" colspan="2">'+
                            'Repeat New Password:'+
                            '</td>'+
                            '<td width="55%" colspan="2">'+
                            '<input  type="password" name="username">'+
                            '</td>'+
                            '<td></td></tr>';                
            if (radio=="lost"){
                $('#myclass').html(outputhtml_lost);
            }
            if (radio=="cancel"){
                $('#myclass').html(outputhtml_cancel);
            }
            if (radio=="username"){
                $('#myclass').html(outputhtml_username);
            }
            if (radio=="password"){
                $('#myclass').html(outputhtml_password);
            }    
        }
        function onClickChBox(chbox, value, categ){
            var outputhtml_username;

            if ((chbox=="newUser") && (value=="off")){
                outputhtml_username = '<tr><td width="35%" colspan="2">'+
                            'New User Name:'+
                            '</td>'+
                            '<td width="55%" colspan="2">'+
                            '<input  type="text" name="newUserName">'+
                            '</td>'+
                            '<td></td></tr>'+
                            '<tr><td width="35%" colspan="2"></td>'+
                            '<td width="55%" colspan="2"><input type="checkbox" checked value="on" onclick="onClickChBox(name, value);" name="newUser"> Create New User</td>'+
                            '<td></td></tr>'+
                            '<tr><td width="35%" colspan="2">Facility/Department</td>'+
                            '<td width="55%" colspan="2"><select type="text" name="structureName"></td>'+
                            '<td></td></tr>';
                $('#myclass').html(outputhtml_username);
            } else {
                outputhtml_username = '<tr><td width="35%" colspan="2">'+
                            'User Name:'+
                            '</td>'+
                            '<td width="55%" colspan="2">'+
                            '<select  type="text" name="username">'+
                            '</td>'+
                            '<td></td></tr>'+
                            '<tr><td width="35%" colspan="2">'+
                            'New User Name:'+
                            '</td>'+
                            '<td width="55%" colspan="2">'+
                            '<input  type="text" name="username">'+
                            '</td>'+
                            '<td></td></tr>'+
                            '<tr><td width="35%" colspan="2"></td>'+
                            '<td width="55%" colspan="2"><input type="checkbox" value="off" onclick="onClickChBox(name, value);" name="newUser"> Create New User</td>'+
                            '<td></td></tr>';
                $('#myclass').html(outputhtml_username);                
            }    
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
                <table cellspacing="0" cellpadding="0" valign="top" width="440px"> 
                    <tr>
                        <td nowrap width="15%">
                            <input type="radio" name="radioRequest" checked value="lost" onclick="onClickRadio(value, '{$request.category}');"> Lost
                        </td>
                        <td nowrap width="20%">
                            <input type="radio" name="radioRequest" value="cancel" onclick="onClickRadio(value, '{$request.category}');"> Cancel
                        </td>
                        <td nowrap width="25%">
                            <input type="radio" name="radioRequest" value="username" onclick="onClickRadio(value, '{$request.category}');"> Username
                        </td>
                        <td nowrap width="30%">
                            <input type="radio" name="radioRequest" value="password" onclick="onClickRadio(value, '{$request.category}');"> Password
                        </td>
                        <td nowrap width="10%">
                        </td>
                    </tr>
                </table>
                <hr width="400px">
                <table width="440px" id="myclass">
                    <tr>
                        <td width="35%" collspan="2">
                            User Name:
                        </td>
                        <td width="55%" collspan="2">
                            <select type="text" name="username">
                        </td>
                        <td>
                        </td>
                    </tr>
                </table>
                <table width="440px">
                    <tr>
                        <td style="padding:5px 5px 0px 5px" align="left" colspan="5">
                            <input type="submit" name="productAction" value="Submit" class="button" Style="Float:Right;margin:0 1px">   
                        </td>
                    </tr>
                </table>
                <input type="hidden" name="productReferer" value="{$productReferer}">
            </td>
        </tr>
        <tr>
            <td valign="top" class="report_issue_bottom">
            </td>
        </tr>
    </table>
    {**} 
</form>
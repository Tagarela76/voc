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
                //$('#myclass').html(outputhtml_lost);
				$('#usersFields').show();
				$('#newUserName').hide();
				$('#chBox').hide();
				$('#structName').hide();
				$('#oldPass').hide();
				$('#newPass').hide();
				$('#reNewPass').hide();	
            }
            if (radio=="cancel"){
                //$('#myclass').html(outputhtml_cancel);
				$('#usersFields').show();
				$('#newUserName').hide();
				$('#chBox').hide();
				$('#structName').hide();
				$('#oldPass').hide();
				$('#newPass').hide();
				$('#reNewPass').hide();	
            }
            if (radio=="username"){
				$('#usersFields').show();
				$('#newUserName').show();
				$('#chBox').show();
				$('#structName').hide();
				$('#oldPass').hide();
				$('#newPass').hide();
				$('#reNewPass').hide();	
                //$('#myclass').html(outputhtml_username);
//                $.ajax({
//                    url: 'modules/ajax/getUserList.php',
//                    dataType:'json',
//                    data: 'level=company&id=115',
//                        success: function(responce) {
//                            if (responce.success) {
//                                for (var key in responce.users) {
//                                       if (responce.users.hasOwnProperty(key)) {
//										   $('selectbox').append(
//												'<option value='+responce.users[key].user_id+'>'+responce.users[key].username+'</option>'
//											);										
//										}
//								}
//                            } else {
//								alert('something wrong');
//							}
//						},
//                        error: function() {
//                            alert('something wrong');
//                            }
//
//                    });
            }
            if (radio=="password"){
                //$('#myclass').html(outputhtml_password);
				$('#usersFields').hide();
				$('#newUserName').hide();
				$('#chBox').hide();	
				$('#structName').hide();
				$('#oldPass').show();
				$('#newPass').show();
				$('#reNewPass').show();	
            }
        }
        function onClickChBox(chbox, value){
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
                //$('#myclass').html(outputhtml_username);
				$('#usersFields').hide();
				$('#newUserName').show();
				$('#chBox').show();	
				$('#chBox input').attr('Value', 'on');
				$('#structName').show();
				$('#oldPass').hide();
				$('#newPass').hide();
				$('#reNewPass').hide();
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
                //$('#myclass').html(outputhtml_username);
				$('#usersFields').show();
				$('#newUserName').show();
				$('#chBox').show();	
				$('#chBox input').attr('Value', 'off');
				$('#structName').hide();
				$('#oldPass').hide();
				$('#newPass').hide();
				$('#reNewPass').hide();	
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
                    <tr id="usersFields" style="display: table-row;">
                        <td width="35%" colspan="2">
                            User Name:
                        </td>
                        <td width="55%" colspan="2">
                            <select type="text" name="username">
								{foreach from=$userList item=user}
									<option value="{$user.user_id}">{$user.username}</option>
								{/foreach}
							</select>
                        </td>
                        <td>
                        </td>
                    </tr>
					<tr id="newUserName" style="display: none;">
						<td width="35%" colspan="2">
                            New User Name:
                        </td>
                        <td width="55%" colspan="2">
                            <input type="text" name="newusername">
                        </td>
                        <td>
                        </td>
					</tr>
					<tr id="chBox" style="display: none;">
						<td width="35%" colspan="2">
						</td>
						<td width="55%" colspan="2">
							<input type="checkbox" value="off" onclick="onClickChBox(name, value);" name="newUser"> Create New User
						</td>
                        <td>
						</td>
					</tr>
					<tr id="structName" style="display: none;">
						<td width="35%" colspan="2">
							Facility/Department
						</td>
                        <td width="55%" colspan="2">
							<select type="text" name="structureName">
								{if $request.category=='company'}
								{foreach from=$structureList item=structure}
									<option value="{$structure.facility_id}">{$structure.name}</option>
								{/foreach} 
								{elseif $request.category=='facility'}
									{foreach from=$structureList item=structure}
										<option value="{$structure.department_id}">{$structure.name}</option>
									{/foreach}
								{/if}	
							</select>
						</td>
                        <td>
						</td>
					</tr>
					<tr id="oldPass" style="display: none;">
						<td width="35%" colspan="2">
							Old Password:
                        </td>
						<td width="55%" colspan="2">
							<input  type="password" name="oldpass">
                        </td>
						<td>
						</td>
					</tr>
					<tr id="newPass" style="display: none;">
						<td width="35%" colspan="2">
							New Password:
						</td>
						<td width="55%" colspan="2">
							<input  type="password" name="newpass">
						</td>
						<td>
						</td>
					</tr>
					<tr id="reNewPass" style="display: none;">
						<td width="35%" colspan="2">
							Repeat New Password:
						</td>
						<td width="55%" colspan="2">
							<input  type="password" name="renewpass">
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
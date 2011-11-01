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
			
            if (radio=="lost"){
				$('#usersList').show();
				$('#newUserName').hide();
				$('#newAccessName').hide();
				$('#email').hide();
				$('#phone').hide();
				$('#mobile').hide();	
				$('#newUserCheckBox').hide();
				$('#structureName').hide();
				$('#oldPass').hide();
				$('#newPass').hide();
				$('#reNewPass').hide();
            }
            if (radio=="cancel"){
				$('#usersList').show();
				$('#newUserName').hide();
				$('#newAccessName').hide();
				$('#email').hide();
				$('#phone').hide();
				$('#mobile').hide();	
				$('#newUserCheckBox').hide();
				$('#structureName').hide();
				$('#oldPass').hide();
				$('#newPass').hide();
				$('#reNewPass').hide();
            }
            if (radio=="username"){
				$('#usersList').show();
				$('#newUserName').show();
				$('#newAccessName').hide();
				$('#email').hide();
				$('#phone').hide();
				$('#mobile').hide();	
				$('#newUserCheckBox').show();
				$('#structureName').hide();
				$('#oldPass').hide();
				$('#newPass').hide();
				$('#reNewPass').hide();
            }
            if (radio=="password"){
				$('#usersList').hide();
				$('#newUserName').hide();
				$('#newAccessName').hide();
				$('#email').hide();
				$('#phone').hide();
				$('#mobile').hide();	
				$('#newUserCheckBox').hide();
				$('#structureName').hide();
				$('#oldPass').show();
				$('#newPass').show();
				$('#reNewPass').show();
            }
        }
        function onClickChBox(chbox, value, category){
            
            if ((chbox=="newUser") && (value=="off")){
				$('#usersList').hide();
				$('#newUserName').show();
				$('#newAccessName').show();
				$('#email').show();
				$('#phone').show();
				$('#mobile').show();	
				$('#newUserCheckBox').show();
				$('#newUserCheckBox input').attr('Value', 'on');
				if (category=='company'){
					$('#structureName #structureCaption').text('Facility:');
					$('#structureName').show();
				} else {
					if (category=='facility'){
						$('#structureName #structureCaption').text('Department:');
						$('#structureName').show();
					}
				}
				$('#oldPass').hide();
				$('#newPass').hide();
				$('#reNewPass').hide();
            } else {
				$('#usersList').show();
				$('#newUserName').show();
				$('#newAccessName').hide();
				$('#email').hide();
				$('#phone').hide();
				$('#mobile').hide();	
				$('#newUserCheckBox').show();
				$('#newUserCheckBox input').attr('Value', 'off');
				$('#structureName').hide();
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
					{if $error neq ''}
					<tr>
						<td colspan="5" align="center">
							<font color="red">{$error}</font>
						</td>
					</tr>
					{/if}
                    <tr>
                        <td nowrap width="15%">
                            <input type="radio" name="radioRequest" checked value="lost" onclick="onClickRadio(value);"/>&nbsp;Lost
                        </td>
                        <td nowrap width="20%">
                            <input type="radio" name="radioRequest" value="cancel" onclick="onClickRadio(value);"/>&nbsp;Cancel
                        </td>
                        <td nowrap width="25%">
                            <input type="radio" name="radioRequest" value="username" onclick="onClickRadio(value);"/>&nbsp;Username
                        </td>
                        <td nowrap width="30%">
                            <input type="radio" name="radioRequest" value="password" onclick="onClickRadio(value);"/>&nbsp;Password
                        </td>
                        <td nowrap width="10%">
                        </td>
                    </tr>
                </table>
                <hr width="400px">
                <table width="440px" id="myclass">
                    <tr id="usersList" style="display: table-row;">
                        <td width="35%" colspan="2">
                            User Name:
                        </td>
                        <td width="55%" colspan="2">
                            <select type="text" name="user_id">
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
                            <input type="text" name="new_username"/>
                        </td>
                        <td>
                        </td>
					</tr>
					<tr id="newAccessName" style="display: none;">
						<td width="35%" colspan="2">
                            New Access Name:
                        </td>
                        <td width="55%" colspan="2">
                            <input type="text" name="new_accessname"/>
                        </td>
                        <td>
                        </td>
					</tr>
					<tr id="email" style="display: none;">
						<td width="35%" colspan="2">
                            Email:
                        </td>
                        <td width="55%" colspan="2">
                            <input type="text" name="email">
                        </td>
                        <td>
                        </td>
					</tr>
					<tr id="phone" style="display: none;">
						<td width="35%" colspan="2">
                            Phone:
                        </td>
                        <td width="55%" colspan="2">
                            <input type="text" name="phone">
                        </td>
                        <td>
                        </td>
					</tr>
					<tr id="mobile" style="display: none;">
						<td width="35%" colspan="2">
                            Mobile:
                        </td>
                        <td width="55%" colspan="2">
                            <input type="text" name="mobile">
                        </td>
                        <td>
                        </td>
					</tr>
					<tr id="newUserCheckBox" style="display: none;">
						<td width="35%" colspan="2">
						</td>
						<td width="55%" colspan="2">
							<input type="checkbox" value="off" onclick="onClickChBox(name, value, '{$request.category}');" name="newUser"> Create New User
						</td>
                        <td>
						</td>
					</tr>
					<tr id="structureName" style="display: none;">
						<td width="35%" colspan="2">
							<div id="structureCaption"></div>
						</td>
                        <td width="55%" colspan="2">
							<select type="text" name="structure_id">
								{if $request.category eq 'company'}
								{foreach from=$structureList item=structure}
									<option value="{$structure.id}">{$structure.name}</option>
								{/foreach}
									<input type="hidden" name="structureCategory" value="facility">
								{elseif $request.category eq 'facility'}
									{foreach from=$structureList item=structure}
										<option value="{$structure.id}">{$structure.name}</option>
									{/foreach}
										<input type="hidden" name="structureCategory" value="department">
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
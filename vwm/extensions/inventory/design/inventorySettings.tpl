{*ajax-preloader*}
<div style="height:16px;text-align:center;">
	<div id="preloader" style="display:none">
		<img src='images/ajax-loader.gif'>
	</div>
</div>

{if $color eq "green"}
{include file="tpls:tpls/notify/greenNotify.tpl" text=$message}
{/if}
{if $color eq "orange"}
{include file="tpls:tpls/notify/orangeNotify.tpl" text=$message}
{/if}
{if $color eq "blue"}
{include file="tpls:tpls/notify/blueNotify.tpl" text=$message}
{/if}

<div style="padding:7px;">
	<form method='POST' action='?action={$request.action}&category=inventory&facilityID={$request.facilityID}&tab={$request.tab}'>
        <table class="users" align="center" cellpadding="0" cellspacing="0">
            <tr class="users_u_top_size users_top">
                <td class="users_u_top" width="30%">
                    <span>Editing settings</span>
                </td>
                <td class="users_u_top_r">
                </td>
            </tr>
            <tr>
                <td class="border_users_r border_users_l border_users_b" height="20">
                    E-mail to all :
                </td>
                <td class="border_users_r border_users_b">

					<textarea name='email_all' style="width: 380px;height: 168px;">{$email.email_all}</textarea>
				
                </td>
            </tr>
	
            <tr>
                <td class="border_users_r border_users_l border_users_b" height="20">
                    E-mail only to Manager :
                </td>
                <td class="border_users_r border_users_b">

                    <div align="left">
                        <textarea name='email_manager' style="width: 380px;height: 168px;">{$email.email_manager}</textarea>
                    </div>


                </td>
            </tr>				
			
            <tr>
                <td class="users_u_bottom">
                </td>
                <td bgcolor="" height="20" class="users_u_bottom_r">
                </td>
            </tr>
        </table>
					
        <div align="right" class="margin7">

				<input type='button' class="button" value='Cancel' onclick="location.href='?action=browseCategory&category=facility&id={$request.facilityID}&bookmark=inventory&tab=products'">
			
      	
            <input type='submit' class="button" value='Save'>
			<input type='hidden' name="facilityID" value='{$request.facilityID}'>
			<input type='hidden' name="email_id" value='{$email.email_id}'>

        </div>									
    </form>
</div>

{literal}
<script type="text/javascript">
	$(function() {
		//	global settings object defined at settings.js
		settings.facilityId = {/literal} {$reminders->facility_id} {literal};
		settings.remindId = {/literal} {$reminders->id} {literal};
	});
</script>
{/literal}
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
    <table class="users" align="center" cellpadding="0" cellspacing="0">
        <tr class="users_top_yellowgreen users_u_top_size">
            <td class="users_u_top_yellowgreen" width="37%" height="30">
                <span>View reminder</span>
            </td>
            <td class="users_u_top_r_yellowgreen" width="300">
            </td>
        </tr>
        <tr>
            <td class="border_users_l border_users_b" height="20">
                Reminder name:
            </td>
            <td class="border_users_l border_users_b border_users_r">
                <div align="left">
                    &nbsp; {$reminders->name|escape}
                </div>
            </td>
        </tr>
        <tr>
            <td class="border_users_l border_users_b" height="20">
                Reminder Date:
            </td>
            <td class="border_users_l border_users_b border_users_r">
                <div align="left">
                    &nbsp; {$reminders->date|escape}
                </div>
            </td>
        </tr>
        <tr>
            <td height="20" class="users_u_bottom">
            </td>
            <td height="20" class="users_u_bottom_r">
            </td>
        </tr>
    </table>
	<br />
	<div style="font-size: 14px;" id="usersList">
		{if $usersList} 
			Users: {$usersList}
		{/if}	
	</div>
	<br />
	<div>
		<input type="button" class="button" value="Set" name="setRemind2User" onclick="settings.manageReminders.openDialog();" />
	</div>
    <div align="right">
    </div>    
</div> 
<div id="setRemind2UserContainer" title="set remind to user" style="display:none;">Loading ...</div>	
{if $color eq "green"}
{include file="tpls:tpls/notify/greenNotify.tpl" text=$message}
{/if}
{if $color eq "orange"}
{include file="tpls:tpls/notify/orangeNotify.tpl" text=$message}
{/if}
{if $color eq "blue"}
{include file="tpls:tpls/notify/blueNotify.tpl" text=$message}
{/if}
<div class="padd7">
	<form action="{$addUsageUrl}" method="post" id="addUsageForm">
    <table class="users" align="center" cellpadding="0" cellspacing="0">
        <tr class="users_top_yellowgreen users_u_top_size">
            <td class="users_u_top_yellowgreen" width="10%" height="30">
                <span>View action log</span>
            </td>
            <td class="users_u_top_r_yellowgreen" width="300">
            </td>
        </tr>
        <tr>
            <td class="border_users_l border_users_b" height="20">
                User ID :
            </td>
            <td class="border_users_l border_users_b border_users_r">
                <div align="left">
                    &nbsp;{$log.user_id}
                </div>
            </td>
        </tr>
        <tr>
            <td class="border_users_l border_users_b" height="20">
                User Name :
            </td>
            <td class="border_users_l border_users_b border_users_r">
                <div align="left">
                    &nbsp;{$user.username}
                </div>
            </td>
        </tr>
		
        <tr>
            <td class="border_users_l border_users_b" height="20">
                Access Level :
            </td>
            <td class="border_users_l border_users_b border_users_r">
                <div align="left">
                    &nbsp;{$user.accesslevel_id}
                </div>
            </td>
        </tr>		
		{foreach from=$action->get item=value key=key}
        <tr>
            <td class="border_users_l border_users_b" height="20">
                {$key} :
            </td>
            <td class="border_users_l border_users_b border_users_r">
                <div align="left">
                    &nbsp;{$value}
                </div>
            </td>
        </tr>	
		{/foreach}
{if $log.action_type neq 'POST' && $log.action_type neq 'AUTH' && $log.action_type neq 'LOGOUT'}		
        <tr>
            <td class="border_users_l border_users_b" height="20">
                Link to the Page :  {$log.action_type}
            </td>
            <td class="border_users_l border_users_b border_users_r">
                <div align="left">
                    &nbsp;<a href='{$action->link}' target="_blank">Page</a>
                </div>
            </td>
        </tr>		
{/if}		
        <tr>
            <td height="20" class="users_u_bottom">
            </td>
            <td height="20" class="users_u_bottom_r">
            </td>
        </tr>
    </table>
</form>
</div>	
{include file="tpls:tpls/logging.tpl"}
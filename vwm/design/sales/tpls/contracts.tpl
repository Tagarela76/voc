<div class="padd7" align="center">
    {if $color eq "green"}
		{include file="tpls:tpls/notify/greenNotify.tpl" text=$message}
    {/if}
    {if $color eq "orange"}
		{include file="tpls:tpls/notify/orangeNotify.tpl" text=$message}
    {/if}
    {if $color eq "blue"}
		{include file="tpls:tpls/notify/blueNotify.tpl" text=$message}
    {/if}
	
	<table class="report_issue" cellspacing="0" cellpadding="0" align="center">
        <tr>
            <td valign="top" class="report_issue_top">
            </td>
        </tr>
        <tr>
            <td class="report_issue_center" align="center" style="vertical-align: middle;">
                <h2>Contracts</h2>
            </td>
        </tr>
        <tr>
            <td valign="top" class="report_issue_bottom">
            </td>
        </tr>
    </table>
	
</div>

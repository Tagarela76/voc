{if $color eq "green"}
{include file="tpls:tpls/notify/greenNotify.tpl" text=$message}
{/if}
{if $color eq "orange"}
{include file="tpls:tpls/notify/orangeNotify.tpl" text=$message}
{/if}
{if $color eq "blue"}
{include file="tpls:tpls/notify/blueNotify.tpl" text=$message}
{/if}
<form method="POST" action="?action=reportIssue&category={$request.category}&id={$request.id}">
    {*shadow*} 
    <table class="report_issue" cellspacing="0" cellpadding="0" align="center">
        <tr>
            <td valign="top" class="report_issue_top">
            </td>
        </tr>
        <tr>
            <td valign="top" class="report_issue_center" align="center">
                {**}
                <h1>{$smarty.const.LINK_SUGGEST_FEATURE}</h1>                
                <table cellspacing="0" cellpadding="0" valign="top" width="440px">
                	<tr>
                		<td colspan="3">{$smarty.const.DESCRIPTION_SUGGEST_FEATURE}</td>
                	</tr>
                    <tr>
                        <td>
                            Title:
                        </td>
                        <td width=330px>
                            <input type="text" name="issueTitle" value="{$issueTitle}" class="reportIssue">
                        </td>
                        <td>
                            {if $validStatus.summary eq 'false'}
                            {if $validStatus.title eq 'failed'}
                            {*ERORR*}
                            <div style="width:55px;margin:2px 0px 0px 2px;" align="left">
                                <img src='design/user/img/alert1.gif' height=16 style="float:left;">
                                <font style="float:left;vertical-align:bottom;color:red;margin:1px 0px 0px 5px;">
                                    Error!
                                </font>
                            </div>
                            {*/ERORR*}
                            {/if}
                            {/if}
                        </td>
                    </tr>
                    <tr>
                        <td valign="top">
                            Description
                        </td>
                        <td valign="top" align="left">                           
                            <textarea class="reportIssue_text" name="issueDescription">{$issueDescription}</textarea>
                        </td>
                        <td>
                            {if $validStatus.summary eq 'false'}
                            {if $validStatus.description eq 'failed'}
                            {*ERORR*}
                            <div style="width:55px;margin:2px 0px 0px 2px;" align="left">
                                <img src='design/user/img/alert1.gif' height=16 style="float:left;">
                                <font style="float:left;vertical-align:bottom;color:red;margin:1px 0px 0px 5px;">
                                    Error!
                                </font>
                            </div>
                            {*/ERORR*}
                            {/if}
                            {/if}
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:5px 5px 0px 5px" align="left" colspan="2">
                            <input type="submit" name="issueAction" value="Discard" class="button" Style="Float:Left;margin:0 10px">
							<input type="submit" name="issueAction" value="Send" class="button">
                        </td>
                    </tr>
                </table>
				<input type="hidden" name="referer" value="{$referer}">
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

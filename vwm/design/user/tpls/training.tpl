{if $color eq "green"}
{include file="tpls:tpls/notify/greenNotify.tpl" text=$message}
{/if}
{if $color eq "orange"}
{include file="tpls:tpls/notify/orangeNotify.tpl" text=$message}
{/if}
{if $color eq "blue"}
{include file="tpls:tpls/notify/blueNotify.tpl" text=$message}
{/if}
<script type="text/javascript" src="../vwm/modules/js/flowplayer-3.2.6.min.js"></script>
    <table border="1px" cellspacing="0" cellpadding="0" align="center">
        <tr>
			<td>
			<a href="../videoTutorial/{$request.category}Training.mp4" style="display:block;width:720px;height:480px" id="player"></a>	
			{if ($smarty.const.ENVIRONMENT eq 'server')}
			<script>
				flowplayer("player", "../videoTutorial/flowplayer.commercial-3.2.7.swf");
			</script>
			{else}
			<script>
				flowplayer("player", "../videoTutorial/flowplayer-3.2.7.swf");
			</script>
			{/if}
            </td>
        </tr>
    </table>
    {**} 
</form>

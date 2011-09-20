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
			{literal}		
				flowplayer("player", "../videoTutorial/flowplayer.commercial-3.2.7.swf", {
					key: '#$5f3af9a58275bb39d55',
					clip: {
							autoPlay: false,
							autoBuffering: true	
					},	
					logo: {
							url: '../images/logoLarge.png',
							top: '85%',
							left: '10',
							width: '15%',
							height: '15%',
							fullscreenOnly: false,
							displayTime: 0,
							opacity: 1	
					}
				});
			{/literal}	
			</script>
			{else}
			<script>
			{literal}	
				flowplayer("player", "../videoTutorial/flowplayer-3.2.7.swf");
			{/literal}		
			</script>
			{/if}
            </td>
        </tr>
    </table>
    {**} 
</form>

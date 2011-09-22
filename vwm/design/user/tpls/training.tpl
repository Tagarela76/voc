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
<table cellspacing="0" cellpadding="0" align="center" style="border-collapse: collapse;">
		<tr>
			<td align="center" width="160px" style="padding-right: 20px">
				<img style="height: 130px; width: 160px; margin-bottom: 50px;" src="../images/logoLarge.png">
				<table id="tableLink" cellspacing="0" cellpadding="0" align="center" style="background: #F0F0F0; border: 1px solid black; border-collapse: collapse;">
					{literal}
					<tr>
						<td align="center" id="login" style="padding: 10px; margin: 0; border: 1px solid black;">
							<a style="text-decoration: none;" onClick="changeColor('login');$f('player', '../videoTutorial/flowplayer.commercial-3.2.7.swf', {
								key: '#$5f3af9a58275bb39d55',
								clip: {
									url: '../videoTutorial/login.mp4',
									autoPlay: false,
									autoBuffering: true	
								}	
								});">
								How to Login
							</a>
						</td>
					</tr>
					<tr>
						<td align="center" id="overview" style="padding: 10px; margin: 0; border: 1px solid black;">
							<a style="text-decoration: none;" onClick="changeColor('overview');$f('player', '../videoTutorial/flowplayer.commercial-3.2.7.swf', {
								key: '#$5f3af9a58275bb39d55',
								clip: {
									url: '../videoTutorial/overview.mp4',
									autoPlay: false,
									autoBuffering: true	
								}
								});">
								Overwiew
							</a>
						</td>
					</tr>
					<tr>
						<td align="center" id="report" style="padding: 10px; margin: 0; border: 1px solid black;">
							<a style="text-decoration: none;" onClick="changeColor('report');$f('player', '../videoTutorial/flowplayer.commercial-3.2.7.swf', {
								key: '#$5f3af9a58275bb39d55',
								clip: {
									url: '../videoTutorial/report.mp4',
									autoPlay: false,
									autoBuffering: true	
								}
								});">
								Create Report
							</a>
						</td>
					</tr>
					<tr>
						<td align="center" id="graph" style="padding: 10px; margin: 0; border: 1px solid black;">
							<a style="text-decoration: none;" onClick="changeColor('graph');$f('player', '../videoTutorial/flowplayer.commercial-3.2.7.swf', {
								key: '#$5f3af9a58275bb39d55',
								clip: {
									url: '../videoTutorial/graph.mp4',
									autoPlay: false,
									autoBuffering: true	
								}	
								});">
								Company at a Glance Graphs
							</a>
						</td>
					</tr>
					<tr>
						<td align="center" id="payment" style="padding: 10px; margin: 0; border: 1px solid black;">
							<a style="text-decoration: none;" onClick="changeColor('payment');$f('player', '../videoTutorial/flowplayer.commercial-3.2.7.swf', {
								key: '#$5f3af9a58275bb39d55',
								clip: {
									url: '../videoTutorial/payment.mp4',
									autoPlay: false,
									autoBuffering: true	
								}	
								});">
								Payment Process
							</a>
						</td>
					</tr>
					<tr>
						<td align="center" id="entire" style="padding: 10px; margin: 0; border: 3px solid black; background: #D0D0D0; font-weight: bold;">
							<a style="text-decoration: none;" onClick="changeColor('entire');$f('player', '../videoTutorial/flowplayer.commercial-3.2.7.swf', {
								key: '#$5f3af9a58275bb39d55',
								clip: {
									url: {/literal}'../videoTutorial/{$request.category}Training.mp4'{literal},
									autoPlay: false,
									autoBuffering: true	
								}	
								});">
								See Entire Video
							</a>
						</td>
					</tr>
					{/literal}
				</table>
			</td>
			<td style="border:1px solid black;">
			<a href="../videoTutorial/{$request.category}Training.mp4" style="display:block;width:720px;height:480px" id="player"></a>	
			{if ($smarty.const.ENVIRONMENT eq 'server')}
			<script>
			{literal}		
				flowplayer("player", "../videoTutorial/flowplayer.commercial-3.2.7.swf", {
					key: '#$5f3af9a58275bb39d55',
					clip: {
							autoPlay: false,
							autoBuffering: true	
					}
				});
			{/literal}	
			</script>
			{else}
			<script>
			{literal}	
				flowplayer("player", "../videoTutorial/flowplayer-3.2.7.swf", {
					clip: {
							autoPlay: false,
							autoBuffering: true	
					}
					});
			{/literal}		
			</script>
			{/if}
            </td>
        </tr>
    </table>
	{literal}		
	<script>
		var allVal = ['login', 'overview', 'report', 'graph', 'payment', 'entire'];
		function changeColor(val){
			//console.log(allVal);
			for (var i=0; i<allVal.length; i++){
				if (val == allVal[i]){
					document.getElementById(allVal[i]).style.border = '3px solid black';
					document.getElementById(allVal[i]).style.background = '#D0D0D0';
					document.getElementById(allVal[i]).style.fontWeight = 'bold';	
				} else {		
					document.getElementById(allVal[i]).style.border = '1px solid black';
					document.getElementById(allVal[i]).style.background = '#F0F0F0';
					document.getElementById(allVal[i]).style.fontWeight = 'normal';	
				}
			}
		}
	</script>		
	{/literal}
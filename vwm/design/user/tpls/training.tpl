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
    <table cellspacing="0" cellpadding="0" align="center">
        <tr>
			<td align="center" width="120px" style="padding-right: 20px">
				<table id="tableLink" cellspacing="0" cellpadding="0" align="center" border="1px">
					{literal}
					<tr>
						<td align="center" id="login" style="padding: 10px; margin: 0;" onclick="changeColor(id);">
							<a onClick="$f('player', '../videoTutorial/flowplayer.commercial-3.2.7.swf', {
								key: '#$5f3af9a58275bb39d55',
								clip: {
									url: '../videoTutorial/login.mp4',
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
								});">
								How to Login
							</a>
						</td>
					</tr>
					<tr>
						<td align="center" id="overview" style="padding: 10px; margin: 0;" onclick="changeColor(id);">
							<a  onClick="$f('player', '../videoTutorial/flowplayer.commercial-3.2.7.swf', {
								key: '#$5f3af9a58275bb39d55',
								clip: {
									url: '../videoTutorial/overview.mp4',
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
								});">
								Overwiew
							</a>
						</td>
					</tr>
					<tr>
						<td align="center" id="report" style="padding: 10px; margin: 0;" onclick="changeColor(id);">
							<a onClick="$f('player', '../videoTutorial/flowplayer.commercial-3.2.7.swf', {
								key: '#$5f3af9a58275bb39d55',
								clip: {
									url: '../videoTutorial/report.mp4',
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
								});">
								Create Report
							</a>
						</td>
					</tr>
					<tr>
						<td align="center" id="graph" style="padding: 10px; margin: 0;" onclick="changeColor(id);">
							<a onClick="$f('player', '../videoTutorial/flowplayer.commercial-3.2.7.swf', {
								key: '#$5f3af9a58275bb39d55',
								clip: {
									url: '../videoTutorial/graph.mp4',
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
								});">
								Company at a Glance Graphs
							</a>
						</td>
					</tr>
					<tr>
						<td align="center" id="payment" style="padding: 10px; margin: 0; " onclick="changeColor(id);">
							<a onClick="$f('player', '../videoTutorial/flowplayer.commercial-3.2.7.swf', {
								key: '#$5f3af9a58275bb39d55',
								clip: {
									url: '../videoTutorial/payment.mp4',
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
								});">
								Payment Process
							</a>
						</td>
					</tr>
					<tr>
						<td align="center" id="entire" style="padding: 10px; margin: 0; border: 4px solid gray; color: red" onclick="changeColor(id);">
							<a onClick="$f('player', '../videoTutorial/flowplayer.commercial-3.2.7.swf', {
								key: '#$5f3af9a58275bb39d55',
								clip: {
									url: {/literal}'../videoTutorial/{$request.category}Training.mp4'{literal},
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
		function changeColor(val){
			document.getElementById('login').style.border = '1px solid black';	
			document.getElementById('overview').style.border = '1px solid black';	
			document.getElementById('report').style.border = '1px solid black';	
			document.getElementById('graph').style.border = '1px solid black';	
			document.getElementById('payment').style.border = '1px solid black';	
			document.getElementById('entire').style.border = '1px solid black';		
			document.getElementById('login').style.color = 'black';	
			document.getElementById('overview').style.color = 'black';	
			document.getElementById('report').style.color = 'black';	
			document.getElementById('graph').style.color = 'black';	
			document.getElementById('payment').style.color = 'black';	
			document.getElementById('entire').style.color = 'black';	
			document.getElementById(val).style.border = '4px solid gray';
			document.getElementById(val).style.color = 'red';		
		}
	</script>		
	{/literal}
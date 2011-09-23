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
	<tr style="vertical-align: bottom;">
			<td valign="bottom" align="center" width="160px" style="padding-right: 20px">
				<img style="height: 130px; width: 160px; margin-bottom: 50px;" src="../images/logoLarge.png">
				<table id="tableLink" cellspacing="0" cellpadding="0" align="center" style="background: #F0F0F0; border: 1px solid black; border-collapse: collapse;">
				{foreach from=$trainingParts key=key item=part}
					<tr>
						<td align="center" id="{$key}" style="padding: 10px; margin: 0; border: 1px solid black;">
							<a style="text-decoration: none;" onClick="changeColor('{$key}');$f('player', '../videoTutorial/flowplayer.commercial-3.2.7.swf', 
								{literal}
								{
								key: '#$5f3af9a58275bb39d55',
								clip: {
								{/literal}
									url: '../videoTutorial/{$request.category}/{$key}{$request.category|capitalize}.mp4',
								{literal}	
									autoPlay: false,
									autoBuffering: true	
								}	
								});">
								{/literal}	
								{$part}
							</a>
						</td>
					</tr>
				{/foreach}
					<tr>
						<td align="center" id="entire" style="width: 150px;padding: 10px; margin: 0; border: 3px solid black; background: #D0D0D0; font-weight: bold;">
							<a style="text-decoration: none;" onClick="changeColor('entire');$f('player', '../videoTutorial/flowplayer.commercial-3.2.7.swf', 
							   {literal}
								{
								key: '#$5f3af9a58275bb39d55',
								clip: {
								{/literal}	
									url: '../videoTutorial/{$request.category}/training{$request.category|capitalize}.mp4',
								{literal}	
									autoPlay: false,
									autoBuffering: true	
								}	
								});">
								{/literal}	
								See Entire Video
							</a>
						</td>
					</tr>
				</table>
			</td>
			<td>
				<a href="../videoTutorial/{$request.category}/training{$request.category|capitalize}.mp4" style="display:block;width:720px;height:480px;border-bottom: 1px solid black;border-top: 1px solid black;" id="player"></a>	
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
		<tr>
			<input type="hidden" id="hiddenCategory" value="{$request.category}"/>
		</tr>
    </table>
	{literal}		
	<script>
		var allValCompany = ['login', 'overview', 'report', 'graph', 'payment', 'entire'];
		var allValFacility = ['login', 'overview', 'report', 'graph', 'msds', 'newproduct', 'management', 'eqgraph', 'payment', 'entire'];
		var allValDepartment = ['login', 'overview', 'pfpmix', 'singlemix', 'report', 'msds', 'newproduct', 'management', 'eqgraph', 'entire'];
		function changeColor(val){
			//console.log(allVal);
			switch (document.getElementById('hiddenCategory').value){
				case 'company':
					allVal = allValCompany;
					break;
				case 'facility':
					allVal = allValFacility;
					break;
				case 'department':
					allVal = allValDepartment;
					break;		
			}	
			for (var i=0; i<allVal.length; i++){
				if (val == allVal[i]){
					document.getElementById(allVal[i]).style.border = '3px solid black';
					document.getElementById(allVal[i]).style.background = '#D0D0D0';
					document.getElementById(allVal[i]).style.fontWeight = 'bold';
					document.getElementById(allVal[i]).style.width = '150px';	
				} else {		
					document.getElementById(allVal[i]).style.border = '1px solid black';
					document.getElementById(allVal[i]).style.background = '#F0F0F0';
					document.getElementById(allVal[i]).style.fontWeight = 'normal';	
					document.getElementById(allVal[i]).style.width = '150px';		
				}
			}
		}
	</script>		
	{/literal}
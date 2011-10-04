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
<table cellspacing="0" cellpadding="0" align="center" colls="2" style="border-collapse: collapse;">
	<tr>
		<td style="vertical-align: top;" align="center">
			<img style="height: 100px; width: 120px; margin-right: 20px;" src="../images/logoLarge.png">
		</td>
		<td style="vertical-align: bottom;" rowspan="2">
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
		<td align="center" width="160px" style="vertical-align: bottom;padding-right: 20px;">
			<table><tr><td width="180px" align="center">
			<b id="caption">{$request.category|capitalize} Level Tutorial</b><br/><br/>
			</td></tr></table>
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
			</table>
		</td>
		<td>
		</td>
	</tr>
	<tr>
	<input type="hidden" id="hiddenCategory" value="{$request.category}"/>
	<input type="hidden" id="hiddenPart" value="{$request.category}"/>
</tr>
{if $request.category == "company"}
<tr>
	<td>
	</td>
	<td id="linkNextPrevious" align="center">
		<br/><br/>
		<a style="border: 1px solid black;background: #F0F0F0;text-decoration: none;padding: 5px;" onclick="nextTraining();">Next Training</a>
		<br/><br/>
	</td>
</tr>
{elseif $request.category == "facility"}
<tr>
	<td>
	</td>
	<td id="linkNextPrevious" align="center">
		<br/><br/>
		<a style="border: 1px solid black;background: #F0F0F0;text-decoration: none;padding: 5px;" onclick="nextTraining();">Next Training</a>
		<br/><br/>
	</td>
</tr>	
{/if}
</table>

{literal}
	<script>
		var allVal = [];
		var allValCompany = ['login', 'overview', 'report', 'graph', 'payment', 'training', 'npvideo'];
		var allValFacility = ['login', 'overview', 'report', 'graph', 'msds', 'newproduct', 'management', 'eqgraph', 'payment', 'training', 'npvideo'];
		var allValDepartment = ['login', 'overview', 'pfpmix', 'singlemix', 'report', 'msds', 'newproduct', 'management', 'eqgraph', 'training', 'npvideo'];
		function changeColor(val){
			switch (document.getElementById('hiddenPart').value){
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
					document.getElementById(allVal[i]).style.width = '160px';
				} else {
					document.getElementById(allVal[i]).style.border = '1px solid black';
					document.getElementById(allVal[i]).style.background = '#F0F0F0';
					document.getElementById(allVal[i]).style.fontWeight = 'normal';
					document.getElementById(allVal[i]).style.width = '160px';
				}
			}
		}
			
		function previousTraining(){
			switch (document.getElementById('hiddenCategory').value){
				case 'company':
					switch (document.getElementById('hiddenPart').value){
						case 'department':
							document.getElementById('hiddenPart').value = 'facility';	
							$('#tableLink').html(createHtml('facility'));
							$('#linkNextPrevious').html(showNextPreviousLink(true, true));	
							break;
						case 'facility':
							document.getElementById('hiddenPart').value = 'company';	
							$('#tableLink').html(createHtml('company'));
							$('#linkNextPrevious').html(showNextPreviousLink(true, false));	
							break;
						case 'company':
							break;
					}
					break;
				case 'facility':
					switch (document.getElementById('hiddenPart').value){
						case 'department':
							document.getElementById('hiddenPart').value = 'facility';	
							$('#tableLink').html(createHtml('facility'));
							$('#linkNextPrevious').html(showNextPreviousLink(true, false));	
							break;
						case 'facility':
							break;
					}
					break;
			}
			document.getElementById('training').style.border = '3px solid black';
			document.getElementById('training').style.background = '#D0D0D0';
			document.getElementById('training').style.fontWeight = 'bold';
			document.getElementById('training').style.width = '160px';
			$f('player', '../videoTutorial/flowplayer.commercial-3.2.7.swf',
								{
								key: '#$5f3af9a58275bb39d55',
								clip: {
									url: '../videoTutorial/'+document.getElementById('hiddenPart').value+'/training'+document.getElementById('hiddenPart').value.charAt(0).toUpperCase()+document.getElementById('hiddenPart').value.substr(1).toLowerCase()+'.mp4',
									autoPlay: false,
									autoBuffering: true
								}
								});
			$('#caption').html(document.getElementById('hiddenPart').value.charAt(0).toUpperCase()+document.getElementById('hiddenPart').value.substr(1).toLowerCase()+' Level Tutorial');												
		}
		
		function nextTraining(){
			switch (document.getElementById('hiddenCategory').value){
				case 'company':
					switch (document.getElementById('hiddenPart').value){
						case 'company':
							document.getElementById('hiddenPart').value = 'facility';	
							$('#tableLink').html(createHtml('facility'));
							$('#linkNextPrevious').html(showNextPreviousLink(true, true));	
							break;
						case 'facility':
							document.getElementById('hiddenPart').value = 'department';	
							$('#tableLink').html(createHtml('department'));
							$('#linkNextPrevious').html(showNextPreviousLink(false, true));	
							break;
						case 'department':
							break;
					}
					break;
				case 'facility':
					switch (document.getElementById('hiddenPart').value){
						case 'facility':
							document.getElementById('hiddenPart').value = 'department';
							$('#tableLink').html(createHtml('department'));
							$('#linkNextPrevious').html(showNextPreviousLink(false, true));	
							break;
						case 'department':
							break;
					}
					break;
			}
			document.getElementById('training').style.border = '3px solid black';
			document.getElementById('training').style.background = '#D0D0D0';
			document.getElementById('training').style.fontWeight = 'bold';
			document.getElementById('training').style.width = '160px';
			$f('player', '../videoTutorial/flowplayer.commercial-3.2.7.swf',
								{
								key: '#$5f3af9a58275bb39d55',
								clip: {
									url: '../videoTutorial/'+document.getElementById('hiddenPart').value+'/training'+document.getElementById('hiddenPart').value.charAt(0).toUpperCase()+document.getElementById('hiddenPart').value.substr(1).toLowerCase()+'.mp4',
									autoPlay: false,
									autoBuffering: true
								}
								});	
			$('#caption').html(document.getElementById('hiddenPart').value.charAt(0).toUpperCase()+document.getElementById('hiddenPart').value.substr(1).toLowerCase()+' Level Tutorial');						
		}
			
		function createHtml(level){
			var levelAll = [];
			var levelAllKey = [];	
			var levelC = ['How to Login', 'Overview', 'Create Report','Company at a Glance Graphs', 'Payment Process','See Entire Video','New Product Video'];
			var levelF = ['How to Login', 'Overview', 'Create Report', 'Facility at a Glance Graphs', 'How to Manage MSDS & Product Library', 'How to Add a New Product', 'Equipment Management', 'Equipment Graphs', 'Payment Process', 'See Entire Video','New Product Video'];	
			var	levelD = ['How to Login', 'Overview', 'Pre Formaulated Mix', 'Single Mix Input', 'Create Report', 'How to Manage MSDS & Product Library', 'How to Add a New Product', 'Equipment Management', 'Equipment Graphs', 'See Entire Video','New Product Video'];
			var result = '';
			switch (level){
				case 'company':
					levelAll = levelC;
					levelAllKey = allValCompany;	
					break;
				case 'facility':
					levelAll = levelF;	
					levelAllKey = allValFacility; 	
					break;
				case 'department':
					levelAll = levelD;
					levelAllKey = allValDepartment;	
					break;
			}
				for (var i=0; i<levelAll.length; i++){
					result += "<tr>"+
								"<td align=\"center\" id='"+levelAllKey[i]+"' style=\"padding: 10px; margin: 0; border: 1px solid black;\">"+
									"<a style=\"text-decoration: none;\" onClick=\"changeColor('"+levelAllKey[i]+"');$f('player', '../videoTutorial/flowplayer.commercial-3.2.7.swf',"+
										"{"+
										"key: '#$5f3af9a58275bb39d55',"+
										"clip: {"+
											"url: '../videoTutorial/"+level+"/"+levelAllKey[i]+level.charAt(0).toUpperCase()+level.substr(1).toLowerCase()+".mp4',"+
											"autoPlay: false,"+
											"autoBuffering: true"+
										"}"+
										"});\">"+
										levelAll[i]+
									"</a>"+
								"</td>"+
							"</tr>";
				}
			return result;	
		}
			
		function showNextPreviousLink(next, prev){
			var result = '';
			if ((prev) && (next)){
				result = "<br/><br/><a style=\"border: 1px solid black;background: #F0F0F0;text-decoration: none;padding:5px;margin: 5px;\" onclick=\"previousTraining();\">Previous Training</a>"+
						 "<a style=\"border: 1px solid black;background: #F0F0F0;text-decoration: none;padding:5px;margin: 5px;\" onclick=\"nextTraining();\">Next Training</a><br/><br/>";
			} else {		
				if (prev){
					result = "<br/><br/><a style=\"border: 1px solid black;background: #F0F0F0;text-decoration: none;padding:5px;margin: 5px;\" onclick=\"previousTraining();\">Previous Training</a><br/><br/>";
				} else {
					result = "<br/><br/><a style=\"border: 1px solid black;background: #F0F0F0;text-decoration: none;padding:5px;margin: 5px;\" onclick=\"nextTraining();\">Next Training</a><br/><br/>";
				}
			}
			return result;	
		}	
	</script>
	<script>
		$(document).ready(function(){
			document.getElementById('training').style.border = '3px solid black';
			document.getElementById('training').style.background = '#D0D0D0';
			document.getElementById('training').style.fontWeight = 'bold';
			document.getElementById('training').style.width = '160px';
		});
	</script>
{/literal}
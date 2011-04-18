
<script type="text/javascript">	
	var wasteStreamsWithPollutions=eval ("("+'{$wasteStreamsWithPollutions}'+")");	
	var deletedStorageValidationString='{$deletedStorageValidation}';	
	
	var unitTypeClasses=eval ("("+'{$jsTypeEx}'+")");
	var review='{if $data->waste_json}{$data->waste_json}{else}false{/if}';
	var storages=eval ("("+'{$storages}'+")");	
	var storageOverflow='{$storageOverflow}';
	var isMWS = true;		
</script>
<script type="text/javascript" src="modules/js/wasteStreamsCollectionObj.js"></script>
<script type="text/javascript" src="modules/js/pollutionObj.js"></script>
<script type="text/javascript" src="modules/js/wasteStreamsObj.js"></script>	
<script type="text/javascript" src="modules/js/wasteStreams.js"></script>

<input type="hidden" id ="wasteStreamCount" name="wasteStreamCount" value="0">
{*  <input type="hidden" id ="wasteStreamWithoutPollutions" name="wasteStreamWithoutPollutions" value="0">*}

{if $debug}
<input type="button" value="Display Waste JSON" onclick="alert(wasteStreamsCollection.toJson());" />

<a id="generateMix" href="#" onclick="generateLink(); return false;">Generate Link</a>
<a id="addMix" href="" style="display:none;" target="_blank">Add Mix</a>
{/if}

<div id="wasteStreamDiv">
	<table class="users" cellpadding="0" cellspacing="0" align="center" id="wasteStreamTable">
		<tr class="users_u_top_size users_top_lightgray" >
			<td colspan="2">Set waste: <a id='addWasteStream' href='#' onclick="viewWasteStreams(); return false;">add waste stream</a></td>
			<td>
			<div id="wasteValidError" style="width:680px;margin:2px 0px 0px 5px;display:none;" ><img src='design/user/img/alert1.gif' height=16  >			
							<font style="vertical-align:bottom;color:red;margin:1px 0px 0px 5px;"></font></div>
				{if $validStatus.summary eq 'false'}				
					{if $validStatus.waste.percent eq 'failed'}				
						{*ERORR*}			
							<div style="width:680px;margin:2px 0px 0px 5px;" ><img src='design/user/img/alert1.gif' height=16  >			
							<font style="vertical-align:bottom;color:red;margin:1px 0px 0px 5px;">Error! Please enter valid waste value. VOC was calculated with waste = 0. Waste value must be less than products total value.</font></div>			
						{*/ERORR*}			
					{/if}				
					{if $validStatus.waste.error neq null}				
						{*ERORR*}			
							<div style="width:680px;margin:2px 0px 0px 5px;" align="left"><img src='design/user/img/alert1.gif' height=16  >			
							<font style="vertical-align:bottom;color:red;margin:1px 0px 0px 5px;">{$validStatus.waste.error}</font></div>			
						{*/ERORR*}			
					{/if}	
					{if $storageError neq null}				
						{*ERORR*}			
							<div style="width:680px;margin:2px 0px 0px 5px;" align="left"><img src='design/user/img/alert1.gif' height=16  >			
							<font style="vertical-align:bottom;color:red;margin:1px 0px 0px 5px;">{$storageError}</font></div>			
						{*/ERORR*}			
					{/if}			
				{/if}				
			</td>			
		</tr>
	</table>
</div>

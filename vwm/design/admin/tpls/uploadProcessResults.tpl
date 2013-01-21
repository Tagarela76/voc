<div align="center">
<div style="width:95%">
	
<div  style="background:#D3DAE2;padding:7px;">{$validationResult}</div>
<br>
<div style="color:red">Process with errors:</div>
<br>
<table width="100%" cellpadding=0 cellspacing=0 >

	<tr>
		<td><b>{$processErrorNames}</b><td>
	</tr>
	<tr>
		<td>{$errorComents}</td>
	</tr>

	<tr>
		<td><b>Total of errors</b><td>
		<td>{$errorCnt}</td>
	</tr>
	<tr>
		<td><b>Total of correct</b><td>
		<td>{$correctCnt}</td>
	</tr>
	<tr>
		<td><b>Total</b><td>
		<td>{$total}</td>
	</tr>
</table>

<br>
<div style="background:#D3DAE2;padding:7px;display:table;width:100%" align="center">
<div style="display:table;">
	
	<div align="left" style="padding-left:10px;">
		Number of inserted Processes: {$processAction.savedProcesses|@count} <br>
		Number of updated Processes: {$processAction.updateProcess|@count}<br>
		Number of not saved Processes: {$processAction.notSavedProcess|@count}<br>
	</div>
	
<br>
<div style="float:left;"><a href="../voc_logs/actions.log" class="button_70" target="_blank">Action log</a></div>
<div style="float:left;"><a href="../voc_logs/validation.log" class="button_70" target="_blank">Validation log</a></div>
</div>
</div>


</div>
</div>
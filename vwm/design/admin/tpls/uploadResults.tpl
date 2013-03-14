<div align="center">
<div style="width:95%">
	
<div  style="background:#D3DAE2;padding:7px;">{$validationResult}</div>
<br>
<div style="color:red">Products with errors:</div>
<br>
<table width="100%" cellpadding=0 cellspacing=0 >
{section name=i loop=$productsError}
	<tr>
		<td><b>{$productsError[i].productId}</b><td>
		<td>{$productsError[i].errorComments}</td>
	</tr>
{/section}
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
{if $insertedCnt>0 or $updatedCnt>0}
	{$actions}
{else}
	{if $isPFP and $isPFP == 'Startpfp'}
	Number of inserted PFPs: {$insertedCnt} <br>
	Number of updated PFPs: {$updatedCnt}<br>

	{else}	
	Number of inserted products: {$insertedCnt} <br>
	Number of updated products: {$updatedCnt}<br>
{/if}
{/if}
</div>
<br>
<div style="float:left;"><a href="../voc_logs/actions.log" class="button_70" target="_blank">Action log</a></div>
<div style="float:left;"><a href="../voc_logs/validation.log" class="button_70" target="_blank">Validation log</a></div>
</div>
</div>


</div>
</div>
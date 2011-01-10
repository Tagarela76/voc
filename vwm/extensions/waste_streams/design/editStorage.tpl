{if $color eq "green"}
	{include file="tpls:tpls/notify/greenNotify.tpl" text=$message}
{/if}
{if $color eq "orange"}
	{include file="tpls:tpls/notify/orangeNotify.tpl" text=$message}
{/if}
{if $color eq "blue"}
	{include file="tpls:tpls/notify/blueNotify.tpl" text=$message}
{/if}


{if $data->active!='0' && $request.action=='edit'}
	<form name="emptyWasteStorage" action = "?action=deleteItem&category=wastestorage&facilityID={$request.facilityID}&id={$request.id}&empty=1" method = "post">
		<div style='padding-left:20px'>	
			<input type='submit' class='button' id='empty' value='Empty'/>
			<input type='text' name='dateEmpty' id='calendar'/>
		</div>
	</form>
{/if}

<form name="editStorage" action={if $request.action == 'edit'}"?action=edit&category=wastestorage&facilityID={$request.facilityID}&id={$request.id}"{else}"?action=addItem&category=wastestorage&facilityID={$request.facilityID}"{/if} method="POST">

<br>
<table class="users" align="center" cellpadding="0" cellspacing="0">
	<tr class="users_u_top_size users_top_green">	
		<td class="users_u_top_green" >	
			<span>Edit Storage</span>	
		</td>	
		<td class="users_u_top_r_green">
			&nbsp;	
		</td>	
	</tr>
	<tr>
		<td height="20" class="border_users_l border_users_b border_users_r">Name</td>
		<td class="border_users_b border_users_r" align='left'>
			<input type='text' name='name' value='{$data->name}'/>			
			{*ERORR*}
			{if $validation.summary=='failed'}
				{if $validation.name!=null}	
					<br>				
					<div class="error_img"><span class="error_text">{$validation.name}</span></div>						
				{/if}
			{/if}	
			{*/ERORR*}	 			
			&nbsp;
		</td>
	</tr>
	<tr>
		<td height="20" class="border_users_l border_users_b border_users_r">Capacity (volume)</td>
		<td class="border_users_b border_users_r" align='left'>
			<input type='text' name='capacity_volume' value='{$data->capacity_volume}'/>
			<select name='selectVolumeUnittype'>
				{section name=i loop=$volumeUnittypes}
					<option value='{$volumeUnittypes[i].id}' {if $volumeUnittypes[i].id eq  $data->volume_unittype}selected="selected"{/if}>{$volumeUnittypes[i].name}</option>
				{/section}
			</select>			
			{*ERORR*}
			{if $validation.summary=='failed'}
				{if $validation.capacity_volume!=null}
					<br>					
					<div class="error_img"><span class="error_text">{$validation.capacity_volume}</span></div>						
				{/if}
			{/if}	
			{*/ERORR*}	 
			&nbsp;
		</td>
	</tr>
	<!--<tr>
		<td height="20" class="border_users_l border_users_b border_users_r">Capacity (weight)</td>
		<td class="border_users_b border_users_r" align='left'>
			<input type='text' name='capacity_weight' value='{$data->capacity_weight}'/>
			<select name='selectWeightUnittype'>
				{section name=i loop=$weightUnittypes}
					<option value='{$weightUnittypes[i].id}'  {if $weightUnittypes[i].id eq  $data->weight_unittype}selected="selected"{/if}>{$weightUnittypes[i].name}</option>
				{/section}
			</select>			
			{*ERORR*}
			{if $validation.summary=='failed'}
				{if $validation.capacity_weight!=null}
					<br>					
					<div class="error_img"><span class="error_text">{$validation.capacity_weight}</span></div>											
				{/if}
			{/if}	
			{*/ERORR*}	 
			&nbsp;
		</td>
	</tr>-->
	<tr>
		<td height="20" class="border_users_l border_users_b border_users_r">Density</td>
		<td class="border_users_b border_users_r" align='left'>
			<input type='text' name='density' value='{$data->density}'>
			<select id="selectDensityType" name="selectDensityType" style="width:108px">
				{section name=i loop=$densityDetails}	
					<option value='{$densityDetails[i].id}' {if $densityDetails[i].id eq $densityDefault}selected='selected'{/if}>{$densityDetails[i].numerator}/{$densityDetails[i].denominator}</option>										
				{/section}
			</select>			
			{*ERORR*}
			{if $validation.summary=='failed'}
				{if $validation.density!=null}
					<br>					
					<div class="error_img"><span class="error_text">{$validation.density}</span></div>											
				{/if}
			{/if}	
			{*/ERORR*}	 
			&nbsp;
		</td>
	</tr>
	<tr>
		<td height="20" class="border_users_l border_users_b border_users_r">Max Period</td>
		<td class="border_users_b border_users_r" align='left'>
			<input type='text' name='max_period' value='{$data->max_period}'/> days				
			{*ERORR*}
			{if $validation.summary=='failed'}
				{if $validation.max_period!=null}				
					<br>	
					<div class="error_img"><span class="error_text">{$validation.max_period}</span></div>						
				{/if}
			{/if}	
			{*/ERORR*}	 		
		</td>
	</tr>
	<tr>
		<td height="20" class="border_users_l border_users_b border_users_r">Suitability</td>
		<td class="border_users_b border_users_r" align='left'>
			{if $request.action eq 'addItem'}
				<select name='selectSuitability'>
					{section name=i loop=$suitability}
						<option value='{$suitability[i].id}' {if $suitability[i].id eq  $data->suitability}selected="selected"{/if}>{$suitability[i].name}</option>
					{/section}
				</select>
			{/if}
			{if $request.action eq 'edit'}
				{$suitability}
			{/if}
			
			&nbsp;
		</td>
	</tr>
	{if $show.docs}
		<tr>
		<td height="20" class="border_users_l border_users_b border_users_r">Document</td>
		<td class="border_users_b border_users_r" align='left'>
			<div id='doc_select' onclick="showDir('doc')">Select..</div>
			<div id="doc" style="display:none;">
			{include file="tpls:docs/design/documentsList.tpl"}
			</div>
			&nbsp;
		</td>
	</tr>
	{/if}
	<tr>
	 	<td height="20" class="users_u_bottom">&nbsp;</td>
		<td height="20" class="users_u_bottom_r">&nbsp;</td>
	</tr>	
</table>

<br>
<div width='100%' align='right' style='padding-right:50px'>
	<input type='submit' class='button' name='save' value='Save'/>
	<input type='button' class='button' id='cancel' value='Cancel' onclick='location.href="?action={if $request.action eq 'edit'}viewDetails&category=wastestorage&facilityID={$request.facilityID}&id={$request.id}{else}browseCategory&category=facility&id={$request.facilityID}&bookmark=wastestorage&tab=active{/if}"'/>	
</div>

</form>

<script type="text/javascript" src="modules/js/jquery-ui-1.8.2.custom/js/jquery-1.4.2.min.js"></script>
<script type="text/javascript" src="modules/js/jquery-ui-1.8.2.custom/js/jquery-ui-1.8.2.custom.min.js"></script>
{literal}
<script type="text/javascript">
	$(document).ready(function()
	{
		date = new Date();
		var month = date.getMonth() + 1
		var day = date.getDate()
		var year = date.getFullYear()
		var dateString=month + "/" + day + "/" + year;
		$('#calendar').attr('value',dateString);
		$('#calendar').datepicker({ dateFormat: 'mm/dd/yy'});
	});
</script>
{/literal}	
	{if $color eq "green"}
		{include file="tpls:tpls/notify/greenNotify.tpl" text=$message}
	{/if}
	{if $color eq "orange"}
		{include file="tpls:tpls/notify/orangeNotify.tpl" text=$message}
	{/if}
	{if $color eq "blue"}
		{include file="tpls:tpls/notify/blueNotify.tpl" text=$message}
	{/if}
	
<div class="padd7">
	{if $request.action == 'addItem'}
		<form action='admin.php?action=addItem&category=emissionFactor' method="post">
	{else}
		<form action='admin.php?action=edit&category=emissionFactor&id={$emissionFactor->id}' method="post">
	{/if}
	
		<table class="users addCompany" cellpadding="0" cellspacing="0" align="center">
			<thead>
				<tr class="users_u_top_size users_top">
					<td class="users_u_top"><span><b>{if $request.action == 'addItem'}Adding{else}Editing{/if} Emission Factor</b></span></td>
					<td class="users_u_top_r">&nbsp;</td>		
				</tr>
			</thead>
			<tbody>
				<tr class="border_users_b border_users_r">
					<td class="border_users_l" height="20" width="15%">Name</td>
					<td>
						<div align="left">
							<input id='emissionFactorName' type='text' name='name' value='{$emissionFactor->name}'>
						</div>			
						
						{if $validStatus.summary eq 'false'}
						{if $validStatus.name eq 'failed'}		
			     		{*ERROR*}					
							<div id="error_name" class="error_img"><span class="error_text">Error!</span></div>					    												
						{*/ERROR*}
						{/if}
						{/if}					
					</td>
				</tr>
				
				<tr class="border_users_b border_users_r" height="10px">
					<td class="border_users_l" >Unittype</td>
					<td>
						<div align="left">
							<select name="unittypeID">
								<optgroup label="Volume">
									{foreach from=$volumeUnittypes item=unittype}
										<option value="{$unittype.id}" {if $unittype.id == $emissionFactor->unittype_id} selected {/if}>{$unittype.description} ({$unittype.name})</option>
									{/foreach}
								</optgroup>
								<optgroup label="Weight">
									{foreach from=$weightUnittypes item=unittype}
										<option value="{$unittype.id}" {if $unittype.id == $emissionFactor->unittype_id} selected {/if}>{$unittype.description} ({$unittype.name})</option>
									{/foreach}
								</optgroup>
								<optgroup label="Energy">
									{foreach from=$energyUnittypes item=unittype}
										<option value="{$unittype.id}" {if $unittype.id == $emissionFactor->unittype_id} selected {/if}>{$unittype.description} ({$unittype.name})</option>
									{/foreach}
								</optgroup>												
							</select>							
						</div>								     		
					</td>
				</tr>
				
				<tr class="border_users_b border_users_r" height="10px">
					<td class="border_users_l">Value</td>
					<td>
						<div align="left">
							<input id='emissionFactorValue' type='text' name='emissionFactor' value='{$emissionFactor->emission_factor}'>
							{if $validStatus.summary eq 'false'}
							{if $validStatus.emissionFactor eq 'failed'}		
							{*ERROR*}					
							<div id="error_emissionFactor" class="error_img"><span class="error_text">Error!</span></div>					    													
							{*/ERROR*}			
							{/if}
							{/if}
						</div>								     		
					</td>
				</tr>			
			</tbody>
			<tfoot>			
				<tr class="border_users_l border_users_r">
					<td colspan="2">&nbsp;</td>
				</tr>			
				<tr>
					<td height="20" class="users_u_bottom">&nbsp;</td>
					<td height="20" class="users_u_bottom_r">&nbsp;</td>
				</tr>
			</tfoot>					
			
		</table>
		
		<table cellpadding="5" cellspacing="0" align="center" width="95%">
			{*BUTTONS*}
			<tr>
				<td>				
					<div align="right">
						<input type='button' name='cancel' class="button" value='Cancel' onClick="location.href='admin.php?action=browseCategory&category=tables&bookmark=emissionFactor'">
						<input type='submit' name='save' class="button" value='Save'>						
					</div>							
				</td>
			</tr>
		</table>
	</form>
</div>
{*SELECT_APMethods_POPUP*}
<div id="APMethodsList" title="Select Default AP Methods">
	<table width="350px" cellpadding="0" cellspacing="0" class="popup_table" align="center" >
		<tr>
			<td class="control_list" colspan="3" style="border-bottom:0px solid #fff;padding-left:0px">
				Select: <a onclick="CheckClassOfUnitTypes(document.getElementById('popup_table_APMethod'))" name="allRules" class="id_company1">All</a>
				/<a onclick="unCheckClassOfUnitTypes(document.getElementById('popup_table_APMethod'))" name="allRules" class="id_company1">None</a>
			</td>
		</tr>
	</table>
	<table width="350px" cellpadding="0" cellspacing="0" class="popup_table" align="center" id="popup_table_APMethod">
		<tr class="table_popup_rule">                            
			<td align="center" style="width:150px">
				Select
			</td>                            
			<td>
				Description
			</td>
		</tr>
		<tr>
			<td colspan="2" style="padding:0px;border-bottom:0px;"> 
				<table cellpadding=0 cellspacing=0 width="100%">
					{section name=i loop=$APMethodList}

						<tr name="APMethodlist" id="row_{$smarty.section.i.index}">                                        
							<td align="center" style="width:150px">
								<input type="checkbox" id="APMethodID[]" name="APMethodID[]" value="{$APMethodList[i].apmethod_id}"
									   {section  name=j loop=$defaultAPMethodlist}
										   {$defaultAPMethodlist[j]}
										   {if $APMethodList[i].apmethod_id  == $defaultAPMethodlist[j]}
											   checked 
										   {/if}
									   {/section}
									   >											
							</td>

							<td>
								{$APMethodList[i].description}
							</td>
						</tr>                                    								
					{/section}                                                       			
				</table>
			</td>
		</tr>
	</table>
	<input id="APMethodName" type="hidden" name="APMethodName" value="{$request.category}">
	{if $request.action == 'edit'}
		<input id="editAPMethodName" type="hidden" name="editAPMethodName" value="{$companyID}">
	{/if} 
	<input id="APMethodCount" type="hidden" name="APMethodCount" value="{$smarty.section.i.index}">
</div>
{*END OF POPUP*}
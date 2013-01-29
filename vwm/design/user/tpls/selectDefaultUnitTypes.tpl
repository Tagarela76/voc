<div id="UnitTypelist" title="Select Default Unit Types">
                    <table width="750px" cellpadding="0" cellspacing="0" class="popup_table" align="center" >
                        <tr>
                            <td class="control_list" colspan="3" style="border-bottom:0px solid #fff;padding-left:0px">
                                Select: <a onclick="CheckClassOfUnitTypes(document.getElementById('popup_table_unittype'))" name="allRules" class="id_company1">All</a>
                                /<a onclick="unCheckClassOfUnitTypes(document.getElementById('popup_table_unittype'))" name="allRules" class="id_company1">None</a>
                           </td>
                        </tr>
                    </table>
					<div>
                    <table width="750px" cellpadding="0" cellspacing="0" class="popup_table" align="center" id="popup_table_unittype">
                        <tr class="table_popup_rule">
                            <td style="width:100px">
                                Class
                            </td>
                            <td align="center" style="width:150px">
                                Select
                            </td>
                            <td style="width:20%">
                                UnitType
                            </td>
                            <td>
                                Description
                            </td>
                        </tr>
                        <tr>
                            <td colspan="4"  style="padding:0px;border-bottom:0px;">
                                {section name=k loop=$classlist}
                                <table id="class_{$smarty.section.k.index}" name="class_{$smarty.section.k.index}" width="100%" cellpadding=0 cellspacing=0>
                                    <tr>
                                        <td style="width:100px;">
                                            <strong>{$classlist[k].description}</strong>
                                        </td>
                                        <td style="width:150px" align="center">
                                            <a onclick="CheckClassOfUnitTypes(document.getElementById('class_{$smarty.section.k.index}'))">all</a>/<a onclick="unCheckClassOfUnitTypes(document.getElementById('class_{$smarty.section.k.index}'))">none</a>
                                        </td>
                                        <td colspan="2">
                                        </td>
                                    </tr>
                                    {section name=i loop=$unitTypelist}
                                    {if ($classlist[k].id == $unitTypelist[i].unit_class_id)}
                                    <tr name="unitTypelist" id="row_{$smarty.section.i.index}">
                                        <td style="width:100px">
                                        </td>
                                        <td align="center" style="width:150px">
                                            <input type="checkbox" id="unitTypeID[]" name="unitTypeID[]" value="{$unitTypelist[i].unittype_id}"
												{section  name=j loop=$defaultUnitTypelist}
													{$defaultUnitTypelist[j]}
													{if $unitTypelist[i].unittype_id  == $defaultUnitTypelist[j]}
													 checked 
													{/if}
												{/section}
											>											
                                        </td>
                                        <td id="UnitTypeName_{$smarty.section.i.index}" style="width:20%">
                                            {$unitTypelist[i].name}
                                        </td>
                                        <td>
                                            {$unitTypelist[i].unittype_desc}
                                        </td>
                                    </tr>
                                    {/if}									
                                    {/section}									
                                </table>
                                {/section}
								
                            </td>
                        </tr>
                    </table>
                    <input id="categoryName" type="hidden" name="categoryName" value="{$request.category}">{if $request.action == 'edit'}<input id="companyID" type="hidden" name="companyID" value="{$companyID}">{/if} <input id="unitCount" type="hidden" name="unitCount" value="{$smarty.section.i.index}">

    </div>
</div>
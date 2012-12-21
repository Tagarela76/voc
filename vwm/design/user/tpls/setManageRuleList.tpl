<div id="ruleList" title="Choose Rules">

            <form>
                <table width="750px" cellpadding="0" cellspacing="0" class="popup_table" align="center">
                    <tr>
                        <td class="control_list" style="border-bottom:0px solid #fff;padding-left:0px">
                            Select: <a onclick="CheckAll(this)" name="allRules" class="id_company1">All</a>
                            /<a onclick="unCheckAll(this)" name="allRules" class="id_company1">None</a>
                        </td>
                        <td colspan="2" style="border-bottom:0px solid #fff">
                            <div>
                                I am customizing this rule list as {$categoryName}
								<!--<span style="padding:0px 3px;"><input type="radio" name="role" value="user" ></span>user-->
								<span style="padding:0px 3px;"><input type="hidden" name="role" value="{$categoryName}" checked></span>
                            </div>
                        </td>
                    </tr>

                    <tr class="table_popup_rule">
                        <td align="center">
                            Select
                        </td>
                        <td style="width:20%">
                            Rule
                        </td>
                        <td>
                            Description
                        </td>
                    </tr>
                    {section name=i loop=$ruleList}
                    <tr name="ruleList" id="row_{$smarty.section.i.index}">
                        <td align="center">
                            <input type="checkbox" name="ruleID" value="{$ruleList[i].rule_id}"
								{section  name=j loop=$customizedRuleList}
									{if $ruleList[i].rule_id  == $customizedRuleList[j].rule_id}
 										checked
									{/if}
								{/section}>
                        </td>
                        <td id="ruleName_{$smarty.section.i.index}">
                            {$ruleList[i].rule_nr}&nbsp;
                        </td>
                        <td>
                            {$ruleList[i].description}&nbsp;
                        </td>
                    </tr>
                    {/section}
                </table>
				<input id="userID" type="hidden" name="userID" value="{$userID}">
				<input id="categoryRuleListName" type="hidden" name="categoryName" value="{$categoryName}">
				
				
				{if $categoryName == 'facility' || $categoryName == 'department'}
				<input id="categoryNameRuleListID" type="hidden" name="categoryNameID" value="{$facilityID}">
				{elseif $categoryName == 'company'}
				<input id="categoryNameRuleListID" type="hidden" name="categoryNameRuleListID" value="{$companyID}">
				{/if}
            </form>

</div>
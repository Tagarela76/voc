
<table id="typesClassList" width="750px" cellpadding="0" cellspacing="0" class="popup_table" align="center" >
	<tr>
		<td class="control_list" colspan="2" style="border-bottom:0px solid #fff;padding-left:0px">
			Select:
			<a onclick="CheckAll(this)" name="allTypesClasses" class="id_company1" >All</a>
			/
			<a onclick="unCheckAll(this)" name="allTypesClasses" class="id_company1">None</a>
		</td>
	</tr>

	<tr class="table_popup_rule">
		<td>
			Select
		</td>
		<td>
			Name
		</td>
	</tr>

	{foreach from=$productTypeList item=type key=k}
		<tr>
			<td align="center" style="width:150px">
				<input type="checkbox"  value="{$type.id}"
					   {foreach from=$productTypes item=productType key=j}
					   {if $type.id eq $j} checked {/if}
				{/foreach}
				/>
		</td>
		<td id="category_{$type.id}">
			<b>{$k}&nbsp;</b>
		</td>
	</tr>
	{foreach from=$type.subTypes item=subType key=i}
		<tr>
			<td align="center" style="width:150px">
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="checkbox"  value="{$i}"
																			 {foreach from=$productTypes item=productType key=j}
																			 {if $i eq $j} checked {/if}
				{/foreach}
				/>
		</td>
		<td id="category_{$i}">
			{$subType}&nbsp;
		</td>
	</tr>
{/foreach}
<tr>
	<td colspan="2"><hr/></td>
</tr>
{/foreach}
</table>
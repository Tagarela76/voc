<table class="popup_table" align="left" cellspacing="0" cellpadding="0">
<tr>
	<td>
		<div id="usersListContainer">
			{foreach from=$productTypeList item=type key=k}
				<tr>
					<td align="center" style="width:150px">
						<input type="checkbox"  value="{$type.id}"
							   {foreach from=$productTypes item=productType key=j}
								   {if $type.id eq $productType.industry_type_id} checked {/if}
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
																							{if $i eq $productType.industry_type_id} checked {/if}
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
					<input type="hidden" name="page" value="{$page}"/>
				</tr>
			{/foreach}
		</div>
	</td>
</tr>
</table>
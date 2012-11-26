<table class="popup_table" align="left" cellspacing="0" cellpadding="0">
	<tr align="center" style="width:150px">
		<td class="control_list" colspan="3" style="border-bottom:0px solid #fff;padding-left:0px">
			Select: <a onclick="CheckAll(this)" name="allColumnsDisplayList" class="id_company1">All</a>
			/<a onclick="unCheckAll(this)" name="allColumnsDisplayList" class="id_company1">None</a>
		</td>
	</tr>
	<tr>
		<td>
			<div>
				{foreach from=$columnsDefaultDisplay key=columnId item=columnDefault}
					<tr>
						<td align="center" style="width:150px">
							<input type="checkbox" value="{$columnId}"
								   {foreach from=$columnsDisplay item=column}
									   {if $columnId eq $column} checked {/if}
								   {/foreach}
							/>
						</td>
						<td>
							<b>{$columnDefault}&nbsp;</b>
						</td>
					</tr>
				{/foreach}
			</div>
		</td>
	</tr>
</table>
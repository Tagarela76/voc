<table width="750px" cellpadding="0" cellspacing="0" class="popup_table" align="center">
    <tr class="table_popup_rule">
		<td>
			Select
		</td>
		<td>
			Name
		</td>
	</tr>
    {foreach from=$libraryTypesList item=libraryType key=i}
        <tr>
            <td align="center" style="width:150px">
                <input type="checkbox"  value="{$libraryType->id}" id="checkBox_{$i}" {if $libraryType->checked}checked{/if}>
            </td>
             <td align="center" style="width:150px">
                {$libraryType->name}
            </td>
        <tr>
        {/foreach}
</table>
<input type="hidden" value="{$countLibraryTypes}" id='countLibraryTypes'>
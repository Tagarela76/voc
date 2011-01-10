<div class="button_float_left">			
		{if $request.tab eq 'active'}	
			<input type='button' class='button' id='add' value='Add' onclick="location.href='?action=addItem&category=wastestorage&facilityID={$request.id}'"/>					
			<input type='submit' class='button' id='empty' name="empty" value='Empty' onclick="location.href='?action=deleteItem&category=wastestorage&facilityID={$request.id}'"/>
			<input type='text' name='dateEmpty' id='calendar'>
			&nbsp;&nbsp;&nbsp;&nbsp;
		{/if}
		<input type='submit' class='button' {if $request.tab eq 'active'}name='delete' value='Delete'{else}name='restore' value='Restore'{/if}/>
		{if $request.tab eq 'active'}	
			<input type='text' name='dateDeleted' id='calendar2'>
		{/if}
</div>
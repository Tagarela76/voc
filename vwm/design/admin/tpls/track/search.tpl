<div style="text-align:center;">	
	
    <div>
        
            <input type="hidden" name="action" value="search">
            <label for="searchTrack">
                <input type="text" id="searchTrack" style="width:410px;border:1px solid #D3D3D3;height:20px;" title="searchTrack" value='{$searchText}'/>
				<span style="position:absolute;left:5px;top:1px;"></span>
            </label>
            <input type="button" id="goSearchTrack" class="button" value="Search">
        
    </div>
 
	<br>
	<div id ='trackTable'>
		  &nbsp;
	</div>
	<br>
	<div>
		{if $showDependencies && !$integrityError && !$juniorTrashError}
		<input type="button" value="Cancel" class="button" onclick="loadLastTracks();">
		<input id="rollbackButton" type="button" value="Yes, rollback all" class="button" onclick="areYouSure();">
		{/if}
	</div>
</div>
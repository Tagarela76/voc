<div style="text-align:center;">	
	<div id="notify" style="display:none;">			
		{include file="tpls:tpls/notify/orangeNotify.tpl" text="Are you sure you want to rollback <b>$trashRecordLabel</b> with all dependencies?"}		
	</div>
	<div><h1>Confirm rollback</h1></div>
       <div> <table width="600px" align="center" border="1">
            <tr style="background-color:red;">
                <td colspan="6">
                	Are you sure you want to rollback <b>{$trashRecordLabel}</b>  with all dependencies?
                </td>                
            </tr>
            <tr  style="background-color:#D3D3D3;">
              	<td>
        			ID
        		</td>
				<td>
        			Type
        		</td>
				<td>
        			Item
        		</td>
				<td>
        			Item Name (ID)
        		</td>
				<td>
        			User
        		</td>
				<td>
        			Date
        		</td>
            </tr>			
    		<tr>
        		<td>
        			<b>{$parentTrashRecord.id}</b>
        		</td>
				<td>
        			<b>{$parentTrashRecord.type}</b>
        		</td>
				<td>
        			<b>{$parentTrashRecord.item}</b>
        		</td>
				<td>
        			<b>{$parentTrashRecord.itemName}</b>
        		</td>
				<td>
        			<b>{$parentTrashRecord.user}</b>
        		</td>
				<td>
        			<b>{$parentTrashRecord.date}</b>
        		</td>			
    		</tr>
			{foreach from=$trashRecords item=trashRecord}
    		<tr>
        		<td>
        			{$trashRecord.id}
        		</td>
				<td>
        			{$trashRecord.type}
        		</td>
				<td>
        			{$trashRecord.item}
        		</td>
				<td>
        			{$trashRecord.itemName}
        		</td>
				<td>
        			{$trashRecord.user}
        		</td>
				<td>
        			{$trashRecord.date}
        		</td>			
    		</tr>
			{/foreach}
        </table></div>
		<br />
	<div>		
		<input type="button" value="Cancel" class="button" onclick="loadSearchTemplate();loadLastTracks();">
		<input id="confirmButton" type="button" value="Yes, rollback" class="button" onclick="iAmSure();">	
	</div>
</div>
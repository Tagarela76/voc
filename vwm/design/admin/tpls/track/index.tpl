<div id="notify" style="display:none;">
	{if $showDependencies}
			{if $juniorTrashError}
				{include file="tpls:tpls/notify/orangeNotify.tpl" text="Can not rollback <b>$trashRecordLabel</b><br>beacuse there are more recent actions with this item"}
			{elseif $integrityError}
				{include file="tpls:tpls/notify/orangeNotify.tpl" text="Can not rollback <b>$trashRecordLabel</b><br>Please, rollback following actions first"}
			{else}			
				{include file="tpls:tpls/notify/orangeNotify.tpl" text="You are trying to rollback <b>$trashRecordLabel</b><br>There was some actions linked with this item. Rollback these actions too?"}
			{/if}
	{/if}			
    </div>	
{if $showDependencies}
		<div style="text-align:center; width:100%"><h1>Dependencies List</h1></div>
{else}
	<div style="text-align:center; width:100%">
        <h1>Tracking System</h1>		
    </div>
{/if}  	
<table class="users addCompany" cellpadding="0" cellspacing="0" align="center">
			<tr class="users_top_red users_u_top_size">
        		<td  class="users_u_top_red">
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
			{if !$showDependencies || $integrityError || $juniorTrashError}
				<td  class="users_u_top_r_red">
        			Rollback
        		</td>
			{/if}
    		</tr>
			{section loop=$trashRecords name=i}
    		<tr class="border_users_b border_users_r">
        		<td  class="border_users_l">
        			{$trashRecords[i].id}
        		</td>
				<td>
        			{$trashRecords[i].type}
        		</td>
				<td>
        			{$trashRecords[i].item}
        		</td>
				<td>
        			{$trashRecords[i].itemName}
        		</td>
				<td>
        			{$trashRecords[i].user}
        		</td>
				<td>
        			{$trashRecords[i].date}
        		</td>
			{if !$showDependencies || $integrityError || $juniorTrashError}
				<td>
        			<a href="javascript:rollback({$trashRecords[i].id});">rollback</a>
        		</td>
			{/if}
    		</tr>
			{/section}
			{if $smarty.section.i.total ==0}
				<tr align = 'center'>						
					<td class="border_users_l border_users_b border_users_r" colspan='7'>
						No records
					</td>						
				</tr>
			{/if}
				<tr>
				<td height="20" class="users_u_bottom">&nbsp;</td>
				<td height="20" class="users_u_bottom_r" colspan="6">&nbsp;</td>
			</tr>
</table>
<div style="width:100%;text-align:right;margin-top:10px;">
		{if $showDependencies && !$integrityError && !$juniorTrashError}
		<input type="button" value="Cancel" class="button" onclick="loadLastTracks();">
		<input id="rollbackButton" type="button" value="Yes, rollback all" class="button" onclick="areYouSure();">
		{/if}
</div>
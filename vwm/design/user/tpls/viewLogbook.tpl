{if $color eq "green"}
{include file="tpls:tpls/notify/greenNotify.tpl" text=$message}
{/if}
{if $color eq "orange"}
{include file="tpls:tpls/notify/orangeNotify.tpl" text=$message}
{/if}
{if $color eq "blue"}
{include file="tpls:tpls/notify/blueNotify.tpl" text=$message}
{/if}


<div class="padd7">
	<table class="users" align="center" cellpadding="0" cellspacing="0">
        <tr class="users_top_yellowgreen users_u_top_size">
            <td class="users_u_top_yellowgreen" width="27%">
                <span>Logbook record #{$data->id}</span>
            </td>
            <td class="users_u_top_r_yellowgreen" width="300">
            </td>
        </tr>
		<tr>
			<td class="border_users_l border_users_b border_users_r" height="20">Date</td>
			<td class="border_users_b border_users_r">
				{$data->date}&nbsp;			
			</td>
		</tr>
		
		{if $logbookType=="Filter"}
			<tr id="tr_installed">
				<td class="border_users_l border_users_b border_users_r" height="20">Installed</td>
				<td class="border_users_b border_users_r">
				 	{if $data->installed eq true}Yes{else}No{/if}			
				</td>
			</tr>
			
			<tr id="tr_removed">
				<td class="border_users_l border_users_b border_users_r" height="20">Removed</td>
				<td class="border_users_b border_users_r">
				 	{if $data->removed eq true}Yes{else}No{/if}			
				</td>
			</tr>	
				
			<tr id="tr_filterType">
				<td class="border_users_l border_users_b border_users_r" height="20">Filter type</td>
				<td class="border_users_b border_users_r">
					 {$data->filter_type}&nbsp;		
				</td>
			</tr>
			
			<tr id="tr_filterSize">
				<td class="border_users_l border_users_b border_users_r" height="20">Filter size</td>
				<td class="border_users_b border_users_r">
					 {$data->filter_size}&nbsp;			
				</td>
			</tr>
		{/if}
		
		{if ($logbookType=="Sampling")||($logbookType=="Inspection") }
			<tr id="tr_description">
				<td class="border_users_l border_users_b border_users_r" height="20">Description</td>
				<td class="border_users_b border_users_r">
					{$data->description}&nbsp;		
				</td>
			</tr>
		{/if}
		
		{if ($logbookType=="Malfunction")||($logbookType=="Sampling")||($logbookType=="Inspection") }
			<tr id="tr_operator">
				<td class="border_users_l border_users_b border_users_r" height="20">Operator</td>
				<td class="border_users_b border_users_r">
					{$data->operator}&nbsp;		
				</td>
			</tr>
		{/if}
		
		{if $logbookType=="Malfunction"}
			<tr id="tr_reason">
				<td class="border_users_l border_users_b border_users_r" height="20">Reason</td>
				<td class="border_users_b border_users_r">
					{$data->reason}&nbsp;		
				</td>
			</tr>
		{/if}
		
		{if $logbookType=="Sampling"}
			<tr id="tr_action">
				<td class="border_users_l border_users_b border_users_r" height="20">Action</td>
				<td class="border_users_b border_users_r">
					{$data->action}&nbsp;			
				</td>
			</tr>
		{/if}
		
		{if $logbookType!="AccidentPlan"}
			<tr id='tr_department'>
				<td class="border_users_l border_users_b border_users_r" height="20">Department</td>
				<td class="border_users_b border_users_r">				
					{$data->department_name}&nbsp;									
				</td>
			</tr>
			
			<tr id='tr_equipment'>
				<td class="border_users_l border_users_b border_users_r" height="20">Equipment</td>
				<td class="border_users_b border_users_r">
					{$data->equipment_id}&nbsp;				
				</td>
			</tr>
		{/if}
		
		{if $logbookType=="AccidentPlan"}
			<tr id='tr_upload'>
				<td class="border_users_l border_users_b border_users_r" height="20">Document Link</td>
				<td class="border_users_b border_users_r">
					<a href='{$data->link}'>document</a>&nbsp;		
				</td>
			</tr> 
		{/if}
		       
        <tr>
            <td height="20" class="users_u_bottom">
            </td>
            <td height="20" class="users_u_bottom_r">
            </td>
        </tr>
    </table>
    <div align="right">
    </div>    
</div>
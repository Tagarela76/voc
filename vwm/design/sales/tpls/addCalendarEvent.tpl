<div id="addEventWindow">
	<table>
		<tr>
			<td><input type="hidden" id="eventId" name="eventId" value="{$data->id}"/></td>
		</tr>		
		<tr>
			<td colspan=""  style="padding:0px;border-bottom:0px;">
				Event Title : 	
			</td>
			<td colspan=""  style="padding:0px;border-bottom:0px;">
				<input type="text" name="title" id="title" style="float:left;" value="{$data->title}"/>
				{foreach from=$violationList item="violation"}
					{if $violation->getPropertyPath() eq 'title'}							
					{*ERROR*}					
					<div class="error_img" style="float: left;"><span class="error_text">{$violation->getMessage()}</span></div>
					{*/ERROR*}						    
					{/if}
				{/foreach}							
			</td>							
		</tr>

		<tr>
			<td colspan=""  style="padding:0px;border-bottom:0px;">
				Event Description : 	
			</td>
			<td colspan=""  style="padding:0px;border-bottom:0px;">
				<textarea name="description" id="description" cols="50" rows="7">{$data->description}</textarea>
				{foreach from=$violationList item="violation"}
					{if $violation->getPropertyPath() eq 'description'}							
					{*ERROR*}					
					<div class="error_img" style="float: left;"><span class="error_text">{$violation->getMessage()}</span></div>
					{*/ERROR*}						    
					{/if}
				{/foreach}	
			</td>	
	</table>
</div>	
<table>
	<tr>
		<td><input type="hidden" id="timestamp" name="timestamp" value="{$timestamp}"/></td>
	</tr>	
</table>		


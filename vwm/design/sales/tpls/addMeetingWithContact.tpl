<div style="padding:7px;">
	<form method='POST' action=''>
		<table class="users rd" align="center" cellpadding="0" cellspacing="0">
			<tr class="users_u_top_size users_top">
				<td class="users_u_top" height="30" width="20%">
					<span >{if $request.action eq "add"}Adding for a new meeting{else}Editing meeting{/if}</span>
				</td>

				<td class="users_u_top_r">
				</td>
			</tr>

			<tr style="height:10px;">
				<td class="border_users_l border_users_b">
					Contact Name:
				</td>
				<td class="border_users_l border_users_b border_users_r">
					<div align="left" style='display:inline; float:left;'>
						{assign var="contact" value=$meeting->getContact()}
						{$contact->contact|escape}
					</div>				
				</td>

			</tr>

			<tr height="10px">
				<td class="border_users_l border_users_b">
					Date:
				</td>
				<td class="border_users_l border_users_b border_users_r">
					<div align="left" style='display:inline; float:left;'>
						{if $violationList|@count == 0}
							{if $request.action != 'add'}
								{assign var="meetingDate" value=$meeting->getMeetingDate(true)}
							{else}
								{assign var="meetingDate" value=$meeting->getMeetingDate()}
							{/if}
						{else}
							{assign var="meetingDate" value=$meeting->getMeetingDate()}
						{/if}
						<input type='text' name="meeting_date" id="date" class="calendarFocus" value='{$meetingDate|escape}'>
					</div>
						{foreach from=$violationList item="violation"}
								{if $violation->getPropertyPath() eq 'meeting_date'}
								{*ERROR*}
								<div class="error_img" style="float: left;"><span class="error_text">{$violation->getMessage()}</span></div>
								{*/ERROR*}
								{/if}
							{/foreach}
				</td>
			</tr>

			<tr height="10px">
				<td class="border_users_l border_users_b">
					Notes:
				</td>
				<td class="border_users_l border_users_b border_users_r">
					<div align="left" style='display:inline; float:left;'>
						<textarea name='notes' rows="5" >{$meeting->getNotes()}</textarea>
					</div>
					{foreach from=$violationList item="violation"}
								{if $violation->getPropertyPath() eq 'notes'}
								{*ERROR*}
								<div class="error_img" style="float: left;"><span class="error_text">{$violation->getMessage()}</span></div>
								{*/ERROR*}
								{/if}
							{/foreach}
				</td>
			</tr>
			
			<tr>
				<td height="20" class="users_u_bottom">
					&nbsp;
				</td>
				<td height="20" class="users_u_bottom_r">
					&nbsp;
				</td>
            </tr>


		</table>
		<div align="right">
			<br>
			<input type='submit' name='save' class="button" value='Save'>
			<input type='button' class="button" id='cancelButton' value='Cancel' onclick="location.href='sales.php?action=viewDetails&category=contacts&id={$meeting->getContactId()|escape}'">
			<span style="padding-right:50">&nbsp;</span>
		</div>		
	</form>
</div>

{literal}
	<script type="text/javascript">
		$(document).ready(function(){			
			$('#date').datetimepicker({
				dateFormat: '{/literal}{$dataChain->getFromTypeController('getFormatForCalendar')}{literal}'
			});
		});
	</script>
{/literal}
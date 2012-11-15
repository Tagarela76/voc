<div style="padding:7px;">
	<div class="control_panel_center padd7">
		<input type="button" class="button" value="Add" onclick="location.href='?action=add&category=meetingWithContact&contactId={$request.id|escape}';">
	</div>

	<table class="users" height="200" cellspacing="0" cellpadding="0" align="center">
		<tr class="users_top" height="27px">
			{*<td class="users_u_top" width="10%">
				<span style='display:inline-block; width:60px;'> 
					<a onclick="CheckAll(this)" style='color:white'>All</a>
					/
					<a style='color:white' onclick="unCheckAll(this)" >None</a>
				</span>
			</td>*}

			<td class="users_u_top" width="10%">
				<a style='color:white;'>
					<div style='width:100%;  color:white;'>
						ID
					</div>
				</a>
			</td>		
			<td class="" width="30%">
				<a style='color:white;'>
					<div style='width:100%;  color:white;'>
						Manager Name
					</div>
				</a>
			</td>
			<td class="" width="20px">
				<a style='color:white;'>
					<div style='width:100%;  color:white;'>
						Meeting Date
					</div>
				</a>
			</td>
			<td class="users_u_top_r" width="40%">
				<a style='color:white;'>
					<div style='width:100%;  color:white;'>
						Notes
					</div>
				</a>
			</td>
		</tr>
		{if $meetings|@is_array and $meetings|@count > 0}
			{*BEGIN LIST*}
			{foreach from=$meetings item="meeting"}
				{assign var="id" value=$meeting->getId()}
				{assign var="url" value="sales.php?action=edit&category=meetingWithContact&id=$id"}
				<tr class="hov_company" height="10px">

					{*<td class="border_users_b border_users_l border_users_r" >
						<input type="checkbox" value="{$meeting->getId()|escape}" name="meetingId[]">
					</td>*}
					
					<td class="border_users_b border_users_l border_users_r" >
						<div style="width:100%;">
							{$meeting->getId()|escape}
						</div>
					</td>
					<td class="border_users_b border_users_r">
						<div style="width:100%;">
							<a href="{$url}">
							{assign var="user" value=$meeting->getUser()}
							{$user->username|escape}
							</a>
						</div>
					</td>
					<td class="border_users_b border_users_r">
						<div style="width:100%;">
							<a href="{$url}">
							{$meeting->getMeetingDate(true)}
							</a>
						</div>
					</td>
					<td class="border_users_b border_users_r">
						<div style="width:100%;">
							<a href="{$url}">
							{$meeting->getNotes()|escape}
							</a>
						</div>
					</td>
			</tr>
		{/foreach}
		<tr>
			<td colspan="4" class="border_users_l border_users_r">
				&nbsp;
			</td>
		</tr>
		{*END LIST*}
	{else}
		{*BEGIN	EMPTY LIST*}
		<tr class="">
			<td colspan="4"class="border_users_l border_users_r" align="center">
				No meetings with this client
			</td>
		</tr>
		{*END	EMPTY LIST*}
	{/if}
    <tr>
        <td class="users_u_bottom">
        </td>
        <td colspan="2" height="15" class="border_users">
        </td>
        <td class="users_u_bottom_r">
        </td>
    </tr>
</table>
</div>
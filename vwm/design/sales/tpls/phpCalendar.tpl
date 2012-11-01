<!--Start the opening div that the calendar is contained within-->
<div id="cal-content">
	{$navButtonBackward} 
	<!--Set the navigation buttons and the current month/year heading -->
    <span id="cal-date-heading">{$currentMonthName}, {$year}</span>{$navButtonForward}
	<!-- Start the HTML table that the calendar squares are in -->
	<table id="calendar" cellspacing="2">
		<!-- Generate the day names at top of the calendar. -->
		<tr class="days_header">
			{foreach from=$days item=day}
				<td>{$day|truncate:3:""}</td>
			{/foreach}	
		</tr>
		<!-- Print empty calendar squares for days the first day doesn't start on -->
		<tr>
			{if $dayMonthBegan < 7}
				{section name=i start=0 loop=$dayMonthBegan}
					<td width=100>&nbsp;</td>
				{/section}
			{/if}
			
			{$pieceOfCalendar}
		</tr> 
	</table> 
</div>
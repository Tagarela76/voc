<!-- Start the opening div that the calendar is contained within-->
<div id="cal-content">
	<!-- Set the navigation buttons and the current month/year heading-->
	{$navButtonBackward} 
	<span id="cal-date-heading">
		{$currentMonthName} , {$year}
	</span>
	{$navButtonForward}
	<!-- Start the HTML table that the calendar squares are in -->
	<table id="calendar" cellspacing="2">
		<!-- Generate the day names at top of the calendar-->
		<tr class="days_header">
			{foreach from=$days item=day}
				<td>{strtoupper(substr($day, 0, 3))}</td>';
			{/foreach}	

		</tr>

		<!-- Print empty calendar squares for days the first day doesn't start on -->
		<tr>
			{if $dayMonthBegan < 7}
				{for $i=0 to $dayMonthBegan}
				<td>&nbsp;</td>
				{/for}
			{/if}	
			<!-- Loop through all the days -->
			{assign var=dayAsInt value=0}
			{foreach from=$classesForCurrentDays item=classesForCurrentDay key=dayAsInt}
				<!-- Set the actual calendar squares, hyperlinked to their timestamps -->
				<td><a href="?timestamp={strtotime($classesForCurrentDay.currentDay)}" {$classesForCurrentDay.class} > {$dayAsInt} </a></td>

				<!-- Our calendar has Saturday as the last day of the week,
				 so we'll wrap to a newline after every SAT -->
				{if $dayAsInt != $daysInMonth && $dayAsStr == 'Sat'}
				</tr><tr>
				{/if}
			{/foreach}
			<!-- Close up the table and div for the calendar-->
		</tr> </table> </div>';
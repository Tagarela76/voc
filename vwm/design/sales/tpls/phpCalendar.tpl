<div id="cal-content">
	<h1 class="titleinfo">{$currentMonthName}, {$year}</h1>
	<div>				
		<a class="cal-nav-buttons" 
		   href="?action=browseCategory&category=calendar&timestamp={$navButtonBackwardTimestamp}">
			{$navButtonBackwardDate}</a>
		<a class="cal-nav-buttons"
		   href="?action=browseCategory&category=calendar&timestamp={$navButtonForwardTimestamp}">
			{$navButtonForwardDate}</a>
	</div>
	
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
					<td><div>&nbsp;</div></td>
				{/section}
			{/if}
			
			{$pieceOfCalendar}
		</tr> 
	</table> 
</div>
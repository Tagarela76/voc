{literal}
<script type="text/javascript">
	$(function() {
		//	global calendarPage object defined at calendarSettings.js
		calendarPage.userId = {/literal} {$userId} {literal};
	});
</script>
{/literal}
<div class="padd7" align="center">
	{$phpCalendarTpl}
</div>

<div id="addCalendarEventContainer" title="add calendar event" style="display:none;">Loading ...</div>
<div id="updateCalendarEventContainer" title="update calendar event" style="display:none;">Loading ...</div>
{literal}
	<script type="text/javascript">
		$(function() {
			$('a').tooltip({
				track: true,
				delay: 30,
				showURL: false,
				fixPNG: true,
				extraClass: "mixSaveButton",
				top: -35,
				left: -40,
				 bodyHandler: function() { 
					return $($(this).attr("href")).html(); 
				} 
			});
		});
		
	</script>
{/literal}	
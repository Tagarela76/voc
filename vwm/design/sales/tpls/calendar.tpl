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

<div id="addCalendarEventContainer" title="Add Calendar Event" style="display:none;">Loading ...</div>
<div id="updateCalendarEventContainer" title="Update Calendar Event" style="display:none;">Loading ...</div>
{literal}
	<script type="text/javascript">
		$(function() {
			$('a').tooltip({
				track: true,
				delay: 30,
				showURL: false,
				fixPNG: true,
				extraClass: "mixSaveButton",
				top: 5,
				left: 5,
				 bodyHandler: function() { 
					return $($(this).attr("href")).html(); 
				} 
			});
		});
		
	</script>
{/literal}	
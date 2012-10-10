{literal}
	<script type="text/javascript">
		var page = {};
		$(function() {			
			page = new Page();
			page.salesBrochure.setTitleUp('{/literal}{$salesBrochure->title_up|escape}{literal}', true);
			page.salesBrochure.setTitleDown('{/literal}{$salesBrochure->title_down|escape}{literal}', true);
			page.init();
		});
	</script>
{/literal}	

<input type="hidden" id="salesBrochureClientId" value="{$salesBrochure->sales_client_id|escape}"/>

<div align="center" id="sales_brochure">
	<div id="sales_brochure_title_up">
		<p id="title_up"></p>	
	</div>

	<div id="sales_brochure_title_down">
		<p id="title_down"></p>	
	</div>	
</div>

<div id="brochure_control_button">
	<input type="button" class="button" value="edit" onclick="page.salesBrochure.editMode()"/>
</div>	
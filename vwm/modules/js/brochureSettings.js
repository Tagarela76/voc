function salesBrochure() {
	this.divId = 'salesBrochureContainer';	
	
	this.editMode = function() { 
		
		$('#title_up').html('<input type="text" id="salesBrochureTitleUp" value="'+page.titleUp+'"/>');
		$('#title_down').html('<input type="text" id="salesBrochureTitleDown" value="'+page.titleDown+'"/>');
		var html = '';
		html += '<input type="button" class="button" href="#" onclick="page.salesBrochure.update(); return false;" value="update">';
		html += '<input type="button" class="button" href="#" onclick="page.salesBrochure.cancel(); return false;" value="cancel">';
		$('#brochure_control_button').html(html);
	}
	
	this.cancel = function() {
		$('#title_up').html(page.titleUp);
		$('#title_down').html(page.titleDown);
		$('#brochure_control_button').html('<input type="button" class="button" value="edit" onclick="page.salesBrochure.editMode()"/>');
	}
	this.update = function() { 		

		var salesBrochureTitleUp = $('#salesBrochureTitleUp').val();
		var salesBrochureTitleDown = $('#salesBrochureTitleDown').val();
		var salesBrochureClientId = $('#salesBrochureClientId').val();
		
		$.ajax({
			url: "?action=updateItem&category=salesBrochure",
			data: {salesBrochureTitleUp: salesBrochureTitleUp, salesBrochureTitleDown: salesBrochureTitleDown, salesBrochureClientId: salesBrochureClientId},
			type: "GET",
			dataType: "html",
			success: function (response) {
				page.titleUp = salesBrochureTitleUp;
				page.titleDown = salesBrochureTitleDown;
				$('#title_up').html(salesBrochureTitleUp);
				$('#title_down').html(salesBrochureTitleDown);
				$('#brochure_control_button').html('<input type="button" class="button" value="edit" onclick="page.salesBrochure.editMode()"/>');
			}
		});
	}
}
			
		
function Page() {
	this.salesBrochure = new salesBrochure();
	this.titleUp = false;
	this.titleDown = false;
}

//	global settings object
var page;

$(function() {
	//	ini global object
	page = new Page();
});

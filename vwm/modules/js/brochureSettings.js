function salesBrochure() {
	this.divId = 'salesBrochureContainer';
	var that = this;
	
	this.editMode = function() { 
		
		$('#title_up').html('<input type="text" id="salesBrochureTitleUp" value="'+page.titleUp+'"/>');
		$('#title_down').html('<input type="text" id="salesBrochureTitleDown" value="'+page.titleDown+'"/>');
		$('#brochure_control_button').html('<a href="#" onclick="page.salesBrochure.update(); return false;">update</a> | <a href="#" onclick="page.salesBrochure.cancel(); return false;">Cancel</a>');
	}
	
	this.cancel = function() {
		$('#title_up').html(page.titleUp);
		$('#title_down').html(page.titleDown);
		$('#brochure_control_button').html('<input type="button" value="edit" onclick="page.salesBrochure.editMode()"/>');
	}
	this.update = function() { 
		var that = this;

		var salesBrochureTitleUp = $('#salesBrochureTitleUp').val();
		var salesBrochureTitleDown = $('#salesBrochureTitleDown').val();
		var salesBrochureClientId = $('#salesBrochureClientId').val();
		
		$.ajax({
			url: "?action=updateItem&category=salesBrochure",
			data: {salesBrochureTitleUp: salesBrochureTitleUp, salesBrochureTitleDown: salesBrochureTitleDown, salesBrochureClientId: salesBrochureClientId},
			type: "GET",
			dataType: "html",
			success: function (response) {
				$('#title_up').html(salesBrochureTitleUp);
				$('#title_down').html(salesBrochureTitleDown);
				$('#brochure_control_button').html('<input type="button" value="edit" onclick="page.salesBrochure.editMode()"/>');
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

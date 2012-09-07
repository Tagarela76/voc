function SalesBrochure() {
	this.divId = 'salesBrochureContainer';		
	this.titleUp = false;
	this.titleDown = false;
	
	this.setTitleUp = function(titleUp, isEscaped) {
		isEscaped = typeof isEscaped !== 'undefined' ? isEscaped : false;
		if(isEscaped) {
			this.titleUp = titleUp;
		} else {
			this.titleUp = page.utils.escapeHTML(titleUp);
		}
	}
	
	this.setTitleDown = function(titleDown, isEscaped) {
		isEscaped = typeof isEscaped !== 'undefined' ? isEscaped : false;
		if(isEscaped) {
			this.titleDown = titleDown;
		} else {
			this.titleDown = page.utils.escapeHTML(titleDown);
		}
	}
	
	this.renderTitleUp = function(isEditMode) {
		isEditMode = typeof isEditMode !== 'undefined' ? isEditMode : false;
		if(isEditMode) {
			$('#title_up').html('<input type="text" id="salesBrochureTitleUp" value="'+this.titleUp+'"/>');	
		} else {
			$('#title_up').html(this.titleUp);
		}		
	}
	
	this.renderTitleDown = function(isEditMode) {
		isEditMode = typeof isEditMode !== 'undefined' ? isEditMode : false;
		if(isEditMode) {
			$('#title_down').html('<input type="text" id="salesBrochureTitleDown" value="'+this.titleDown+'"/>');
		} else {
			$('#title_down').html(this.titleDown);
		}		
	}
	
	this.editMode = function() { 		
		this.renderTitleDown(true);
		this.renderTitleUp(true);
		
		var html = '';
		html += '<input type="button" class="button" href="#" onclick="page.salesBrochure.update(); return false;" value="update">';
		html += '<input type="button" class="button" href="#" onclick="page.salesBrochure.cancel(); return false;" value="cancel">';
		$('#brochure_control_button').html(html);
	}
	
	this.cancel = function() {
		$('#title_up').html(this.titleUp);
		$('#title_down').html(this.titleDown);
		$('#brochure_control_button').html('<input type="button" class="button" value="edit" onclick="page.salesBrochure.editMode()"/>');
	}
	this.update = function() { 		
		var that = this;
		var salesBrochureTitleUp = $('#salesBrochureTitleUp').val();
		var salesBrochureTitleDown = unescape($('#salesBrochureTitleDown').val());
		var salesBrochureClientId = $('#salesBrochureClientId').val();
		
		$.ajax({
			url: "?action=updateItem&category=salesBrochure",
			data: {salesBrochureTitleUp: salesBrochureTitleUp, salesBrochureTitleDown: salesBrochureTitleDown, salesBrochureClientId: salesBrochureClientId},
			type: "GET",
			dataType: "html",
			success: function (response) {				
				that.setTitleUp(salesBrochureTitleUp);
				that.renderTitleUp();
				that.setTitleDown(salesBrochureTitleDown);
				that.renderTitleDown();				
				$('#brochure_control_button').html('<input type="button" class="button" value="edit" onclick="page.salesBrochure.editMode()"/>');
			}
		});
	}
}
			
		
function Page() {
	this.salesBrochure = new SalesBrochure();
	this.utils = new Utils();	
	
	this.init = function() {
		this.salesBrochure.renderTitleUp();
		this.salesBrochure.renderTitleDown();
	}
}

function manageDisplayColumnsMix() {
	this.divId = 'displayColumnsSettingsMixContainer';
	this.isLoaded = false;

	this.iniDialog = function(divId) {
		divId = typeof divId !== 'undefined' ? divId : this.divId;
		if(divId != this.divId) {
			this.divId = divId;
		}

		var that = this;
		$("#"+divId).dialog({
			width: 350,
			height: 400,
			autoOpen: false,
			resizable: true,
			dragable: true,
			modal: true,
			buttons: {
				'Cancel': function() {
					$(this).dialog('close');
					that.isLoaded = false;
				},
				'Save': function() {
					that.save();
				}
			}
		});
	}

	this.openDialog = function() {
		$('#'+this.divId).dialog('open');
		if(!this.isLoaded) {
			this.loadContent();
		}
		return false;
	}

	this.loadContent = function() {
		var that = this;
		$.ajax({
			url: "?action=loadDisplayColumnsSettings&category=industryType&entity=mix",
			data: {industryTypeId: industryTypePage.industryTypeId},
			type: "GET",
			dataType: "html",
			success: function (response) {
				$("#"+that.divId).html(response);
				that.isLoaded = true;
      		}
		});
	}
	
	this.save = function() {
		var that = this;
		var checkboxes = $("#"+that.divId).find("input[type='checkbox']"); 
		var rowsToSave = new Array();
	
		checkboxes.each(function(i){
			var id = this.value;
			if(this.checked) {
				rowsToSave.push(id);
			}
		}); 
		$.ajax({
			url: "?action=saveDisplayColumnsSettings&category=industryType&entity=mix",
			data: {rowsToSave: rowsToSave, industryTypeId: industryTypePage.industryTypeId},
			type: "GET",
			dataType: "html",
			success: function (response) {
				$("#browse_category_mix").html(response);
				$("#"+that.divId).dialog('close'); 
				that.divId.isLoaded = false;
			}
		});
	}
}
			
		
function IndustryTypePage() {
	this.manageDisplayColumnsMix = new manageDisplayColumnsMix();
	this.industryTypeId = false;
}


//	global object
var industryTypePage;

$(function() {
	//	ini global object
	industryTypePage = new IndustryTypePage();
	industryTypePage.manageDisplayColumnsMix.iniDialog();
});

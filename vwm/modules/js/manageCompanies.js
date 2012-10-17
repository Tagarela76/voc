function manageCompanies() {
	this.divId = 'setCompanyToIndustryTypeContainer';
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
				'Set': function() {
					that.set();
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
			url: "?action=loadIndustryTypes&category=company",
			data: {companyId: companyPage.companyId},
			type: "GET",
			dataType: "html",
			success: function (response) {
				$("#"+that.divId).html(response);
				that.isLoaded = true;
      		}
		});
	}
	
	this.set = function() {
		var that = this;
		var checkboxes = $("#"+that.divId).find("input[type='checkbox']"); 
		var rowsToSet = new Array();
	
		checkboxes.each(function(i){
			var id = this.value;
			if(this.checked) {
				rowsToSet.push(id);
			}
		});
		$.ajax({
			url: "?action=setCompanyToIndustryType&category=company",
			data: {rowsToSet: rowsToSet},
			type: "GET",
			dataType: "html",
			success: function (response) {
				$("#industryTypesList").html(response);
				$("#"+that.divId).dialog('close'); 
				that.divId.isLoaded = false;
			}
		});
	}
}
			
		
function CompanyPage() {
	this.manageCompanies = new manageCompanies();
	this.companyId = false;
}


//	global reminderPage object
var companyPage;

$(function() {
	//	ini global object
	companyPage = new CompanyPage();
	companyPage.manageCompanies.iniDialog();
});

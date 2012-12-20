
function managePfpTypes(){
	this.divId = 'setDepartmentToPfpContainer';
	this.isLoaded = false;
	
	this.iniDialog = function(divId) {
		divId = typeof divId !== 'undefined' ? divId : this.divId;
		if(divId != this.divId) {
			this.divId = divId;
		}

		var that = this;
		$("#"+divId).dialog({
			width: 250,
			height: 300,
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
		var departmentsId = $('#pfpDepartments_id').val();
		$.ajax({
			url: "?action=loadDepartments&category=pfpTypes",
			data: {facilityId: pfpTypesPage.facilityId, departmentsId:departmentsId},
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
			url: "?action=setDepartmentToPfpTypes&category=pfpTypes",
			data: {rowsToSave: rowsToSave},
			type: "GET",
			dataType: "html",
			success: function (response) {
				$("#departments2pfpTypes_list").html(response);
				$("#"+that.divId).dialog('close'); 
				that.divId.isLoaded = false;
			}
		});
	}
		
	
}

function PfpTypesPage() {
	this.managePfpTypes = new managePfpTypes();
    this.facilityId = false;
   // this.woId = false;
}

var pfpTypesPage;

$(function() {
	//	ini global object
	pfpTypesPage = new PfpTypesPage();
	pfpTypesPage.managePfpTypes.iniDialog();
});


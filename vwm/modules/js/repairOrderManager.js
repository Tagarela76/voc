function manageRepairOrder() {
	this.divId = 'setDepartmentToWoContainer';
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
		$.ajax({
			url: "?action=loadDepartments&category=repairOrder",
			data: {facilityId: repairOrderPage.facilityId, woId: repairOrderPage.woId},
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
			url: "?action=setDepartmentToWo&category=repairOrder",
			data: {rowsToSave: rowsToSave},
			type: "GET",
			dataType: "html",
			success: function (response) {
				$("#departments2wo_list").html(response);
				$("#"+that.divId).dialog('close'); 
				that.divId.isLoaded = false;
			}
		});
	}
}
			
		
function RepairOrderPage() {
	this.manageRepairOrder = new manageRepairOrder();
    this.facilityId = false;
    this.woId = false;
}


//	global object
var repairOrderPage;

$(function() {
	//	ini global object
	repairOrderPage = new RepairOrderPage();
	repairOrderPage.manageRepairOrder.iniDialog();
});

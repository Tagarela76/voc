function ManagePermissions() {
	this.divId = 'managePermissionsContainer';
	this.allUsersSelectId = 'allUsers';

	this.isLoaded = false;
	this.allUsers = [];
	this.currentDepartmentId = 0;

	this.iniDialog = function(divId) {
		divId = typeof divId !== 'undefined' ? divId : this.divId;
		if(divId != this.divId) {
			this.divId = divId;
		}

		var that = this;
		$("#"+divId).dialog({
			width: 800,
			autoOpen: false,
			resizable: true,
			dragable: true,
			modal: true,
			buttons: {
				'Cancel': function() {
					$(this).dialog('close');
					that.isLoaded = false;
					that.allUsers = [];
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
			url: "?action=loadManagePermissions&scope=department",
			data: {facilityId: settings.facilityId},
			type: "GET",
			dataType: "html",
			success: function (response) {
				$("#"+that.divId).html(response);

				// save allUsers options to internal property as we will modify
				// original selectbox really often
				that.setAllUsers();

				// by default show the first department
				that.showDepartament();

				// do not perform any AJAX queries from now
				that.isLoaded = true;
      		}
		});
	}

	this.save = function() {
	var that = this;	
		var assignedUsers = [];
		$('#departmentUsers_'+this.currentDepartmentId).children('option').each(function(){
			assignedUsers.push($(this).val());
		});
		$.ajax({
			url: "?action=saveManagePermissions&scope=department",
			data: {departmentId: this.currentDepartmentId, assignedUsers: assignedUsers},
			type: "GET",
			dataType: "html",
			success: function (response) {
				$("#"+that.divId).dialog('close'); 
				that.divId.isLoaded = false;
				that.divId.allUsers = [];
      		}
		});
	}

	this.setAllUsers = function() {
		var that = this;
		$('#'+this.allUsersSelectId).children("option").each(function () {
			that.allUsers.push(this);
		});
	}

	this.resetAllUsersSelectBox = function() {
		var selectBox = $('#'+this.allUsersSelectId);

		//	clean
		$(selectBox).empty();

		// fill selectbox with this.allUsers which was set right after AJAX
		// query
		for(option in this.allUsers) {
			$(selectBox).append(this.allUsers[option]);
		}
	}

	this.showDepartament = function(selectBox) {
		var departmentId = 0;
		if(typeof selectBox !== 'undefined') {
			departmentId = $(selectBox).val();
		} else {
			departmentId = $("#departmentSwitcher").val();
		}

		// set current departmentId
		this.currentDepartmentId = departmentId;
		// reload allUsers
		this.resetAllUsersSelectBox();
		// remove users which are already assigened to department
		this.removeDuplicates($('#'+this.allUsersSelectId), $("#departmentUsers_"+departmentId));

		$(".departmentPermissions").hide();
		$("#departmentPermissions_"+departmentId).show();
	}

	this.removeDuplicates = function (from, compareWith) {
		var usedNames = {};
		$(compareWith).children("option").each(function () {
			usedNames[this.text] = this.value;
		});

		$(from).children("option").each(function () {
			if(usedNames[this.text]) {
				$(this).remove();
			}
		});
	}

	this.moveFromAllToAssigned = function() {
		var that = this;
		$('#'+this.allUsersSelectId).children('option:selected').each(function() {
			$('#departmentUsers_'+that.currentDepartmentId).append($(this).clone());
			$(this).remove();
		});
	}

	this.moveFromAssignedToAll = function() {
		var that = this;
		$('#departmentUsers_'+this.currentDepartmentId).children('option:selected').each(function() {
			$('#'+that.allUsersSelectId).append($(this).clone());
			$(this).remove();
		});
	}
}

function ManageAdditionalEmailAccounts() {
	this.divId = 'manageAdditionalEmailAccountsContainer';
	this.divUserAccountListId = 'userAccountListContainer';
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
			url: "?action=loadManageAdditionalEmailAccounts",
			data: {facilityId: settings.facilityId, companyId: settings.companyId},
			type: "GET",
			dataType: "html",
			success: function (response) {
				$("#"+that.divId).html(response);
				that.isLoaded = true;
      		}
		});
	}

	this.deleteSelectedEmailAccount = function() {
		
		var that = this;
		var checkboxes = $("#"+that.divUserAccountListId).find("input[type='checkbox']");
		var rowsToRemove = new Array();
		var companyId = $('#companyId').val();
		// clean
		$('#emailAccountDeleteItemError').css("display", "none");
		$('#emailAccountUserNameError').css("display", "none");
		$('#emailAccountUserEmailError').css("display", "none");
		$('#emailAccountUserIdError').css("display", "none");
		
		checkboxes.each(function(i){
			var id = this.value;
			if(this.checked) {
				rowsToRemove.push(id);
			}

		});
		if (rowsToRemove.length <1) {
			$('#emailAccountDeleteItemError').css("display", "block");
		} else {
			$.ajax({
				url: "?action=deleteItem&category=additionalEmailAccounts",
				data: {id: rowsToRemove, companyId: companyId},
				type: "GET",
				dataType: "html",
				success: function (response) {
					$("#"+that.divUserAccountListId).html(response);
				}
			});
		}		
	}
	
	this.addNewEmailAccount = function() {
		var that = this;
		var isError = false;
		// clean
		$('#emailAccountDeleteItemError').css("display", "none");
		$('#emailAccountUserNameError').css("display", "none");
		$('#emailAccountUserEmailError').css("display", "none");
		$('#emailAccountUserIdError').css("display", "none");
		
		var emailAccountUserName = $('#emailAccountUserName').val();
		var emailAccountUserEmail = $('#emailAccountUserEmail').val();
		var companyId = $('#companyId').val();
		if (emailAccountUserName == "") {
			$('#emailAccountUserNameError').css("display", "block");
			isError = true;
		} if (emailAccountUserEmail == "") {
			$('#emailAccountUserEmailError').css("display", "block");
			isError = true;
		}
		if (isError) {
			return false;
		}
		
		$.ajax({
			url: "?action=addItem&category=additionalEmailAccounts",
			data: {emailAccountUserName: emailAccountUserName, emailAccountUserEmail: emailAccountUserEmail, companyId: companyId},
			type: "GET",
			dataType: "html",
			success: function (response) {
				if (response == "error") {
					// enterred email already in use
					$('#emailAccountUserIdError').css("display", "block");
				} else {
					$("#"+that.divUserAccountListId).html(response);
				}
			}
		});
	}
}
			
function ManageQtyProductGauge() {
	this.divId = 'manageQtyProductGaugeContainer';
	this.isLoaded = false;

	this.iniDialog = function(divId) {
		divId = typeof divId !== 'undefined' ? divId : this.divId;
		if(divId !== this.divId) {
			this.divId = divId;
		}

		var that = this;
		$("#"+divId).dialog({
			width: 350,
			height: 200,
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
			url: "?action=loadQtyProductSettings",
			data: {facilityId: settings.facilityId, 
				companyId: settings.companyId, 
				departmentId:(settings.departmentId) ? settings.departmentId : 0 },
			type: "GET",
			dataType: "html",
			success: function (response) {
				$("#"+that.divId).html(response);
				that.isLoaded = true;
      		}
		});
	};
	
	this.save = function() {
		var that = this;
        var id = $("#id").val();
		var limit = $("#limit").val();
		var gaugeType = $("#gaugeType").val();
        var unit_type = $("#unit_type").val();
        var period = $("#period").val();
        var facility_id = $("#facility_id").val();
		var selectProductGauge = $('#selectProductGauge :selected').val();
		
		
		$.ajax({
			url: "?action=saveQtyProductGaugeSettings",
			data: {id: id, limit: limit, unit_type: unit_type, period: period, facility_id: facility_id, department_id: settings.departmentId, productGauge: selectProductGauge, gaugeType: gaugeType},
			type: "GET",
			dataType: "html",
			success: function (response) {
				console.log(response);
				that.isLoaded = false;
				$("#"+that.divId).dialog('close'); 
				that.divId.isLoaded = false;
			}
		});
	};
}
		
function Settings() {
	this.managePermissions = new ManagePermissions();
	this.manageAdditionalEmailAccounts = new ManageAdditionalEmailAccounts();
    this.manageQtyProductGauge = new ManageQtyProductGauge();
	this.companyId = false;
	this.facilityId = false;
	this.departmentId = false;
}


//	global settings object
var settings;

$(function() {
	//	ini global object
	settings = new Settings();
	settings.managePermissions.iniDialog();
	settings.manageAdditionalEmailAccounts.iniDialog();
    settings.manageQtyProductGauge.iniDialog();
});

function selectProductGauge(){
	var selectProductGauge = $('#selectProductGauge :selected').val();
	var that = this;
	console.log(this.divId);
	$.ajax({
			url: "?action=loadQtyProductSettings",
			data: {facilityId: settings.facilityId, 
				companyId: settings.companyId, 
				productGauge: selectProductGauge,
				departmentId:(settings.departmentId) ? settings.departmentId : 0 },
				
			type: "GET",
			dataType: "html",
			success: function (response) {
				$("#manageQtyProductGaugeContainer").html(response);
				/*that.isLoaded = true;*/
      		}
		});
}

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
		console.log('SAVE');
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


function Settings() {
	this.managePermissions = new ManagePermissions();
	this.companyId = false;
	this.facilityId = false;
}


//	global settings object
var settings;

$(function() {
	//	ini global object
	settings = new Settings();
	settings.managePermissions.iniDialog();
});

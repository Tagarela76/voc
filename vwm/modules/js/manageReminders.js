function manageReminders() {
	this.divId = 'setRemind2UserContainer';
	this.divUsersListId = 'usersListContainer';
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
			url: "?action=loadUsers&category=reminders",
			data: {facilityId: settings.facilityId, remindId: settings.remindId},
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
		var checkboxes = $("#"+that.divUsersListId).find("input[type='checkbox']");
		var rowsToSet = new Array();
        var rowsToUnSet = new Array();
		var remindId = $('#remindId').val();
		
		checkboxes.each(function(i){
			var id = this.value;
			if(this.checked) {
				rowsToSet.push(id);
			} else {
                rowsToUnSet.push(id);
            }

		});
		$.ajax({
			url: "?action=manageRemindToUser&category=reminders",
			data: {rowsToSet: rowsToSet, rowsToUnSet: rowsToUnSet, remindId: remindId},
			type: "GET",
			dataType: "html",
			success: function (response) {
				$("#usersList").html(response);
				$("#"+that.divId).dialog('close'); 
				that.divId.isLoaded = false;
			}
		});
	}
}
			
		
function Settings() {
	this.manageReminders = new manageReminders();
	this.facilityId = false;
	this.remindId = false;
}


//	global settings object
var settings;

$(function() {
	//	ini global object
	settings = new Settings();
	settings.manageReminders.iniDialog();
});

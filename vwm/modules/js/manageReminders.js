function ReminderManager() {
    
    /**
     * 
     * show Reminder Beforhand Container
     * 
     * @returns {null}
     */
    this.showReminderBeforhandContainer = function(){
        var showReminderBeforhandContainer = $('#showReminderBeforeContainer').is(':checked');
        if(showReminderBeforhandContainer){
            $('#remindBeforhandContainer').show();
        }else{
            $('#remindBeforhandContainer').hide();
            //set 1 for default to bypass the validation on the server
            $('#timeNumber').val(1);
        }
    }
    
    /**
     * 
     * ajax method for getting unittype
     * 
     * @returns {null}
     */
    this.getReminderUnitTypeList = function(){
        var periodicity = $('#periodicity').val();
        $.ajax({
            url: '?action=getReminderUnitTypeList&category=reminder',
            data : {periodicity: periodicity},
            type: "POST",
            success: function(responce){
                $('#reminderUnitTypeListContainer').html(responce);
            } 
        });
    }
}

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
		var userListInput = $("#usersList").find("input[type='hidden']"); 
		var remindUsers = new Array();
        var facilityId = $('#facility_id').val();
		userListInput.each(function(i){ 
			var id = this.value;
			remindUsers.push(id);
		}); 
		$.ajax({
			url: "?action=loadUsers&category=reminder",
			data: {facilityId: facilityId, remindId: reminderPage.remindId, remindUsers: remindUsers},
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
			url: "?action=setRemindToUser&category=reminder",
			data: {rowsToSet: rowsToSet},
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
			
		
function ReminderPage() {
	this.manageReminders = new manageReminders();
    this.reminderManager = new ReminderManager();
	this.facilityId = false;
	this.remindId = false;
}


//	global reminderPage object
var reminderPage;

$(function() {
	//	ini global object
	reminderPage = new ReminderPage();
	reminderPage.manageReminders.iniDialog();
});

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

function ReminderUserManager() {
    this.reminderUserList = [];
    this.registeredReminderUserList = [];
    this.facilityId;
    var self = this;
    /**
     * 
     * set Reminder to User
     * 
     * @returns {null}
     */
    this.addReminderUser = function()
    {
        var email = $('#userEmail').val();
        var facilityId = $('#facilityId').val();

        $.ajax({
            url: "?action=SaveReminderUser&category=reminder",
            data: {facilityId: self.facilityId, email: email},
            type: "post",
            dataType: "json",
            success: function(response) {
                if (response) {

                    if (response.error) {
                        $('#emailError').show();
                        var errorHtml = response.errorMessage.email + '<br>';
                        $('#emailError').html(errorHtml);
                    } else {
                        $('#emailError').hide();
                        var reminderUser = new ReminderUser();
                        reminderUser.setEmail(email);
                        reminderUser.setReminderId(response.userId);
                        self.reminderUserList.push(reminderUser);
                        var html = '';
                        html += '<tr id="remined_user_details_' + response.userId + '">';
                        html += '<td height="20">';
                        html += '<input type="checkbox" id="reminderUserEmail_' + response.userId + '" value="' + response.userId + '" >';
                        html += '</td>';
                        html += '<td height="20">';
                        html += email;
                        html += '</td>';
                        html += '</tr>';
                        $('#dialogReminderUsersListContainer').append(html);
                        $('#userEmail').val('')
                    }
                }
            }
        });


    }
    
    /**
     * function is_email check is email is correct
     * true - correct
     * false - incorrect
     *
     */
    this.is_email = function(email)
    {
        var reg = /^[a-z0-9][a-z0-9\-._]*[a-z0-9]@[a-z0-9][a-z\-._]*[a-z0-9]\.(biz|com|edu|gov|info|int|mil|name|net|org|aero|asia|cat|coop|jobs|mobi|museum|pro|tel|travel|arpa|eco|xxx|[a-z]{2})$/i;
        return reg.test(email);
    }
    
    this.initialize = function(reminderUsersEmailList) {
        reminderUsersEmailList = $.parseJSON(reminderUsersEmailList)
        var count = reminderUsersEmailList.length;
        for(var i=0; i<count; i++){
            var reminderUser = new ReminderUser();
            reminderUser.setEmail(reminderUsersEmailList[i].email);
            reminderUser.setReminderId(reminderUsersEmailList[i].id);
            self.reminderUserList.push(reminderUser); 
        }
    }
    /**
     * 
     * @param {object} registeredReminderList
     * @returns {null}
     */
    this.initializeRegisteredReminderList = function(registeredReminderList) {
        registeredReminderList = $.parseJSON(registeredReminderList)
        var count = registeredReminderList.length;
        for(var i=0; i<count; i++){
            var reminderUser = new ReminderUser();
            reminderUser.setEmail(registeredReminderList[i].email);
            reminderUser.setReminderId(registeredReminderList[i].id);
            self.registeredReminderList.push(reminderUser); 
        }
    }
    
    this.getReminderById = function(id){
        var reminderList = self.reminderUserList;
        var count = reminderList.length;
        for(var i=0;i<count;i++){
            if(id == reminderList[i].getReminderId()){
                return reminderList[i];
            }
        }
        return false;
    }
    
}

function manageReminderUsers() {
    this.divId = 'setRemind2ReminderUserContainer';
    this.isLoaded = false;

    this.iniDialog = function(divId) {
        divId = typeof divId !== 'undefined' ? divId : this.divId;
        if (divId != this.divId) {
            this.divId = divId;
        }

        var that = this;
        $("#" + divId).dialog({
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
        $('#' + this.divId).dialog('open');
        if (!this.isLoaded) {
            this.loadContent();
        }
        return false;
    }

    this.loadContent = function() {
        var that = this;
        var facilityId = $('#facility_id').val();
        var reminderId = reminderPage.remindId;
        var selectedIds = $('#reminderUsersIdsList').val();
        $.ajax({
            url: "?action=loadReminderUsers&category=reminder",
            data: {facilityId: facilityId, reminderId: reminderId,selectedIds: selectedIds },
            type: "post",
            dataType: "html",
            success: function(response) {
                $("#" + that.divId).html(response);
                that.isLoaded = true;
            }
        });
    }

    /**
     * 
     * set usre reminders
     * 
     * @returns {null}
     */
    this.set = function() {
        //get reminders user
        var reminderUserIds = new Array();
        var reminderUserEmails = new Array();
        var reminder;
        var checkboxes = $("#" + this.divId).find("input[type='checkbox']");
        
        checkboxes.each(function(i) {
            var id = this.value;
            if (this.checked) {
                reminder = reminderUserManager.getReminderById(id);
                reminderUserEmails.push(reminder.getEmail());
                reminderUserIds.push(reminder.getReminderId());
            }
        });
         
        reminderUserIds = reminderUserIds.join(',');
        reminderUserEmails = reminderUserEmails.join(',');
       
        $('#reminderUsersListContainer').html(reminderUserEmails);
        $('#reminderUsersIdsList').val(reminderUserIds);
        $("#" + this.divId).dialog('close');
        this.divId.isLoaded = false;
    }
}

function manageReminders() {
    this.divId = 'setRemind2UserContainer';
    this.divUsersListId = 'usersListContainer';
    this.isLoaded = false;

    this.iniDialog = function(divId) {
        divId = typeof divId !== 'undefined' ? divId : this.divId;
        if (divId != this.divId) {
            this.divId = divId;
        }

        var that = this;
        $("#" + divId).dialog({
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
        $('#' + this.divId).dialog('open');
        if (!this.isLoaded) {
            this.loadContent();
        }
        return false;
    }

    this.loadContent = function() {
        var that = this;
        var userListInput = $("#usersList").find("input[type='hidden']");
        var remindUsers = new Array();
        var facilityId = $('#facility_id').val();
        userListInput.each(function(i) {
            var id = this.value;
            remindUsers.push(id);
        });
        
        $.ajax({
            url: "?action=loadUsers&category=reminder",
            data: {facilityId: facilityId, remindId: reminderPage.remindId, remindUsers: remindUsers},
            type: "GET",
            dataType: "html",
            success: function(response) {
                $("#" + that.divId).html(response);
                that.isLoaded = true;
            }
        });
    }

    this.set = function() {
        var that = this;
        var checkboxes = $("#" + that.divId).find("input[type='checkbox']");
        var rowsToSet = new Array();

        checkboxes.each(function(i) {
            var id = this.value;
            if (this.checked) {
                rowsToSet.push(id);
            }
        });
        
        $.ajax({
            url: "?action=setRemindToUser&category=reminder",
            data: {rowsToSet: rowsToSet},
            type: "GET",
            dataType: "html",
            success: function(response) {
                $("#usersList").html(response);
                $("#" + that.divId).dialog('close');
                that.divId.isLoaded = false;
            }
        });
    }
}


function ReminderPage() {
	this.manageReminders = new manageReminders();
    this.manageReminderUsers = new manageReminderUsers();
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
    reminderPage.manageReminderUsers.iniDialog();
});
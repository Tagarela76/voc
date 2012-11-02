function calendarAddEvent() {
	this.divId = 'addCalendarEventContainer';
	this.addEventDiv = 'addEventWindow';
	this.isLoaded = false;

	this.iniAddDialog = function(divId) {
		divId = typeof divId !== 'undefined' ? divId : this.divId;
		if(divId != this.divId) {
			this.divId = divId;
		}

		var that = this;
		$("#"+divId).dialog({
			width: 550,
			height: 250,
			autoOpen: false,
			resizable: true,
			dragable: true,
			modal: true,
			buttons: {
				'Cancel': function() {
					$(this).dialog('close');
					that.isLoaded = false;
				},
				'Add': function() {
					that.add();
				}
			}
		});
	}

	this.openDialog = function(timestamp) { 
		$('#'+this.divId).dialog('open');
		if(!this.isLoaded) {
			this.loadContent(timestamp);
		}
		return false;
	}

	this.loadContent = function(timestamp) { 
		var that = this;

		$.ajax({
			url: "?action=openEventWindow&category=calendar",
			data: {calendarAction: "add", timestamp: timestamp},
			type: "GET",
			dataType: "html",
			success: function (response) {
				$("#"+that.divId).html(response);
				that.isLoaded = true;
      		}
		});
	}
	
	this.add = function() {
		var that = this;
		var timestamp = $("#timestamp").val();
		var title = $("#title").val();
		var description = $("#description").val();

		$.ajax({
			url: "?action=addUpdateEvent&category=calendar",
			data: {userId: calendarPage.userId, timestamp: timestamp, title: title, description: description},
			type: "GET",
			dataType: "html",
			success: function (response) { 
				$("#"+that.addEventDiv).html(response);
				if (response == '') {
					$("#"+that.divId).dialog('close'); 
				    that.divId.isLoaded = false;
					window.location.href="?action=browseCategory&category=calendar&timestamp="+timestamp;
				}
			}
		});
	}
	
}

function calendarUpdateEvent() {
	this.divId = 'updateCalendarEventContainer';
	this.addEventDiv = 'addEventWindow';
	this.isLoaded = false;

	this.iniAddDialog = function(divId) {
		divId = typeof divId !== 'undefined' ? divId : this.divId;
		if(divId != this.divId) {
			this.divId = divId;
		}

		var that = this;
		$("#"+divId).dialog({
			width: 550,
			height: 250,
			autoOpen: false,
			resizable: true,
			dragable: true,
			modal: true,
			buttons: {
				'Cancel': function() {
					$(this).dialog('close');
					that.isLoaded = false;
				},
				'Update': function() {
					that.update();
				},
				'Delete': function() {
					that.deleteEvent();
				}
			}
		});
	}

	this.openDialog = function(eventId) { 
		$('#'+this.divId).dialog('open');
		if(!this.isLoaded) {
			this.loadContent(eventId);
		}
		return false;
	}

	this.loadContent = function(eventId) {
		var that = this;

		$.ajax({
			url: "?action=openEventWindow&category=calendar",
			data: {calendarAction: "update", eventId: eventId},
			type: "GET",
			dataType: "html",
			success: function (response) {
				$("#"+that.divId).html(response);
				that.isLoaded = true;
      		}
		});
	}
	
	this.update = function() {
		var that = this;
		var eventId = $("#eventId").val();
		var timestamp = $("#timestamp").val();
		var title = $("#title").val();
		var description = $("#description").val();

		$.ajax({
			url: "?action=addUpdateEvent&category=calendar",
			data: {eventId: eventId, userId: calendarPage.userId, timestamp: timestamp, title: title, description: description},
			type: "GET",
			dataType: "html",
			success: function (response) {
				$("#"+that.addEventDiv).html(response);
				if (response == '') {
					$("#"+that.divId).dialog('close'); 
				    that.divId.isLoaded = false;
					window.location.href="?action=browseCategory&category=calendar&timestamp="+timestamp;
				}
			}
		});
	}
	
	this.deleteEvent = function() {
		var that = this;
		var eventId = $("#eventId").val();
		var timestamp = $("#timestamp").val();
		
		$.ajax({
			url: "?action=deleteEvent&category=calendar",
			data: {eventId: eventId},
			type: "GET",
			dataType: "html",
			success: function () { 
				$("#"+that.divId).dialog('close'); 
				that.divId.isLoaded = false;
				window.location.href="?action=browseCategory&category=calendar&timestamp="+timestamp;
			}
		});
	}
	
}

function eventsOverView() {
	
	this.viewCalendarEvent = function(calendarEvents) { 
		alert(calendarEvents);
	}
}
			
		
function CalendarPage() {
	this.calendarAddEvent = new calendarAddEvent();
	this.calendarUpdateEvent = new calendarUpdateEvent();
	this.eventsOverView = new eventsOverView();
	this.userId = false;
}

//	global reminderPage object
var calendarPage;

$(function() {
	//	ini global object
	calendarPage = new CalendarPage();
	calendarPage.calendarAddEvent.iniAddDialog();
	calendarPage.calendarUpdateEvent.iniAddDialog();
});
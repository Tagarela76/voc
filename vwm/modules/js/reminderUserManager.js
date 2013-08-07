function ReminderManager()
{
    this.getReminderUserList = function()
    {
        var filterReminderList = $('#filterReminderList').val();
        var facilityId = $('#facilityId').val();
        window.location.href = '?action=browseCategory&category=facility&id='+facilityId+'&bookmark=reminderUsers&tab='+filterReminderList;
    }
}

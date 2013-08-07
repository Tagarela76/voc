<script type='text/javascript'>
    var reminderManager = new ReminderManager();
</script>


<div style="float: right; margin: 0 30px 20px 0">
    <select id='filterReminderList' onchange="reminderManager.getReminderUserList();">
        <option value="reminderUsers" {if $filterTab == "reminderUsers"}selected='selected'{/if}>
            Reminder Users
        </option>
        <option value="reminderEmails" {if $filterTab == "reminderEmails"}selected='selected'{/if}>
            Reminder Emails
        </option>
    </select>
</div>
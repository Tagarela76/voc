{literal}
    <script>
        var reminderUserManager = new ReminderUserManager();
        //var temporaryReminderUserId = 0;
        reminderUserManager.facilityId = '{/literal}{$facilityId}{literal}';
        reminderUserManager.initialize('{/literal}{$facilityReminderUsersEmailListJSon}{literal}');

    </script>
{/literal}
<table>
    <tr>
        <td width='30%'>
            Add User Email
        </td>
        <td width='50%' >
            <input type='text' id='userEmail'>
            <div style="color: #B22222" id='emailError' hidden="hidden">Invalid Email Address</div>
        </td>
        <td width='20%'>
            <div style='text-align: right'>
                <input type='button' class="button" value='Add New Email' onclick="reminderUserManager.addReminderUser()">
            </div>
        </td>
    </tr>
</table>

<table class="users" align="center" cellpadding="0" cellspacing="0" id='dialogReminderUsersListContainer'>
    <tr class="users_u_top_size users_top_blue">
        <td width='10%'>
            Check
        </td>
        <td width='90%'>
            Email
        </td>
    </tr>
    {foreach from=$facilityReminderUsersEmailList item=facilityReminderUsersEmail}
        <tr>
            <td height="20">
                <input type='checkbox' id='reminderUserEmail_{$facilityReminderUsersEmail->getId()}' value="{$facilityReminderUsersEmail->getId()}" 
                {if in_array($facilityReminderUsersEmail->getId(),$reminderUsersIds)}checked='checked'{/if}>
            </td>
            <td height="20">
                {$facilityReminderUsersEmail->getEmail()|escape}
            </td>
        </tr>
    {/foreach}

</table>
<div>
    <div>
        <img src='{$iconPath}' height="42" width="60"/>
    </div>
    <div style="margin: 20px 0 0 0;">
        <div>
            A friendly reminder from your friends at Gyant Compliance:
        </div>
        <div style="margin: 10px 0 10px 0;">
            Reminder: <b>{$reminder->getName()|escape}</b><br>
            {if $reminder->getDescription() && $reminder->getDescription()!='NULL'}
                Description: <b>{$reminder->getDescription()|escape}</b>
            {/if}
        </div>
        {if $isBeforehandReminder}
            <div  style="margin: 10px 0 10px 0;">
                Reminder Delivary date <b>{$reminder->getDeliveryDateInOutputFormat()|escape}</b><br>
            </div>
        {/if}
        <div>
            Thank you for your continued business with us!
        </div>
    </div>
</div>



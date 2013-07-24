{if $request.category == "facility" and $request.bookmark == "logbook"}
    <div>
        {if $request.tab == "logbook" || !$request.tab}
            <a href="?action=browseCategory&category=facility&id={$request.id|escape}&bookmark=logbook&tab=logbook" class="active_link">History</a>
            <a href="?action=browseCategory&category=facility&id={$request.id|escape}&bookmark=logbook&tab=logbookPendingRecord" >Pending</a>
            <a href="?action=browseCategory&category=facility&id={$request.id|escape}&bookmark=logbook&tab=logbookRecurring">Recurring Logbook Record</a>
            <a href="?action=browseCategory&category=facility&id={$request.id|escape}&bookmark=logbook&tab=inspectionPerson">Inspection Persons</a>
            <a href="?action=browseCategory&category=facility&id={$request.id|escape}&bookmark=logbook&tab=logbookEquipment">Logbook Equipment</a>
            <a href="?action=browseCategory&category=facility&id={$request.id|escape}&bookmark=logbook&tab=logbookCustomDescription">Logbook Custom Description</a>
        {elseif $request.tab == "inspectionPerson"}
            <a href="?action=browseCategory&category=facility&id={$request.id|escape}&bookmark=logbook&tab=logbook" >History</a>
            <a href="?action=browseCategory&category=facility&id={$request.id|escape}&bookmark=logbook&tab=logbookPendingRecord" >Pending</a>
            <a href="?action=browseCategory&category=facility&id={$request.id|escape}&bookmark=logbook&tab=logbookRecurring">Recurring Logbook Record</a>
            <a href="?action=browseCategory&category=facility&id={$request.id|escape}&bookmark=logbook&tab=inspectionPerson" class="active_link">Inspection Persons</a>
            <a href="?action=browseCategory&category=facility&id={$request.id|escape}&bookmark=logbook&tab=logbookEquipment">Logbook Equipment</a>
            <a href="?action=browseCategory&category=facility&id={$request.id|escape}&bookmark=logbook&tab=logbookCustomDescription">Logbook Custom Description</a>
        {elseif $request.tab == "logbookEquipment"}
            <a href="?action=browseCategory&category=facility&id={$request.id|escape}&bookmark=logbook&tab=logbook" >History</a>
            <a href="?action=browseCategory&category=facility&id={$request.id|escape}&bookmark=logbook&tab=logbookPendingRecord" >Pending</a>
            <a href="?action=browseCategory&category=facility&id={$request.id|escape}&bookmark=logbook&tab=logbookRecurring">Recurring Logbook Record</a>
            <a href="?action=browseCategory&category=facility&id={$request.id|escape}&bookmark=logbook&tab=inspectionPerson" >Inspection Persons</a>
            <a href="?action=browseCategory&category=facility&id={$request.id|escape}&bookmark=logbook&tab=logbookEquipment" class="active_link" >Logbook Equipment</a>
            <a href="?action=browseCategory&category=facility&id={$request.id|escape}&bookmark=logbook&tab=logbookCustomDescription">Logbook Custom Description</a>
        {elseif $request.tab == "logbookCustomDescription"}
            <a href="?action=browseCategory&category=facility&id={$request.id|escape}&bookmark=logbook&tab=logbook" >History</a>
            <a href="?action=browseCategory&category=facility&id={$request.id|escape}&bookmark=logbook&tab=logbookPendingRecord" >Pending</a>
            <a href="?action=browseCategory&category=facility&id={$request.id|escape}&bookmark=logbook&tab=logbookRecurring">Recurring Logbook Record</a>
            <a href="?action=browseCategory&category=facility&id={$request.id|escape}&bookmark=logbook&tab=inspectionPerson" >Inspection Persons</a>
            <a href="?action=browseCategory&category=facility&id={$request.id|escape}&bookmark=logbook&tab=logbookEquipment"  >Logbook Equipment</a>
            <a href="?action=browseCategory&category=facility&id={$request.id|escape}&bookmark=logbook&tab=logbookCustomDescription" class="active_link">Logbook Custom Description</a>
        {elseif $request.tab == "logbookPendingRecord"}
            <a href="?action=browseCategory&category=facility&id={$request.id|escape}&bookmark=logbook&tab=logbook" >History</a>
            <a href="?action=browseCategory&category=facility&id={$request.id|escape}&bookmark=logbook&tab=logbookPendingRecord" class="active_link">Pending</a>
            <a href="?action=browseCategory&category=facility&id={$request.id|escape}&bookmark=logbook&tab=logbookRecurring">Recurring Logbook Record</a>
            <a href="?action=browseCategory&category=facility&id={$request.id|escape}&bookmark=logbook&tab=inspectionPerson" >Inspection Persons</a>
            <a href="?action=browseCategory&category=facility&id={$request.id|escape}&bookmark=logbook&tab=logbookEquipment"  >Logbook Equipment</a>
            <a href="?action=browseCategory&category=facility&id={$request.id|escape}&bookmark=logbook&tab=logbookCustomDescription">Logbook Custom Description</a>
        {elseif $request.tab == "logbookRecurring"}
            <a href="?action=browseCategory&category=facility&id={$request.id|escape}&bookmark=logbook&tab=logbook" >History</a>
            <a href="?action=browseCategory&category=facility&id={$request.id|escape}&bookmark=logbook&tab=logbookPendingRecord" >Pending</a>
            <a href="?action=browseCategory&category=facility&id={$request.id|escape}&bookmark=logbook&tab=logbookRecurring" class="active_link">Recurring Logbook Record</a>
            <a href="?action=browseCategory&category=facility&id={$request.id|escape}&bookmark=logbook&tab=inspectionPerson" >Inspection Persons</a>
            <a href="?action=browseCategory&category=facility&id={$request.id|escape}&bookmark=logbook&tab=logbookEquipment"  >Logbook Equipment</a>
            <a href="?action=browseCategory&category=facility&id={$request.id|escape}&bookmark=logbook&tab=logbookCustomDescription">Logbook Custom Description</a>
        {/if}
    </div>
{/if}
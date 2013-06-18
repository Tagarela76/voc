{if $request.category == "facility" and $request.bookmark == "logbook"}
    <div>
        {if $request.tab == "logbook" || !$request.tab}
            <a href="?action=browseCategory&category=facility&id={$request.id|escape}&bookmark=logbook&tab=logbook" class="active_link">Logbook</a>
            <a href="?action=browseCategory&category=facility&id={$request.id|escape}&bookmark=logbook&tab=inspectionPerson">Inspection Persons</a>
            <a href="?action=browseCategory&category=facility&id={$request.id|escape}&bookmark=logbook&tab=logbookEquipment">Logbook Equipment</a>
        {elseif $request.tab == "inspectionPerson"}
            <a href="?action=browseCategory&category=facility&id={$request.id|escape}&bookmark=logbook&tab=logbook" >Logbook</a>
            <a href="?action=browseCategory&category=facility&id={$request.id|escape}&bookmark=logbook&tab=inspectionPerson" class="active_link">Inspection Persons</a>
            <a href="?action=browseCategory&category=facility&id={$request.id|escape}&bookmark=logbook&tab=logbookEquipment">Logbook Equipment</a>
        {elseif $request.tab == "logbookEquipment"}
            <a href="?action=browseCategory&category=facility&id={$request.id|escape}&bookmark=logbook&tab=logbook" >Logbook</a>
            <a href="?action=browseCategory&category=facility&id={$request.id|escape}&bookmark=logbook&tab=inspectionPerson" >Inspection Persons</a>
            <a href="?action=browseCategory&category=facility&id={$request.id|escape}&bookmark=logbook&tab=logbookEquipment" class="active_link" >Logbook Equipment</a>
        {/if}
    </div>
{/if}
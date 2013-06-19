<div>
    <table width="100%" cellpadding="0" cellspacing="0" class="popup_table" align="center">
        <tr class="table_popup_rule">
            <td>
                Subtype settings
            </td>
            <td>
                
            </td>
        </tr>

        <tr>
            <td>
                Name
            </td>
            <td>
                <input type='textbox' name='subTypeName' id='subTypeName' value=''>
            </td>
        </tr>
        <tr>
            <td>
                Notes
            </td>
            <td>
                <input type='checkbox' name='hasNotes' id='hasNotes'>
            </td>
        </tr>
        <tr>
            <td>
                QTY
            </td>
            <td>
                <input type='checkbox' name='hasQty' id='hasQty'>
            </td>
        </tr>
        <tr>
            <td>
                Gauge
            </td>
            <td>
                <input type='checkbox' name='hasGauge' id='hasGauge' onchange='inspection.inspectionSubTypeAddDialog.getSubTypeDefaultGauge()'>
            </td>
        </tr>
        <tr id = 'defaultGauge' hidden="hidden">
            <td>
                Default Gauge Type
            </td>
            <td>
                <select id='gaugeType'>
                    <option value="none" {if $gaugeTypeId == 'none'}selected='true'{/if}>NONE</option>
                    {foreach from=$gaugeList item=gauge}
                        <option value="{$gauge.id|escape}" {if $gaugeTypeId == $gauge.id}selected='true'{/if}>{$gauge.name|escape}</option>
                    {/foreach}
                </select>
            </td>
        </tr>   
    </table>

</div>
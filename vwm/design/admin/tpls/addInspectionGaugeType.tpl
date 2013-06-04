<div>
    <table width="100%" cellpadding="0" cellspacing="0" class="popup_table" align="center">
        <tr class="table_popup_rule">
            <td>
                Gauge settings
            </td>
            <td>

            </td>
        </tr>
        <tr>
            <td>
                Name
            </td>
            <td>
                <input type='textbox' name='gaugeValueTypeName' id='inspectionGaugeName' value='' style = 'height: 25px'>
            </td>
        </tr>
        <tr>
            <td>
                Gauge Type
            </td>
            <td>
                <select style = 'height: 25px' id = 'inspectionGaugeType'>
                    {foreach from=$gaugeList item=gauge}
                        <option value="{$gauge.id|escape}">
                            {$gauge.name|escape}
                        </option>
                    {/foreach}
                </select>
            </td>
        </tr>
    </table>

</div>
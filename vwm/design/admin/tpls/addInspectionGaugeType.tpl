<table height = '100px'>
    <tr>
        <td  width='70px' style="font-size: 15">
            Name
        </td>
        <td>
            <input type='textbox' name='gaugeValueTypeName' id='inspectionGaugeName' value='' style = 'height: 25px'>
        </td>
    </tr>
    <tr>
        <td  width='100px' style="font-size: 15">
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
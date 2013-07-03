<select onchange="itlManager.gauges.changeGaugeUnitType()" id='gaugeDimension'  style='width: 60px;'>
    {foreach from=$unitTypeList item='unitType'}
        <option value='{$unitType->getUnittypeId()|escape}'>
            {$unitType->getName()|escape}
        </option>
    {/foreach}
</select>
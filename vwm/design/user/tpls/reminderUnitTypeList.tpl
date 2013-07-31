<select style="width: 100px" id='reminderUnitTypeList' name='reminderUnitTypeList'>
    {foreach from=$reminderUnitTypeList item='reminderUnitType'}
        <option value="{$reminderUnitType->getUnitTypeId()|escape}">
            {$reminderUnitType->getName()|escape}
        </option>
    {/foreach}
</select>
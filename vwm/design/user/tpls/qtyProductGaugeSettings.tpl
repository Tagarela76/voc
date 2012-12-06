<div>
    <table class="popup_table" align="left" cellspacing="0" cellpadding="0"> 
        <tr>
            <td>
                Limit:&nbsp;
            </td>
            <td>
                <div align="left" style="float: left;">	<input type='text' name='limit' id='limit' value='{$data->limit|escape}' /></div>												
                {foreach from=$violationList item="violation"}
                    {if $violation->getPropertyPath() eq 'limit'}							
                    {*ERROR*}					
                    <div class="error_img" style="float: left;"><span class="error_text">{$violation->getMessage()}</span></div>
                    {*/ERROR*}						    
                    {/if}
                {/foreach}
            </td>
        </tr>
        <tr>
            <td>
                Unittype:&nbsp;
            </td>
            <td>
                <div align="left" style="float: left;">	
                <select name="unit_type" id="unit_type">
                {section name=i loop=$unitTypeList}										
                        <option value='{$unitTypeList[i].unittype_id}' {if $unitTypeList[i].unittype_id eq $data->unit_type}selected="selected"{/if}> {$unitTypeList[i].name}</option>										
                {/section}
                </select>									
            </div>	
            </td>
        </tr>
        <tr>
            <td>
                Period:&nbsp;
            </td>
            <td>
                <div align="left" style="float: left;">	
                <select name="period" id="period">
                    {foreach from=$periodOptions key='periodName' item='periodValue'}										
                        <option value='{$periodValue}' {if $periodValue eq $data->period}selected="selected"{/if}> {$periodName}</option>										
                    {/foreach}
                </select>									
            </div>	
            </td>
        </tr> 
    </table>
    <input type='hidden' name='id' id='id' value='{$data->id|escape}' />
    <input type='hidden' name='facility_id' id='facility_id' value='{$data->facility_id|escape}' />
</div>
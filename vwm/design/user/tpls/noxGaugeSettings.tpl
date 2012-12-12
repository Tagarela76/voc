<div>
    <table class="popup_table" align="left" cellspacing="0" cellpadding="0"> 
		<tr>
            <td>
                Gauge:&nbsp;
            </td>
            <td>
                {include file='tpls/selectGauge.tpl'}
            </td>
        </tr>
        
        <tr>
            <td>
                NOx:&nbsp;
            </td>
            <td>
                <div align="left" style="float: left;">	<input type='text' name='limit' id='limit' value='{$noxLimit|escape}' /></div>
               
            </td>
        </tr>
	
        <tr>
            <td>
                Period:&nbsp;
            </td>
            <td>
                <div align="left" style="float: left;">	
					Monthly
				</div>	
            </td>
        </tr>
    </table>
    <input type='hidden' name='id' id='id' value='{$data->id|escape}' />
    <input type='hidden' name='facility_id' id='facility_id' value='{$facilityId|escape}' />
	<input type='hidden' name='gaugeType' id='gaugeType' value='{$gaugeType|escape}' />
</div>
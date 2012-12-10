<div>
	<select onchange="selectProductGauge()" id='selectProductGauge'>
		{foreach from=$gauges item="gauge"}
			<option value='{$gauge->id}' {if $selectProductGauge==$gauge->id} selected="selected" {/if}>{$gauge->name}</option>
			{/foreach}
	</select>
</div>





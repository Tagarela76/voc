<div>
	<select onchange="selectProductGauge()" id='selectProductGauge'>
		{foreach from=$gauges item="gauge" key="name"}
			<option value='{$gauge}' {if $selectProductGauge==$gauge} selected="selected" {/if}>{$name}</option>
			{/foreach}
	</select>
</div>





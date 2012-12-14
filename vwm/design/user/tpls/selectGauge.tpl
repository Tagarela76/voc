<div>
	<select onchange="selectProductGauge()" id='selectProductGauge'>
		{foreach from=$gauges item="gauge" key="id"}
			<option value='{$id}' {if $selectProductGauge==$id} selected="selected" {/if}>{$gauge}</option>
			{/foreach}
	</select>
</div>





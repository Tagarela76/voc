{*qty product indicator bar*}

<!--<div style="height: 25px;">-->
<tr>
	<td style="padding: 0px;">
		<div class='gauge_text'>{$gauge->getGaugeTypeName()}</div>
	</td>
	<td>
		<div style="float:right;">
			<div class="widhtsh" style="float:left;">
				<div class="gray">
					<div style="position: absolute; z-index: 1; margin-left:4px; text-shadow: #fff 0.2em 0.2em 0.2em;">

					</div>
					<div class="widhtrelac">
						{if $gauge->getGaugeType()==4 }
							<div class="nox_indicator_colors" style="width:{$gauge->getPxCount()}px;">

							</div>
						{else}
							<div class="colors" style="width:{$gauge->getPxCount()}px;">

							</div>
						{/if}
					</div>

				</div>
			</div>

		</div>
	</td>
	<td>
		{$gauge->getCurrentUsage()}/<b>{$gauge->getLimit()}</b> {$gauge->getUnitTypeName()}	   
	</td>
</tr>
<!--</div>-->
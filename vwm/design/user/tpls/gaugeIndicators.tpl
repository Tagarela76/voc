{*qty product indicator bar*}

<!--<div style="height: 25px;">-->
<tr style='float: left;'>
	<td style="padding: 0px; width: 120px;">
		<div class='gauge_text'>{$gauge->getGaugeTypeName()}</div>
	</td>
	<td style="width: 202px;">
		<div style="float:right;">
			<div class="widhtsh" style="float:left;">
				<div class="gray">
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
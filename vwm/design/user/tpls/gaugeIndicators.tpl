{*qty product indicator bar*}

<!--<div style="height: 25px;">-->
<tr style='float: left;'>
	<td style="padding: 0px; width: 130px;">
		<div class='gauge_text'>{$gauge->getGaugeTypeName()}</div>
	</td>
	<td style="width: 202px;">
		<div style="float:right;">
			<div class="widhtsh" style="float:left;">
				<div class="gray">
					<div class="widhtrelac">
						{if $gauge->getPeriod()==1}
							{if $gauge->getGaugeType()==4 }
								<div class="annually_nox_indicator_colors" style="width:{$gauge->getPxCount()}px;">

								</div>
							{else}
								<div class="annually_colors" style="width:{$gauge->getPxCount()}px;">

								</div>
							{/if}
						{else}
							{if $gauge->getGaugeType()==4 }
								<div class="nox_indicator_colors" style="width:{$gauge->getPxCount()}px;">

								</div>
							{else}
								<div class="colors" style="width:{$gauge->getPxCount()}px;">

								</div>
							{/if}
						{/if}
					</div>

				</div>
			</div>

		</div>
	</td>
	<td style="padding:0px;">
		{$gauge->getCurrentUsage()}/<b>{$gauge->getLimit()}</b> {$gauge->getUnitTypeName()}	   
	</td>
</tr>
<!--</div>-->
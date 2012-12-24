<tr>

	<td style="padding: 0px;">
		<div class='gauge_text'>NOx</div>
	</td>

	<td>
		<div style="float:right;">
			<div class="widhtsh" style="float:left;">
				<div class="nox_indicator">
					{if $noxPeriod==1}
						{if $_gauge}
							<div class="widhtrelac">
								<div class="annually_nox_indicator_colors" style="width:{$_gauge.pxCount}px;">
								</div>
							</div>
						{else}
							<div class="widhtrelac">
								<div class="annually_nox_indicator_colors" style="width:{$noxPxCount}px;">

								</div>
							</div>
						{/if}
					{else}
						{if $_gauge}
							<div class="widhtrelac">
								<div class="nox_indicator_colors" style="width:{$_gauge.pxCount}px;">
								</div>
							</div>
						{else}
							<div class="widhtrelac">
								<div class="nox_indicator_colors" style="width:{$noxPxCount}px;">

								</div>
							</div>
						{/if}
					{/if}
				</div>
			</div>

		</div>
	</td>

	<td>
		{if $_gauge}
			{$_gauge.currentUsage}/<b>{$_gauge.limit}</b> {$_gauge.unitType}
		{else}
			{$noxCurrentUsage}/<b>{$noxLimit}</b>
		{/if}
	</td>
	
</tr>
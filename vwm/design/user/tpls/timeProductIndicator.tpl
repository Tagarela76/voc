{*qty product indicator bar*}

<!--<div style="height: 25px;">-->
<tr>
	<td style="padding: 0px;">
		<div class='gauge_text'>Time Spent</div>
	</td>
	<td>
		<div style="float:right;">
			<div class="widhtsh" style="float:left;">
				<div class="gray">
					<div style="position: absolute; z-index: 1; margin-left:4px; text-shadow: #fff 0.2em 0.2em 0.2em;">

					</div>
					{if $timePeriod==1}
						{if $overritenTimeProductCount}
							<div class="widhtrelac">
								<div class="annually_colors" style="width:{$timeProductCount}px;">

								</div>
							</div>
						{else}
							<div class="widhtrelac">
								<div class="annually_colors" style="width:{$timeProductCount}px;">

								</div>
							</div>
						{/if}
					{else}
						{if $overritenTimeProductCount}
							<div class="widhtrelac">
								<div class="colors" style="width:{$timeProductCount}px;">

								</div>
							</div>
						{else}
							<div class="widhtrelac">
								<div class="colors" style="width:{$timeProductCount}px;">

								</div>
							</div>
						{/if}
					{/if}
				</div>
			</div>

		</div>
	</td>
	<td>
		{if $overritenTimeProductLimit}
			{$overritenCurrenProductTime}/<b>{$overritenTimeProductLimit}</b> {$overritenUnitType}	   
		{else}
			{$currenProductTime}/<b>{$timeProductLimit}</b> {$unitType}	   
		{/if}
	</td>
</tr>
<!--</div>-->
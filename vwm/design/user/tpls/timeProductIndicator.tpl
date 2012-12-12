{*qty product indicator bar*}

<div style="height: 25px;">
	<div style="float:left; font-size: 12px; font-weight: bold; text-align: center;">Time Spent &nbsp; &nbsp; </div>
	<div style="float:right;">
		<!--<div style="float:right;padding: 2px 0px 0px 5px; ">
		{$currenProductTime}/<b>{$timeProductLimit}</b>&nbsp; {$unitType}	   
		</div>-->
		
		<div class="widhtsh" style="float:left;">
			<div class="gray">
				<div style="position: absolute; z-index: 1; margin-left:4px; text-shadow: #fff 0.2em 0.2em 0.2em;">
					{if $overritenTimeProductLimit}
						{$overritenCurrenProductTime}/<b>{$overritenTimeProductLimit}</b> {$overritenUnitType}	   
					{else}
						{$currenProductTime}/<b>{$timeProductLimit}</b> {$unitType}	   
					{/if}
				</div>
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
			</div>
		</div>

	</div>
</div>
{*qty product indicator bar*}

<div>
	<div style="float:left; font-size: 12px; font-weight: bold; text-align: center;"> Product's Spent Time &nbsp; &nbsp; </div>
	<div style="float:right;">
		<!--<div style="float:right;padding: 2px 0px 0px 5px; ">
		{$currenProductTime}/<b>{$timeProductLimit}</b>&nbsp; {$unitType}	   
		</div>-->
		<div class="widhtsh" style="float:left;">
			<div class="gray">
				<div style="margin: 0px 0px 0px 120px; position: absolute; z-index: 1">
					{$currenProductTime}/<b>{$timeProductLimit}</b> {$unitType}	   
				</div>
				<div class="widhtrelac">
					<div class="colors" style="width:{$timeProductCount}px;">
						
					</div>
				</div>
			</div>
		</div>

	</div>
</div>
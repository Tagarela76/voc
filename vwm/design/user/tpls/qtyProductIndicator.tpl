{*qty product indicator bar*}

<div style="height: 25px;">
	<div style="float:left; font-size: 12px; font-weight: bold; text-align: center;"> Product's Quantity &nbsp; &nbsp; </div>
	<div style="float:right;">
		<!--<div style="float:right;padding: 2px 0px 0px 5px; ">
		{$currenProductQty}/<b>{$qtyProductLimit}</b>&nbsp; {$productQtyUnitType}	   
		</div>-->
		<div class="widhtsh" style="float:left;">
			<div class="gray">
				<div style=" position: absolute; z-index: 1;margin-left:4px; text-shadow: #fff 0.2em 0.2em 0.2em;">					
					{if $overritenQtyProductLimit}
						{$overritenCurrenProductQty}/<b>{$overritenQtyProductLimit}</b>&nbsp; {$overritenUnitType}	   
					{else}
						{$currenProductQty}/<b>{$qtyProductLimit}</b>&nbsp; {$productQtyUnitType}	   
					{/if}
				</div>
					{if $overritenQtyProductCount}
						<div class="widhtrelac">
							<div class="colors" style="width:{$overritenQtyProductCount}px;">
							</div>
						</div>
					{else}
						<div class="widhtrelac">
							<div class="colors" style="width:{$pxQtyProductCount}px;">
							</div>
						</div>
					{/if}
			</div>
		</div>

	</div>
</div>



{*qty product indicator bar*}

<div>
	<div style="float:left; font-size: 12px; font-weight: bold; text-align: center;"> Product's Quantity &nbsp; &nbsp; </div>
	<div style="float:right;">
		<!--<div style="float:right;padding: 2px 0px 0px 5px; ">
		{$currenProductQty}/<b>{$qtyProductLimit}</b>&nbsp; {$productQtyUnitType}	   
		</div>-->
		<div class="widhtsh" style="float:left;">
			<div class="gray">
				<div style=" position: absolute; margin: 0px 0px 0px 120px; z-index: 1;">
					{if $overritenCurrenProductQty}
						{$overritenCurrenProductQty}/<b>{$overritenQtyProductLimit}</b>&nbsp; {$overritenUnitType}	   
					{else}
						{$currenProductQty}/<b>{$qtyProductLimit}</b>&nbsp; {$productQtyUnitType}	   
					{/if}
				</div>
				<div class="widhtrelac">
					<div class="colors" style="width:{$pxQtyProductCount}px;">
					</div>
				</div>
			</div>
		</div>

	</div>
</div>



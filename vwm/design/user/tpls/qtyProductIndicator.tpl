{*qty product indicator bar*}
<tr>
	<td style="padding: 0px;">
		<div class='gauge_text'> Product's Quantity</div>
	</td>
	<td>
		<div style="float:right;">
			<!--<div style="float:right;padding: 2px 0px 0px 5px; ">
			{$currenProductQty}/<b>{$qtyProductLimit}</b>&nbsp; {$productQtyUnitType}	   
			</div>-->
			<div class="widhtsh" style="float:left;">
				<div class="gray">
					{if $qtyPeriod==1}
						{if $overritenQtyProductCount}
							<div class="widhtrelac">
								<div class="annually_colors" style="width:{$overritenQtyProductCount}px;">
								</div>
							</div>
						{else}
							<div class="widhtrelac">
								<div class="annually_colors" style="width:{$pxQtyProductCount}px;">
								</div>
							</div>
						{/if}
					{else}
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
					{/if}
				</div>
			</div>

		</div>
	</td>
	<td>
		{if $overritenQtyProductLimit}
			{$overritenCurrenProductQty}/<b>{$overritenQtyProductLimit}</b>&nbsp; {$overritenUnitType}	   
		{else}
			{$currenProductQty}/<b>{$qtyProductLimit}</b>&nbsp; {$productQtyUnitType}	   
		{/if}
	</td>
<tr>




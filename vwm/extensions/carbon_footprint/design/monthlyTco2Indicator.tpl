{if $limits.monthly.show}
<div style="float:right;width:277px">	
    <div style="float:left;padding: 2px 0px 0px 5px;">
    	Monthly TCO2: {$usage.monthly}/<b>{$limits.monthly.value}</b>
    </div>
    <div class="widhtsh" style="float:left;">
        <div class="gray">
            <div class="widhtrelac">
            	{assign value="200" var=countPixels}				
            	{math assign=monthlyCountPixel equation="x/y*z" x=$usage.monthly y=$limits.monthly.value z=$countPixels}
            	{if $monthlyCountPixel gt $countPixels}
            		{assign value=$countPixels var=monthlyCountPixel}
            	{/if}
                <div class="colors" style="width:{$monthlyCountPixel}px;">
                </div>
            </div>
        </div>
    </div>
</div>
{/if}
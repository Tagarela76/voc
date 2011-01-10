{if $limits.annual.show}
<div style="float:right;width:277px">
	
    <div style="left:right;padding: 2px 0px 0px 5px;">
       Annual TCO2:  {$usage.annual}/<b>{$limits.annual.value}</b>
    </div>
    <div class="widhtsh" style="float:left;">
        <div class="gray">
            <div class="widhtrelac">
            	{assign value="200" var=countPixels}
            	{math assign=yearlyCountPixel equation="x/y*z" x=$usage.annual y=$limits.annual.value z=$countPixels}
            	{if $yearlyCountPixel gt $countPixels}
            	{assign value=$countPixels var=yearlyCountPixel}
            	{/if}
                <div class="colors" style="width:{$yearlyCountPixel}px;">
                </div>
            </div>
        </div>
    </div>
</div>
{/if}
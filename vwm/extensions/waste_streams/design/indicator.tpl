<div style="float:left;width:210px">   
    <div class="widhtsh" style="float:left;">
        <div class="gray">
            <div class="widhtrelac">
            	{assign value="200" var=countPixels}
            	{math assign=indicatorCountPixel equation="x/y*z" x=$value y=$limit z=$countPixels}
            	{if $indicatorCountPixel gt $countPixels}
            		{assign value=$countPixels var=indicatorCountPixel}
            	{/if}
                <div class="colors" style="width:{$indicatorCountPixel}px;">
                </div>
            </div>
        </div>
    </div>
</div>

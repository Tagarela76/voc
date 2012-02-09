{*voc indicator bar*}

{if $emissionLog=='true'}{include file="tpls:tpls/emissionLogPopup.tpl" }{/if}
<div style="float:right;width:290px">
    <div style="float:right;padding: 2px 0px 0px 5px; ">
       {if $emissionLog}<a href='#' onclick='$("#emissionLog").dialog("open");' style='color:black'> {$currentUsage}/<b>{$vocLimit}</b></a>{else}{$currentUsage}/<b>{$vocLimit}</b>{/if}	   
    </div>
    <div class="widhtsh" style="float:left;">
        <div class="gray">
            <div class="widhtrelac">
                <div class="colors" style="width:{$pxCount}px;">
                </div>
            </div>
        </div>
    </div>
				
</div>

{*voc indicator bar*}

{if $emissionLog=='true'}{include file="tpls:tpls/emissionLogPopup.tpl" }{/if}
<div>
	<div style="float:left; font-size: 12px; font-weight: bold; text-align: center;"> VOC &nbsp; &nbsp; </div>
	<div style="float:right;">
		<div style="float:right;padding: 2px 0px 0px 5px; ">
		{if $emissionLog}<a href='#' onclick='$("#emissionLog").dialog("open");' style='color:black'> {$currentUsage}/<b>{$vocLimit}</b></a>{else}{$currentUsage}/<b>{$vocLimit}</b>{/if}&nbsp; {$vocUnitType}	   
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
</div>



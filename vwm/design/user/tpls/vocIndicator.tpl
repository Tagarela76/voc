{*voc indicator bar*}

{if $emissionLog=='true'}{include file="tpls:tpls/emissionLogPopup.tpl" }{/if}
<div style="height:25px;">
	<div class="voc_gauge"> VOC </div>
	<div style="float:right;">
		<div class="widhtsh" style="float:left;">
			<div class="gray">
				<div style="margin-left:4px; position: absolute; z-index: 1; color: #000; text-shadow: #fff 0.2em 0.2em 0.2em; ">
		{if $emissionLog}<a href='#' onclick='$("#emissionLog").dialog("open");' style='color:black'> {$currentUsage}/<b>{$vocLimit}</b></a>{else}{$currentUsage}/<b>{$vocLimit}</b>{/if}&nbsp; {$vocUnitType}	   
		</div>
				<div class="widhtrelac">
					<div class="colors" style="width:{$pxCount}px;">
					</div>
				</div>
			</div>
		</div>

	</div>
</div>



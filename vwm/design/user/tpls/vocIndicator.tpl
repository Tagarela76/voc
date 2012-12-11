{*voc indicator bar*}

{if $emissionLog=='true'}{include file="tpls:tpls/emissionLogPopup.tpl" }{/if}
<div>
	<div class="voc_gauge"> VOC </div>
	<div style="float:right;">
		<div class="widhtsh" style="float:left;">
			<div class="gray">
				<div style="float:right;padding: 2px 0px 0px 5px; color: #000000;">
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



{*voc indicator bar*}

{*if $emissionLog=='true'}{include file="tpls:tpls/emissionLogPopup.tpl" }{/if*}

<tr>
	<td>
		<div class="gauge_text"> VOC </div>
	</td>

	<td style="width: 202px;">
		<div style="float:right;">
			<div class="widhtsh" style="float:left;">
				<div class="gray">
					<div class="widhtrelac">
						<div class="colors" style="width:{$pxCount}px;">
						</div>
					</div>
				</div>
			</div>

		</div>
	</td>
	<td style="width: 75px;">
		<div style="position: absolute; z-index: 1; color: #000; text-shadow: #fff 0.2em 0.2em 0.2em; ">
	{if $emissionLog}<a href='#' onclick='$("#emissionLog").dialog("open");' style='color:black'> {$currentUsage}/<b>{$vocLimit}</b></a>{else}{$currentUsage}/<b>{$vocLimit}</b>{/if}&nbsp; {$vocUnitType}	   
</div>
</td>
</tr>


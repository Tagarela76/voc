{*nox indicator bar*}
{*INSERT_NOX_LOG*}
{*Stupid Smarty does not support class constants*}
{*blocksToInsert.2 is equal to Controller::INSERT_NOX_LOG*}
	{if $blocksToInsert.1|@count > 0}
		{foreach from=$blocksToInsert.2 item="blockPath"}
			{include file="tpls:$blockPath"}
		{/foreach}
	{/if}
{*/INSERT_NOX_LOG*}

<div style="float:right;width:290px">
    <div style="float:right;padding: 2px 0px 0px 5px; ">
       <a href='#' onclick='$("#noxLog").dialog("open");' style='color:black'> {$currenNoxtUsage}/<b>{$noxLimit}</b></a>	   
    </div>
    <div class="widhtsh" style="float:left;">
        <div class="nox_indicator">
            <div class="widhtrelac">
                <div class="nox_indicator_colors" style="width:{$pxNoxCount}px;">
                </div>
            </div>
        </div>
    </div>
				
</div>
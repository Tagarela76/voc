{if $mixList|@is_array and $mixList|@count > 0}
<div style="padding:7px;">
 <div style="padding:7px;">
    <table class="users" align="center" cellpadding="0" cellspacing="0">
        <tr class="users_top_yellowgreen users_u_top_size">
			<td class="users_u_top_yellowgreen" width="5%" height="30">
                <div style='width:10%;  color:white;'>
                    Mix ID
                </div>
            </td>
			<td class="border_users_b" width="5%" height="30">
                <div style='width:10%;  color:white;'>
                    Step Number
                </div>
            </td>
			<td class="border_users_b" width="10%">
                <div style='width:10%;  color:white;'>
                    Product Name
                </div>
            </td>
            <td class="border_users_b" width="20%">
                <div style='width:40%;  color:white;'>
                    Description
                </div>
            </td>
            <td class="border_users_b" width="5%">
                <div style='color:white;'>
                    VOC
                </div>
            </td>
            <td class="border_users_b" width="10%">
                    <div style='color:white;'>
                        Creation Date
                    </div>
            </td>
			<td class="border_users_b" width="15%">
                    <div style='color:white;'>
                        Spray/spent time in minutes
                    </div>
            </td>
			<td class="border_users_b" width="7%">
				<div style='color:white;'>
					Material cost
				</div>
			</td>
			<td class="border_users_b" width="7%">
				<div style='color:white;'>
					Labor cost
				</div>
			</td>
			<td class="border_users_b" width="7%">
                    <div style='width:20%;  color:white;'>
                        Paint costs
                    </div>
            </td>
			<td class="border_users_b" width="9%">
                    <div style='width:20%;  color:white;'>
                        Total cost
                    </div>
            </td>
			<td class="users_u_top_r_yellowgreen" width="5%" height="30">
                <div style='width:10%;  color:white;'>
					edit
                </div>
            </td>
        </tr>
    {*BEGIN LIST*}
    {foreach from=$mixList item=mix}
	{assign var="index" value=$mix->mix_id}
    <tr class="hov_company"	height="10px">
		<td class="border_users_l border_users_b border_users_r" >
            <div>
				{if !$mixesCosts[$index].stepEmpty}
                    <a onclick="document.location='?action=viewDetails&category=mix&id={$mix->mix_id}&departmentID={$mix->department_id}'">
                        {$mix->mix_id|escape}
                    </a>
                {else}
                    <a  onclick="document.location='?action=addItem&category=mix&repairOrderId={$repairOrder->getId()}&departmentID={$mix->department_id}&stepID={$mix->mix_id}&stepIsCreated=1'">
                        add mix
                    </a>
				{/if}
            </div>
        </td>
		<td class="border_users_l border_users_b border_users_r" >
            <div>
					{$mixesCosts[$index].stepNumber|escape} &nbsp;
            </div>
        </td>
		<td class="border_users_b border_users_r">
			<div>
                {assign var="products" value=$mix->getProducts()}
				{foreach from=$products item=item}
					{if $item->is_primary}
						{$item->name|escape} &nbsp;
					{/if}
				{/foreach}
            </div>
        </td>
        <td class="border_users_b border_users_r">
    			<div>
                    {$mix->description|escape} &nbsp;
                </div>
        </td>
        <td class="border_users_b border_users_r">
            <div>
                {$mix->voc|escape} &nbsp;
            </div>
        </td>
        <td class="border_users_b border_users_r">
            <div>
                {$mix->creation_time|escape} &nbsp;
            </div>
        </td>
		<td class="border_users_b border_users_r">
            <div>
                {$mix->spent_time|escape} &nbsp;
            </div>
        </td>
		<td class="border_users_b border_users_r">
            <div>
                ${$mixesCosts[$index].materialCost}&nbsp;
            </div>
        </td>
		<td class="border_users_b border_users_r">
            <div>
                ${$mixesCosts[$index].laborCost}&nbsp;
            </div>
        </td>
		<td class="border_users_b border_users_r">
            <div>
				{if !$mixesCosts[$index].stepEmpty}
					${$mix->price|escape} &nbsp;
				{else}
					$0
				{/if}
            </div>
        </td>
		<td class="border_users_b border_users_r">
            <div>
               <b>${$mixesCosts[$index].totalCost}&nbsp;</b>
            </div>
        </td>
		 <td class="border_users_l border_users_b border_users_r" >
            <div align='center'>
                {if $mixesCosts[$index].stepId}
				<a onclick="stepManager.editStep({$mixesCosts[$index].stepId})">
					edit
				</a>
                {else}
                    --
				{/if}
            </div>
        </td>
    </tr>
    {/foreach}
	<!--<tr class="hov_company"	height="10px">
        <td class="border_users_l border_users_b border_users_r" colspan="5">
			<div></div>
        </td>
		<td class="border_users_b border_users_r">
            <div>
				{if $mixTotalSpentTime}
                <b> Total: </b>{$mixTotalSpentTime|escape} min
				{/if}
				&nbsp;
            </div>
        </td>
		<td class="border_users_b border_users_r">
            <div>
                <b> Total: </b> $ {$mixTotalPrice|escape} &nbsp;
            </div>
        </td>
    </tr>-->
    <tr>
        <td colspan="12" class="border_users_l border_users_r">
            &nbsp;
        </td>
    </tr>
    {*END LIST*}
    <tr>
        <td class="users_u_bottom" colspan="11" height="15">
        </td>
        <td class="users_u_bottom_r">
        </td>
    </tr>
</table>
</div>
{/if}

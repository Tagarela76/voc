{if $color eq "green"}
	{include file="tpls:tpls/notify/greenNotify.tpl" text=$message}
{/if}
{if $color eq "orange"}
	{include file="tpls:tpls/notify/orangeNotify.tpl" text=$message}
{/if}
{if $color eq "blue"}
	{include file="tpls:tpls/notify/blueNotify.tpl" text=$message}
{/if}
<div style="padding:7px;">
	<table style='width: 98%' align="center" cellpadding="0" cellspacing="0">
        <!--<tr class="users_top_yellowgreen">
            <td class="users_u_top_yellowgreen" width="27%" height="30">
                <span>View </span>
            </td>
            <td class="users_u_top_r_yellowgreen" width="300">
            </td>
        </tr>-->
		<tr>
            <td width="50%" valign="top" style="padding:0 2px 0 5px">

				<table class="users" align="center" cellpadding="0" cellspacing="0">
					<tr class="users_top_yellowgreen users_u_top_size">
						<td class="users_u_top_yellowgreen" width="37%" height="30">
							<span>View {$repairOrderLabel}</span>
						</td>
						<td class="users_u_top_r_yellowgreen" width="300">
						</td>
					</tr>
					<tr>
						<td class="border_users_l border_users_b" height="20">
							{$repairOrderLabel} number:
						</td>
						<td class="border_users_l border_users_b border_users_r">
							<div align="left">
								&nbsp; {$repairOrder->number|escape}
							</div>
						</td>
					</tr>
					<tr>
						<td class="border_users_l border_users_b" height="20">
							{$repairOrderLabel} description:
						</td>
						<td class="border_users_l border_users_b border_users_r">
							<div align="left">
								&nbsp; {$repairOrder->description|escape}
							</div>
						</td>
					</tr>
					<tr>
						<td class="border_users_l border_users_b" height="20">
							Customer Name:
						</td>
						<td class="border_users_l border_users_b border_users_r">
							<div align="left">
								&nbsp; {$repairOrder->customer_name|escape}
							</div>
						</td>
					</tr>
					<tr>
						<td class="border_users_l border_users_b" height="20">
							{$repairOrderLabel} Status:
						</td>
						<td class="border_users_l border_users_b border_users_r">
							<div align="left">
								&nbsp; {$repairOrder->status|escape}
							</div>
						</td>
					</tr>
					{if $instanceOfWorkOrder instanceof VWM\Apps\WorkOrder\Entity\AutomotiveWorkOrder}
						<tr>
							<td class="border_users_l border_users_b" height="20">
								{$repairOrderLabel} VIN number:
							</td>
							<td class="border_users_l border_users_b border_users_r">
								<div align="left">
									&nbsp; {$repairOrder->vin|escape}
								</div>
							</td>
						</tr>
					{/if}    
					<tr>
						<td class="border_users_l border_users_b" height="20">
							Departments:
						</td>
						<td class="border_users_l border_users_b border_users_r">
							<div align="left">
								&nbsp; {$woDepartments}
							</div>
						</td>
					</tr>
					<tr>
						<td class="border_users_l border_users_b" height="20">
							Process:
						</td>
						<td class="border_users_l border_users_b border_users_r">
							<div align="left">
								&nbsp; {$processName}
							</div>
						</td>
					</tr>
					<tr>
						<td height="20" class="users_u_bottom">
						</td>
						<td height="20" class="users_u_bottom_r">
						</td>
					</tr>
				</table>
			<td>


			<td width="50%" valign="top" style="padding:0 2px 0 5px">
				<table class="users" align="center" cellpadding="0" cellspacing="0">
					<tr class="users_top_yellowgreen users_u_top_size">
						<td class="users_u_top_yellowgreen" width="37%" height="30">
							<span>TOTALS</span>
						</td>
						<td class="users_u_top_r_yellowgreen" width="300">
						</td>
					</tr>
					<tr>
						<td class="border_users_l border_users_b" height="20">
							Paint costs:
						</td>
						<td class="border_users_l border_users_b border_users_r">
							${$mixTotalPrice|escape}
						</td>
					</tr>

					<tr>
						<td class="border_users_l border_users_b" height="20">
							Material costs:
						</td>
						<td class="border_users_l border_users_b border_users_r">
							${$materialCoat}
						</td>
					</tr>

					<tr>
						<td class="border_users_l border_users_b" height="20">
							Labor costs:
						</td>
						<td class="border_users_l border_users_b border_users_r">
							${$laborCoast}
						</td>
					</tr>

					<tr>
						<td class="border_users_l border_users_b" height="20">
							Total:
						</td>
						<td class="border_users_l border_users_b border_users_r">
							<b>${$totalCoast}</b>
						</td>
					</tr>

					<tr>
						<td class="border_users_l border_users_b" height="20">
							Spent time:
						</td>
						<td class="border_users_l border_users_b border_users_r">
							{$mixTotalSpentTime|escape} min
						</td>
					</tr>
					<tr>
						<td class="users_u_bottom" height="20">
						</td>
						<td class="users_u_bottom_r">
						</td>
					</tr>
				</table>

			<td>

		</tr>
		<tr>
			<td>
				<div  style="margin: 10px 0 0 25px; width: 100%">
					<input class='button' type="button" value="Add New Mix" onclick="document.location.href={$urlMixAdd}">
				</div>
			</td>
		</tr>
	</table>
	

    <div align="right">
    </div>    
</div>            
{include file="tpls:tpls/repairOrderMixList.tpl"}


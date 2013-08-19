<div style="padding:7px;">
    <table align="center" cellpadding="0" cellspacing="0">        
		<tr>
			<td style="padding:5px 5px 0 5px">
				<img src="../images/logoLarge.png" width="95px" height="75px"/>
			</td>
			<td style="padding:5px 5px 0 5px">
				<div  name="printPage" id="printPage" style="display:block;">
					<input type="button" onclick="printPage();" value="Print Page" style="font-size: 10px;"/>
				</div>
			</td>
		</tr>

        <tr>
            <td width="50%" valign="top" style="padding:0 2px 0 5px" colspan="2">
                <table width="100%" cellpadding="0" cellspacing="0" style="font-size: 12px;">
					<tr>
						<td colspan="2" style="border-width:0px;" height="20px">
							<b>{$repairOrderLabel} ID</b> {$repairOrder->id}
						</td>
					</tr>
					<tr>
						<td height="20">
							<b>{$repairOrderLabel} number:</b> {$repairOrder->number|escape}
						</td>
					</tr>
					<tr>
						<td height="20">
							<b>{$repairOrderLabel} description:</b> {$repairOrder->description|escape}
						</td>
					</tr>
					<tr>
						<td height="20">
							<b>Customer Name:</b> {$repairOrder->customer_name|escape}
						</td>
					</tr>
					<tr>
						<td height="20">
							<b>{$repairOrderLabel} Status:</b> {$repairOrder->status|escape}
						</td>
					</tr>
                    <tr>
						<td height="20">
							<b>{$repairOrderLabel} creation time:</b> {$creationTime|escape}
						</td>
					</tr>
                    <tr>
						<td height="20">
							<b>{$repairOrderLabel} process name:</b> {$processName|escape}
						</td>
					</tr>
					<tr>
						<td height="20">
							<b>{$repairOrderLabel} VIN number:</b> {$repairOrder->vin|escape}
						</td>
					</tr>
                    <tr>
						<td height="20">
							<b>{$repairOrderLabel} Profit:</b> {$profit|escape} $
						</td>
					</tr>
                    <tr>
						<td height="20">
							<b>{$repairOrderLabel} Overhead:</b> {$overhead|escape} $
						</td>
					</tr>
				</table>
            </td>
        </tr>
		<tr>
			<td>&nbsp;
			</td>
		</tr>
		<tr>
			<td valign="top" style="padding:0 2px 0 5px" colspan="2">
				<table width="100%" align="center" cellpadding="0" cellspacing="0" style="font-size: 11px;">
					<tr>
						<td width="5%" height="30">
							<b>Mix ID</b>
						</td>
						<td width="15%">
							<b>Product Name</b>
						</td>
						<td width="25%">
							<b>Description</b>
						</td>
						<td width="5%">
							<b>VOC</b>
						</td>
						<td width="10%">
							<b>Creation Date</b>
						</td>
                        <td width="10%">
							<b>Material cost</b>
						</td>
                        <td width="10%">
							<b>Labor cost</b>
						</td>
                        <td width="10%">
							<b>Paint cost</b>
						</td>
						<td  width="10%">
							<b>Mix Total Price</b>
						</td>
					</tr>
				{*BEGIN LIST*}
				{foreach from=$mixList item=mix}
                {assign var="index" value=$mix->mix_id}
				<tr height="10px">
					<td>
						{$mixesCosts[$index].stepNumber|escape} &nbsp;
					</td>
					<td>
						{assign var="products" value=$mix->getProducts()}
						{foreach from=$products item=item}
							{if $item->is_primary}
								{$item->name|escape} &nbsp;
							{/if}
						{/foreach}
					</td>
					<td>
						{$mix->description|escape} &nbsp;
					</td>
					<td>
						{$mix->voc|escape} &nbsp;
					</td>
					<td>
						{$mix->creation_time|escape} &nbsp;
					</td>
                    <td class="border_users_b border_users_r">
						$ {$mixesCosts[$index].materialCost|escape} &nbsp;
					</td>
                    <td class="border_users_b border_users_r">
						$ {$mixesCosts[$index].laborCost|escape} &nbsp;
					</td>
                    <td class="border_users_b border_users_r">
						$ {$mixesCosts[$index].paintCost|escape} &nbsp;
					</td>
					<td class="border_users_b border_users_r">
						$ {$mixesCosts[$index].totalCost|escape} &nbsp;
					</td>
				</tr>
				{/foreach}
				<tr	height="10px">
					<td colspan="5">
					</td>
                    <td>
						<b> Total Material Cost: </b> $ {$totalMaterialCost|escape} &nbsp;
					</td>
                    <td>
						<b> Total Labor Cost: </b> $ {$totalLaborCost|escape} &nbsp;
					</td>
                    <td>
						<b> Total Paint Cost: </b> $ {$totalPaintCost|escape} &nbsp;
					</td>
					<td>
						<b> Total: </b> $ {$mixTotalPrice|escape} &nbsp;
					</td>
				</tr>
                <tr	height="10px">
					<td colspan="8">
					</td>
					<td>
						<b> Work Order Total Price: </b> $ {$workOrdetTotalPrice|escape} &nbsp;
					</td>
				</tr>
				</table>
			</td>
		</tr>
        <tr>
            <td colspan="3" style="padding:5px 5px 0 5px">

            </td>
        </tr>   
	</table>

</div>

{literal}
	<script type="text/javascript" src="modules/js/jquery-1.5.2.js"></script>
	<script type="text/javascript">
	
		function printPage() {
			$('#printPage').css("display", "none");
			window.print();
			$('#printPage').css("display", "block");
		}
	</script>
{/literal}
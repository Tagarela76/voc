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
							<b>Repair Order ID</b> {$workOrder->id}
						</td>
					</tr>
					<tr>
						<td height="20">
							<b>Repair order number:</b> {$workOrder->number|escape}
						</td>
					</tr>
					<tr>
						<td height="20">
							<b>Repair order description:</b> {$workOrder->description|escape}
						</td>
					</tr>
					<tr>
						<td height="20">
							<b>Customer Name:</b> {$workOrder->customer_name|escape}
						</td>
					</tr>
					<tr>
						<td height="20">
							<b>Repair Order Status:</b> {$workOrder->status|escape}
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
						<td width="20%" height="30">
							<b>Mix ID</b>
						</td>
						<td width="30%">
							<b>Description</b>
						</td>
						<td width="10%">
							<b>VOC</b>
						</td>
						<td width="20%">
							<b>Creation Date</b>
						</td>
						<td  width="20%">
							<b>Price</b>
						</td>
					</tr>
				{*BEGIN LIST*}
				{foreach from=$mixList item=mix}
				<tr height="10px">
					<td>
						{$mix->mix_id|escape} &nbsp;
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
						$ {$mix->price|escape} &nbsp;
					</td>
				</tr>
				{/foreach}
				<tr	height="10px">
					<td colspan="4">
					</td>
					<td>
						<b> Total: </b> $ {$workOrder->totalPrice|escape} &nbsp;
					</td>
				</tr>
				</table>
			</td>
		</tr>
        <tr>
            <td colspan="2" style="padding:5px 5px 0 5px">

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
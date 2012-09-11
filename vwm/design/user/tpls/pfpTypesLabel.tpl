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
			<td>&nbsp;
			</td>
		</tr>
        <tr>
            <td width="50%" valign="top" style="padding:0 2px 0 5px" colspan="4">
				<b>PFP Type Name:</b> {$pfpTypes->name|escape}
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
							<b>PFP ID</b>
						</td>
						<td width="30%">
							<b>Description</b>
						</td>
						<td width="20%">
							<b>Ratio</b>
						</td>
						<td width="20%">
							<b>Products count</b>
						</td>
					</tr>
				{*BEGIN LIST*}
				{if $pfpProducts}
					{foreach from=$pfpProducts item=pfp}
					<tr height="10px">
						<td>
							{$pfp->getId()|escape} &nbsp;
						</td>
						<td>
							{$pfp->getDescription()|escape} &nbsp;
							<div>
								<table>
									{assign var="pfpProducts" value=$pfp->getProducts()}
									{foreach from=$pfpProducts item=item}
										<tr>
											<td>{$item->product_nr}</td>
											<td>{$item->name}</td>
										</tr>
									{/foreach}
								</table>
							</div>
						</td>
						<td>
							{$pfp->getRatio(false)|escape} &nbsp;{if $pfp->isRangePFP}(with range){/if} &nbsp;
						</td>
						<td>
							{$pfp->getProductsCount()|escape} &nbsp;
						</td>
					</tr>
					{/foreach}
				{/if}	
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
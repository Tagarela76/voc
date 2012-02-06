<html style="background:#4C505B">
	<head>
		<title>{if $result != 'false'}VOC-WEB-MANAGER: NEW ORDER{else}VOC-WEB-MANAGER: ERROR{/if}</title>
		<link href="style.css" rel="stylesheet" type="text/css">
		<link rel="shortcut icon" href="images/vocicon.ico" type="image/x-icon">
	</head>
	<body  style="background:#4C505B;">
		<div>	
			<div class="errors_list">Inventory System</div>
			<div class="errors_list_text">
				Please review this order
				<table style='color:white;'>
					<tr>
						<td>
							Facility
						</td>
						<td>
							{$facility.name}
						</td>
					</tr>
					<tr>
						<td>
							Phone
						</td>
						<td>
							{$facility.phone}
						</td>
					</tr>
					<tr>
						<td>
							Email
						</td>
						<td>
							<a style='color:white;' href='mailto:{$facility.email}'>{$facility.email}</a>
						</td>
					</tr>
				</table>
			</div>
		</div>
		
					{*include file='tpls:inventory/design/inventoryOrdersDetail.tpl'*}
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
						<table class="users" width="100%" cellpadding="0" cellspacing="0" align="center">
							<tr class="users_top_yellowgreen">
								<td class="users_u_top_yellowgreen" width="27%" height="30">
									<span>View Order</span>
								</td>
								<td class="users_u_top_r_yellowgreen" width="300">
								</td>
							</tr>
							<tr>
								<td class="border_users_l border_users_b" height="20">
									Order Name :
								</td>
								<td class="border_users_l border_users_r border_users_b">
									<div align="left">
										&nbsp;{$order->order_name}
									</div>
								</td>
							</tr>
							<tr>
								<td class="border_users_l border_users_b" height="20">
									Amount : 
								</td>
								<td class="border_users_l border_users_r border_users_b">
									<div align="left">
										&nbsp;{$inventory->amount}
									</div>
								</td>
							</tr>
							<tr>
								<td class="border_users_l border_users_b" height="20">
									Status : 
								</td>
								<td class="border_users_l border_users_r border_users_b">
									<div align="left">
										&nbsp;{if $order->order_status == 1}In Progress 
										{elseif $order->order_status == 2}Confirm
											{elseif $order->order_status == 3}Completed
												{elseif $order->order_status == 4}Canceled{/if}
									</div>
								</td>
							</tr>							
								<tr>
									<td class="border_users_l border_users_b" height="20">
										Price : 
									</td>
									<td class="border_users_l border_users_r border_users_b">
										<div align="left">
											&nbsp;{$order->order_total} $
										</div>
									</td>
								</tr>	
								<tr>
									<td class="border_users_l border_users_b" height="20">
										Discount : 
									</td>
									<td class="border_users_l border_users_r border_users_b">
										<div align="left">
											&nbsp;{$inventory->discount} %
										</div>
									</td>
								</tr>								
							<tr>
								<td class="border_users_l border_users_b" height="20">
									Total : 
								</td>
								<td class="border_users_l border_users_r border_users_b">
									<div align="left">
										&nbsp;{$order->order_total} $
									</div>
								</td>
							</tr>	

							<tr>
								<td class="border_users_l border_users_b" height="20">
									Date : 
								</td>
								<td class="border_users_l border_users_r border_users_b">
									<div align="left">
										{assign var=order_created_date value=$order->order_created_date}
										&nbsp;{$order_created_date->format($smarty.const.DEFAULT_DATE_FORMAT)}
									</div>
								</td>
							</tr>		

							<tr>
								<td height="15" class="users_u_bottom">
								</td>
								<td height="15" class="users_u_bottom_r">
								</td>
							</tr>
						</table>
					</div>
									
					<div  class="errors_list_text" style="float:right">						
						<input class="button" type="button" value="CONFIRM ORDER" onclick="location.href='?action=processororder&category=inventory&hash={$request.hash}&result=confirm'"/>						
						<input class="button" type="button" value="CANCEL ORDER" onclick="location.href='?action=processororder&category=inventory&hash={$request.hash}&result=cancel'"/>						
					</div>

	</body>
</html>





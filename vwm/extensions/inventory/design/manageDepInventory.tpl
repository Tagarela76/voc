<div>
	<form action="?action=addItem&category=inventory&departmentID={$request.departmentID}&tab={$request.tab}" method="POST" accept-charset="utf-8">	
		<div style="text-align:center;">
		<h1>Add/Delete inventory at {$department->getName()}</h1>
		</div>
		<div id="2tables">
	<div style="float:right;width:49%">
				<div class="padd_left">
					<b>{$department->getName()} inventories</b>
				</div>
				<div>
	<table cellpadding=0 cellspacing=0 class="users_DepInv"  align="center">
		<thead>
			<tr class="users_u_top_size users_top_blue">								
				<td  class="users_u_top_blue"><div class="padd_left">ID Number</div></td>
				<td>Inventory Name</td>
				<td class="users_u_top_r_blue" >Inventory Description</td>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td colspan="3">
					<table border="0" cellpadding=0 cellspacing=0 class="users_DepInventory">
						<tbody id="depInvTbody">
							{if $depInventory|@count > 0}
								{foreach from=$depInventory item=inventory}
								<tr id="{$inventory->getID()}" class="draggableDepInv users_u_top_size hov_DepInventory" >									
									<td><div>{$inventory->getID()}</div></td>
									<td><div>{$inventory->getName()}</div></td>
									<td><div>{$inventory->getDescription()}</div></td>
								</tr>
								{/foreach}
							{else}
								<tr id="emptyDepInv" class="users_u_top_size">
									<td>
										No inventories at department
									</td>
								</tr>								
							{/if}							
						</tbody>						
					</table>
				</td>
			</tr>
		</tbody>
		<tfoot>
			<tr class="users_u_top_size">
				<td colspan="2" class="users_u_bottom">
					<div class="padd_left"><i>Drag inventory to available ones to remove it from {$department->getName()}</i></div>
				</td>
				<td class="users_u_bottom_r"></td>
			</tr>							
		</tfoot>
	</table>
				</div>
				<div id="hiddenFields">
					{if $depInventory|@count > 0}
						{foreach from=$depInventory item=inventory}
						<input type="hidden" name="id[]" value="{$inventory->getID()}">
						{/foreach}
					{/if}
				</div>
	</div>
			
	<div style="float:left;width:50%">
				<div class="padd_left"><b>Available inventories</b></div>
				<div>
					<table cellpadding=0 cellspacing=0 class="users_DepInv"  align="center">
						<thead>
							<tr class="users_u_top_size users_top_blue">
								<td  class="users_u_top_blue"><div class="padd_left">ID Number</div></td>
								<td>Inventory Name</td>
								<td  class="users_u_top_r_blue">Inventory Description</td>
							</tr>
						</thead>
						<tbody>
						<tr>
							<td colspan="3">
					<table border="0" cellpadding=0 cellspacing=0 class="users_DepInventory">
						<tbody id="availableInvTbody">
							{if $facInventory|@count > 0}
								{foreach from=$facInventory item=inventory}								
								<tr id="{$inventory->getID()}" class="draggableAvailableInv users_u_top_size hov_DepInventory">									
									<td><div>{$inventory->getID()}</div></td>
									<td><div>{$inventory->getName()}</div></td>
									<td><div>{$inventory->getDescription()}</div></td>
								</tr>								
								{/foreach}
							{else}
								<tr id="emptyAvailableInv" class="users_u_top_size">
									<td>
										No inventories at facility
									</td>
								</tr>
							{/if}
						</tbody>
					</table>
						</td>
					</tr>
				</tbody>
				<tfoot>
					<tr class="users_u_top_size">
							<td colspan="2" class="users_u_bottom">
								<div class="padd_left"><i>Drag inventory to {$department->getName()} to add it to department</i></div>
							</td>
							<td class="users_u_bottom_r"></td>
					</tr>							
				</tfoot>
		</table>
				</div>
	</div>
			<div style="clear:both;"></div>
		</div>
				<table cellpadding="5" cellspacing="0" align="center" width="95%">
			<tr>
				<td>
		{*BUTTONS*}
		<div style="text-align:center;">
			<input type="hidden" name="savingFlag" value="yes">
			<input type="button" class="button" value="Cancel" onclick="location.href='?action=browseCategory&category=department&id={$request.departmentID}&bookmark=inventory&tab={$request.tab}'">
			<input type="submit" class="button" value="Save"></div>
									</td>
			</tr>
		</table>
	</form>
</div>
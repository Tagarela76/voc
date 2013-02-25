
<div style='margin: 15px 0 0 0'>
	<table>
		<tr>
			<td>
				<div  style="width:100px">
					Description
				</div>
			</td>
			<td>
				<div>
					<input type="text" value="" id = "resourceDescription" >
				</div>
			</td>
		</tr>
		<tr>
			<td>
				<div  style="width:150px">
					Quantity
				</div>
			</td>
			<td>
				<div>
					<input type="number" value="" id="resourceQty">
				</div>
			</td>
		</tr>
		<tr>
			<td>
				<div  style="width:150px">
					Resource type
				</div>
			</td>
			<td>
				<div>
					<select id ='resourceType' onchange="getUnitType()">
						{foreach from=$resourceType key=key item=type}
							<option id = '{$key}' value='{$key}'>
								{$type}
							</option>
						{/foreach}
					</select>
				</div>
			</td>
		</tr>
		<tr>
			<td>
				<div  style="width:150px">
					Unit type
				</div>
			</td>
			<td>
				<div>
					<select id = 'selectUnitType'>
						{foreach from=$unitTypeList item=unitType}
							<option value = '{$unitType->getUnittypeId()}'>
								{$unitType->getName()}
							</option>
						{/foreach}
					</select>
				</div>
			</td>
			<tr>
			<td>
				<div  style="width:150px">
					Rate
				</div>
			</td>
			<td>
				<div>
					<input type='number' value="" id = "resourceRate">
				</div>
			</td>
		</tr>
		</tr>
	</table>
</div>
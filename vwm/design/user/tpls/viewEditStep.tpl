{literal}
	<script type="text/javascript">
	//step initialization
	var departmentId = '{/literal}{$departmentId}{literal}';
	var stepId = {/literal}{$stepInstance->getId()}{literal};
	var repaitOrderId = '{/literal}{$repaitOrderId}{literal}';
	var templateResourceId = 0;
		
	var step = new Step();
	step.setId(stepId);
	step.setDescription({/literal}'{$stepInstance->getDescription()}'{literal});

	{/literal}{foreach from=$stepInstance->getResources() item=resource}{literal}
		templateResourceId++;
		var resource = new Resource();
			//resource.setId({/literal}{$resource->getId()}{literal});
			resource.setId(templateResourceId);
			resource.setDescription({/literal}'{$resource->getDescription()}'{literal});
			resource.setQty({/literal}{$resource->getQty()}{literal});
			resource.setRate({/literal}{$resource->getRate()}{literal});
			resource.setUnittypeId({/literal}{$resource->getUnittypeId()}{literal});
			resource.setResourceUnittypeId({/literal}{$resource->getResourceTypeId()}{literal});
			resource.setStepId({/literal}{$stepInstance->getId()}{literal});
			step.setResources(resource);
	{/literal}{/foreach}{literal}
		
	</script>
{/literal}

<div align="left">
	<!--Step-->
	<!--<div style="font-size: 30px; margin: 0px 0 0 0">
		Edit Step
	</div>-->
	<table style='width: 98%' align="left" cellpadding="0" cellspacing="0" >
		<tr>
			<td valign="top" style="padding:0 2px 0 5px; width: 30%">
				<table class="users" align="center" cellpadding="0" cellspacing="0">
					<tr class="users_top_yellowgreen users_u_top_size">
						<td class="users_u_top_yellowgreen" width="20%" height="30">
							<span>Edit Step</span>
						</td>
						<td class="users_u_top_r_yellowgreen" width="300">
						</td>
					</tr>
					<tr>
						<td class="border_users_l border_users_b" height="20">
							Step description
						</td>
						<td class="border_users_l border_users_b border_users_r">
							<input type="text" id="stepDescription" value='{$stepInstance->getDescription()}' onchange="changeStepDescription()" style="width: 400px">
						</td>
					</tr>
					<tr>
						<td class="users_u_bottom">
						</td>
						<td class="users_u_bottom_r">
						</td>
					</tr>
				</table>
						
			</td>
		</tr>

		<tr>
			<td style="width: 100px">
				<!--Resources-->
				<div style="margin: 10px 0 0 25px; width: 100%">
					Resources
				</div>
			</td>
		</tr>
		<tr>
			<td>
				<div style="margin: 10px 0 0 25px; width: 100%">
					<input type='button' class='button' value="Add Resource" onclick="stepSettings.checkNewDialog(0, 'add'); stepSettings.stepAddEditResource.openDialog();">
					<input type='button' class='button' value="Delete Resources" onclick = 'deleteResources()'>
				</div>
			</td>
		</tr>

		<tr>
			<td>
				<div style="padding:17px;">
					<table class="users" align="left" cellpadding="0" cellspacing="0" style='width: 100%' id='stepResourcesDetails'>
						<tr class="users_top_yellowgreen users_u_top_size">
							<td class="users_u_top_yellowgreen" width = "5%">
								<div style='width:20%;  color:white;'>
									Check
								</div>
							</td>
							<td class="border_users_b" width = '25%'>
								<div style='width:10%;  color:white;'>
									Description
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
							<td class="border_users_b" width="9%">
								<div style='width:20%;  color:white;'>
									Total cost
								</div>
							</td>
							<td class="users_u_top_r_yellowgreen" width="9%">
								<div style='width:20%;  color:white;'>
									Edit Resource
								</div>
							</td>
						</tr>
						<!-- use counter to set template id for resource-->
						{counter assign=count start=0 skip=1}
						<!--get step Resources-->
						{foreach from=$stepInstance->getResources() item=resource}
							<!--increase count-->
							{counter}
							<tr class="hov_company"	height="10px" id="resource_detail_{$count}">
								<td class="border_users_l border_users_b border_users_r" >
									<div align='center'>
										<input type="checkbox" value='{$count}' id = 'deleteCheckBox'>
									</div>
								</td>
								<td class="border_users_l border_users_b border_users_r" >
									<div style='width: 150px' id='resource_description_{$count}'>
										{$resource->getDescription()}
									</div>
								</td>
								<td class="border_users_l border_users_b border_users_r" id="material_cost_{$count}">
									<div align='center'>
										${$resource->getMaterialCost()}
									</div>
								</td>
								<td class="border_users_l border_users_b border_users_r" id = "labor_cost_{$count}">
									<div align='center'>
										${$resource->getLaborCost()}
									</div>
								</td>
								<td class="border_users_l border_users_b border_users_r" id = "total_cost_{$count}">
									<div align='center'>
										${$resource->getTotalCost()}
									</div>
								</td>
								<td class="border_users_b border_users_r">
									<div align='center' id = '{$count}'>
										<a onclick="stepSettings.checkNewDialog({$count}, 'edit'); stepSettings.stepAddEditResource.openDialog();">
											edit
										</a>
									</div>
								</td>
							</tr>
						{/foreach}
					</table>

				</div>
			</td>
		</tr>
		<tr>
			<td>
				<div style="margin: 10px 0 0 25px; ">
					<input type='button' class='button' value='<< Back' onclick="history.back()">
					<input type='button' class='button' value='save' onclick="saveStep()">
				</div>
			</td>
		</tr>
	</table>
</div>
<div id="resourceDetailsContainer" title="Add new resource" style="display:none;">Loading ...</div>
<input type='hidden' id='currentStep' value='{$stepInstance}'>
<input type='hidden' id='departmentId' value='{$departmentId}'>


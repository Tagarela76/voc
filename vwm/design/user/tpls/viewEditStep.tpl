{literal}
	<script type="text/javascript">
	//step initialization

	var departmentId = '{/literal}{$departmentId|escape}{literal}';
	var stepId = {/literal}{$stepInstance->getId()|escape}{literal};
	var repaitOrderId = '{/literal}{$repaitOrderId|escape}{literal}';
	//temporary resource id. resources get id from 1 to resource count
	var templateResourceId = 0;

	var step = new Step();
	step.setId(stepId);
	step.setDescription({/literal}"{$stepInstance->getDescription()|escape}"{literal});


	{/literal}
	{if $stepInstance->getResources()!=''}
		{foreach from=$stepInstance->getResources() item=resource}
			{if $resource->getResourceTypeId()!=2}
				{literal}
		templateResourceId++;
		var resource = new Resource();
			resource.setId(templateResourceId);
			resource.setDescription({/literal}'{$resource->getDescription()|escape}'{literal});
			resource.setQty({/literal}{$resource->getQty()|escape}{literal});
			resource.setRate({/literal}{$resource->getRate()|escape}{literal});
			resource.setUnittypeId({/literal}{$resource->getUnittypeId()|escape}{literal});
			resource.setResourceUnittypeId({/literal}{$resource->getResourceTypeId()|escape}{literal});
			resource.setStepId({/literal}{$stepInstance->getId()|escape}{literal});
			step.addResource(resource);
				{/literal}
			{/if}
		{/foreach}
	{/if}
	{literal}

	</script>
{/literal}

<div align="left">
	{*Step*}
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
							<input type="text" id="stepDescription" value='{$stepInstance->getDescription()|escape}' onchange="stepPage.stepEdit.changeStepDescription()" style="width: 400px">
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
				{*Resources*}
				<div style="margin: 10px 0 0 25px; width: 100%">
					Resources
				</div>
			</td>
		</tr>
		<tr>
			<td>
				<div style="margin: 10px 0 0 25px; width: 100%">
					<input type='button' class='button' value="Add Resource" onclick="stepPage.stepAddEditResource.checkNewDialog(0, 'add'); stepPage.stepAddEditResource.openDialog();">
					<input type='button' class='button' value="Delete Resources" onclick = 'stepPage.stepEdit.deleteResources()'>
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
								<div style='color:white;'>
									Total cost
								</div>
							</td>
							<td class="border_users_b users_u_top_r_yellowgreen" width="9%">
								<div style='color:white;'>
									Edit Resource
								</div>
							</td>
						</tr>
						{*Use counter to set template id for resource from 1 to resource count*}
						{counter assign=count start=0 skip=1}
						{*get step Resources*}
						{if $stepInstance->getResources()!=''}
							{foreach from=$stepInstance->getResources() item=resource}
                                {*check if resource type is not VOLUME. We can't edit such resource type as we edit mix in that case*}
								{if $resource->getResourceTypeId()!=2}
									{*increase count*}
									{counter}
									<tr class="hov_company"	height="10px" id="resource_detail_{$count}">
										<td class="border_users_l border_users_b" >
											<div align='center'>
												<input type="checkbox" value='{$count}' id = 'deleteCheckBox'>
											</div>
										</td>
										<td class="border_users_l border_users_b" >
											<div style='width: 150px' id='resource_description_{$count}'>
												{$resource->getDescription()|escape}
											</div>
										</td>
										<td class="border_users_l border_users_b" id="material_cost_{$count}">
											<div align='center'>
												${$resource->getMaterialCost()|escape}
											</div>
										</td>
										<td class="border_users_l border_users_b" id = "labor_cost_{$count}">
											<div align='center'>
												${$resource->getLaborCost()|escape}
											</div>
										</td>
										<td class="border_users_l border_users_b" id = "total_cost_{$count}">
											<div align='center'>
												${$resource->getTotalCost()|escape}
											</div>
										</td>
										<td class="border_users_l border_users_b border_users_r">
											<div align='center' id = '{$count}'>
												<a onclick="stepPage.stepAddEditResource.checkNewDialog({$count}, 'edit'); stepPage.stepAddEditResource.openDialog();">
													edit
												</a>
											</div>
										</td>
									</tr>
								{/if}
							{/foreach}
						{/if}
                        
					</table>
						<table class="users" align="left" cellpadding="0" cellspacing="0" style='width: 100%' id='stepResourcesDetails'>
							<tr>
								<td class="users_u_bottom" colspan="5"> </td>
								<td class="users_u_bottom_r"> </td>
							</tr>
						</table>
				</div>
			</td>
		</tr>
		<tr>
			<td>
				<div style="margin: 10px 0 0 25px; ">
					<input type='button' class='button' value='<< Back' onclick="history.back()">
					<input type='button' class='button' value='Save' onclick="stepPage.stepEdit.saveStep()">
				</div>
			</td>
		</tr>
	</table>
</div>
<div id="resourceDetailsContainer" title="Add new resource" style="display:none;">Loading ...</div>
<input type='hidden' id='currentStep' value='{$stepInstance|escape}'>
<input type='hidden' id='departmentId' value='{$departmentId|escape}'>

<div class="error_img" style="float: left; display: none;" id = 'showStepError'><span class="error_text" id = 'stepSaveErrors'></span></div>


function StepAddEditResource() {
	this.divId = 'resourceDetailsContainer';

	this.isLoaded = false;

	this.iniDialog = function(divId) {
		divId = typeof divId !== 'undefined' ? divId : this.divId;
		if(divId != this.divId) {
			this.divId = divId;
		}

		var that = this;
		$("#"+divId).dialog({
			width: 350,
			height: 300,
			autoOpen: false,
			resizable: true,
			dragable: true,
			modal: true,
			buttons: {
				'Cancel': function() {
					that.isLoaded = false;
					$(this).dialog('close');
					that.allUsers = [];
				},
				'Save': function() {
					that.save();
					$(this).dialog('close');
				}
			}
		});
	}

	this.openDialog = function() {
		$('#editResourceContainer').html('');
		$('#'+this.divId).dialog('open');
		if(!this.isLoaded) {
			this.loadContent();
		}
		return false;
	}

	this.loadContent = function() {
		if(stepSettings.action=='edit'){ 
			var resource = step.getResourceById(stepSettings.resourceId);
			var resourceUnitTypeId = resource.getResourceUnittypeId();
		}else{
			resourceUnitTypeId = 1;
		}
		
		var that = this;
		$.ajax({
			url: "?action=loadResourceDetails&category=repairOrder",
			data: {
				departmentId: stepSettings.departmentId,
				resourceUnitTypeId: resourceUnitTypeId
			},
			type: "POST",
			dataType: "html",
			success: function (response) {
				$("#"+that.divId).html(response);
				that.isLoaded = true;
				//check action
				if(stepSettings.action=='edit'){ 
					
					//insert data to form
					$('#resourceDescription').val(resource.getDescription());
					$('#resourceQty').val(resource.getQty());
					$('#resourceRate').val(resource.getRate());
					$('#selectUnitType').val(resource.getUnittypeId());
					//$('#resourceType').val(resource.getResourceUnittypeId());
					$("#resourceType [value='"+resource.getResourceUnittypeId()+"']").attr("selected", "selected");
					$("#selectUnitType [value='"+resource.getUnittypeId()+"']").attr("selected", "selected");
					//alert(resource.getUnittypeId());
					
				}
				
			}
		});
	}

	//save function
	this.save = function(){
		if(stepSettings.action == 'add'){
			this.saveAddResource();
		}else{
			this.saveEditResource();
		}
		this.isLoaded = false;
	}
	//action save for adding new Resource
	this.saveAddResource = function() {
		var errors = false;
		
		if($('#resourceDescription').val()==''){
			errors = true;
			alert('enter description');
		}else if($('#resourceQty').val() == ''){
			errors = true;
			alert('enter quantity');
		}else if($('#resourceRate').val() == ''){
			errors = true;
			alert('enter rate');
		}
		
		if(errors == false){
			//init new Resource
			var resource = new Resource();
			//set template id for new resource
			templateResourceId++;
			stepSettings.resourceId = templateResourceId;
			resource.setId(stepSettings.resourceId);
			resource.setDescription($('#resourceDescription').val());
			resource.setQty($('#resourceQty').val());
			resource.setRate($('#resourceRate').val());
			resource.setUnittypeId($('#selectUnitType').val());
			resource.setResourceUnittypeId($('#resourceType').val());
			
			//calculate resource cost
			$.ajax({
				url: "modules/ajax/getResourceCosts.php",
				data: {
					resourceQty: $('#resourceQty').val(),
					resourceRate: $('#resourceRate').val(),
					resourceUnittypeId: $('#selectUnitType').val(),
					resourceResourceUnittypeId: $('#resourceType').val()
				},
				type: "POST",
				dataType: "html",
				success: function (response) {
					var resourceCost = eval("(" + response + ')');
					
					//create new row for display 
					var html = ''
					html += '<tr class="hov_company" height="10px" id="resource_detail_'+stepSettings.resourceId+'">';
			
					html +=	'<td class="border_users_l border_users_b border_users_r">';
					html +=   '<div align="center"><input type="checkbox" value="'+stepSettings.resourceId+'"></div>';
					html +=	'</td>';
			
					html +=	'<td class="border_users_l border_users_b border_users_r">';
					html +=   '<div style="font-size: 15px" id="resource_description_'+stepSettings.resourceId+'">'
					html += $('#resourceDescription').val()+'</div>';
					html +=	'</td>';
			
					html +=	'<td class="border_users_l border_users_b border_users_r">';
					html +=   '<div align="center" id="material_cost_'+stepSettings.resourceId+'">';
					html += '$'+resourceCost.materialCost+'</div>';
					html +=	'</td>';
			
					html +=	'<td class="border_users_l border_users_b border_users_r" id = "labor_cost_'+stepSettings.resourceId+'">';
					html +=   '<div align="center">';
					html += '$'+resourceCost.laborCost+'</div>';
					html +=	'</td>';
			
					html +=	'<td class="border_users_l border_users_b border_users_r" id = "total_cost_'+stepSettings.resourceId+'">';
					html +=   '<div align="center">';
					html += '$'+resourceCost.totalCost+'</div>';
					html +=	'</td>';
			
					html +=	'<td class="border_users_b border_users_r">';
					html +=   '<div align="center" id = "'+stepSettings.resourceId+'">'
					html +=			'<a onclick="stepSettings.checkNewDialog('+stepSettings.resourceId+', \'edit\'); stepSettings.stepAddEditResource.openDialog();">'
					html +=				'edit'
					html +=			'</a>'
					html +=		'</div>';
					html +=	'</td>';
			
					html+='</tr>'
					$('#stepResourcesDetails').append(html);
					step.addResource(resource);
				}
			});
			
		}
	}
	
	this.saveEditResource = function(){
		//init new Resources
		var resource = new Resource();
		resource.setId(stepSettings.resourceId);
		resource.setDescription($('#resourceDescription').val());
		resource.setQty($('#resourceQty').val());
		resource.setRate($('#resourceRate').val());
		resource.setUnittypeId($('#selectUnitType').val());
		resource.setResourceUnittypeId($('#resourceType').val());
		$.ajax({
			url: "modules/ajax/getResourceCosts.php",
			data: {
				resourceQty: $('#resourceQty').val(),
				resourceRate: $('#resourceRate').val(),
				resourceUnittypeId: $('#selectUnitType').val(),
				resourceResourceUnittypeId: $('#resourceType').val()
			},
			type: "POST",
			dataType: "html",
			success: function (response) {
				//delete Resource 
				step.deleteResource(stepSettings.resourceId);
				var resourceCosts = eval("(" + response + ')');
				//set new information
				$('#resource_description_'+stepSettings.resourceId).html(resource.getDescription());
				$('#material_cost_'+stepSettings.resourceId).html('<div align="center">$'+resourceCosts.materialCost+'</div>');
				$('#labor_cost_'+stepSettings.resourceId).html('<div align="center">$'+resourceCosts.laborCost+'</div>');
				$('#total_cost_'+stepSettings.resourceId).html('<div align="center">$'+resourceCosts.totalCost+'</div>');
				//create new row for display 
				//add new Resource
				step.addResource(resource);
			}
		});
	}

}
function StepSettings() {
	this.stepAddEditResource = new StepAddEditResource();
	//this.stepEditResource = new StepEditResource()
	this.departmentId = departmentId;
	this.resourceId = false;
	this.stepId = stepId;
	this.action = false;
	
	this.checkNewDialog = function(id, action){
		if(this.resourceId!=id){
			this.resourceId=id;
		}
		this.action = action;
	}
}

//function for getting unitTypes
function getUnitType(){
	var departmentId = $('#departmentId').val();
	var sysType = $('#resourceType').val();
	
	if(sysType == 3){
		sysType = 'Quantity';
	}else if(sysType == 2){
		sysType = 'USA Weight';
	}else if(sysType == 1){
		sysType = 'Time';
	}
	
	$.ajax({
		url: "modules/ajax/getUnitTypes.php",
		type: "GET",
		async: false,
		data: {
			"sysType":sysType,
			"departmentId":departmentId
		},
		dataType: "html",
		success: function (response)
		{
			var html = '';
			var resp=eval("("+response+")");
							
			for (var key in resp)
			{
				html+="<option value='"+resp[key]['unittype_id']+"'>"+resp[key]['name']+"</option>";
			}
			$('#selectUnitType').html(html);
		}
	});
}

//function for delete resources
function deleteResources(){
	var rowsToDelete = new Array();
	var checkboxes = $("#stepResourcesDetails").find("input[type='checkbox']");
	 
	checkboxes.each(function(i){
		var id = this.value;
		if(this.checked) {
			rowsToDelete.push(id);
		}
	});
	var count = rowsToDelete.length;
	for(var i = 0; i<count; i++){
		step.deleteResource(rowsToDelete[i]);
		$('#resource_detail_'+rowsToDelete[i]).remove();
	}
	
}

function saveStep(){
	if($('#stepDescription').val()==''){
		alert('Enter step description please');
	}else{
		var stepAttributes = step.toJson();
		var resourcesAttributes = new Array();
		var resources = step.getResources();
		var countResources = resources.length;
		
		for(var i = 0; i<countResources; i++){
			resourcesAttributes[i] = resources[i].toJson();
		}
		resourcesAttributes = JSON.stringify(resourcesAttributes);
		$.ajax({
			url: "modules/ajax/saveNewStep.php",
			type: "POST",
			async: false,
			data: {
				'stepAttributes': stepAttributes,
				'resourcesAttributes':resourcesAttributes
			},
			dataType: "text",
			success: function (response)
			{
				window.location.href = response+'&id='+repaitOrderId+'&departmentID='+departmentId;
			}
		});
		
	}
}

//function for changing stepDescription
function changeStepDescription(){
		step.setDescription($('#stepDescription').val());
}

var stepSettings;

$(function() {
	//	ini global object
	stepSettings = new StepSettings();
	stepSettings.stepAddEditResource.iniDialog();
//stepSettings.stepEditResource.iniDialog();
});



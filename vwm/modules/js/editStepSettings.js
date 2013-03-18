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
        if(stepPage.action=='edit'){ 
            var resource = stepPage.stepEdit.step.getResourceById(stepPage.resourceId);
            var resourceUnitTypeId = resource.getResourceUnittypeId();
        }else{
            resourceUnitTypeId = 1;
        }
		
        var that = this;
        $.ajax({
            url: "?action=loadResourceDetails&category=repairOrder",
            data: {
                departmentId: stepPage.departmentId,
                resourceUnitTypeId: resourceUnitTypeId
            },
            type: "POST",
            dataType: "html",
            success: function (response) {
                $("#"+that.divId).html(response);
                that.isLoaded = true;
                //check action
                if(stepPage.action=='edit'){ 
					
                    //insert data to form
                    $('#resourceDescription').val(resource.getDescription());
                    $('#resourceQty').val(resource.getQty());
                    $('#resourceRate').val(resource.getRate());
                    $('#selectUnitType').val(resource.getUnittypeId());
                    //$('#resourceType').val(resource.getResourceUnittypeId());
                    $("#resourceType [value='"+resource.getResourceUnittypeId()+"']").attr("selected", "selected");
                    $("#selectUnitType [value='"+resource.getUnittypeId()+"']").attr("selected", "selected");
					
                }
				
            }
        });
    }

    //save function
    this.save = function(){
        if(stepPage.action == 'add'){
            this.saveAddResource();
        }else{
            this.saveEditResource();
        }
        this.isLoaded = false;
    }
    //action save for adding new Resource
    this.saveAddResource = function() {
        var errors = false;
        //init new Resource
        var resource = new Resource();
        //set template id for new resource
        templateResourceId++;
        stepPage.resourceId = templateResourceId;
        resource.setId(stepPage.resourceId);
        resource.setDescription($('#resourceDescription').val());
        resource.setQty($('#resourceQty').val());
        resource.setRate($('#resourceRate').val());
        resource.setUnittypeId($('#selectUnitType').val());
        resource.setResourceUnittypeId($('#resourceType').val());
			
        //calculate resource cost
        $.ajax({
            url: "?action=getResourceCostsInformation&category=repairOrder",
            data: {
                resourceQty: $('#resourceQty').val(),
                resourceRate: $('#resourceRate').val(),
                resourceUnittypeId: $('#selectUnitType').val(),
                resourceResourceUnittypeId: $('#resourceType').val()
            },
            type: "POST",
            dataType: "text",
            success: function (response) {
                var resourceCost = eval("(" + response + ')');
					
                //create new row for display 
                var html = ''
                html += '<tr class="hov_company" height="10px" id="resource_detail_'+stepPage.resourceId+'">';
			
                html +=	'<td class="border_users_l border_users_b">';
                html +=   '<div align="center"><input type="checkbox" value="'+stepPage.resourceId+'"></div>';
                html +=	'</td>';
			
                html +=	'<td class="border_users_l border_users_b">';
                html +=   '<div style="width: 150px" id="resource_description_'+stepPage.resourceId+'">'
                html += $('#resourceDescription').val()+'</div>';
                html +=	'</td>';
			
                html +=	'<td class="border_users_l border_users_b">';
                html +=   '<div align="center" id="material_cost_'+stepPage.resourceId+'">';
                html += '$'+resourceCost.materialCost+'</div>';
                html +=	'</td>';
			
                html +=	'<td class="border_users_l border_users_b" id = "labor_cost_'+stepPage.resourceId+'">';
                html +=   '<div align="center">';
                html += '$'+resourceCost.laborCost+'</div>';
                html +=	'</td>';
			
                html +=	'<td class="border_users_l border_users_b" id = "total_cost_'+stepPage.resourceId+'">';
                html +=   '<div align="center">';
                html += '$'+resourceCost.totalCost+'</div>';
                html +=	'</td>';
			
                html +=	'<td class="border_users_b border_users_r border_users_l">';
                html +=   '<div align="center" id = "'+stepPage.resourceId+'">'
                html +=			'<a onclick="stepPage.stepAddEditResource.checkNewDialog('+stepPage.resourceId+', \'edit\'); stepPage.stepAddEditResource.openDialog();">'
                html +=				'edit'
                html +=			'</a>'
                html +=		'</div>';
                html +=	'</td>';
			
                html+='</tr>'
                $('#stepResourcesDetails').append(html);
                stepPage.stepEdit.step.addResource(resource);
            }
        });
    }
	
    this.saveEditResource = function(){
        //init new Resources
        var resource = new Resource();
        resource.setId(stepPage.resourceId);
        resource.setDescription($('#resourceDescription').val());
        resource.setQty($('#resourceQty').val());
        resource.setRate($('#resourceRate').val());
        resource.setUnittypeId($('#selectUnitType').val());
        resource.setResourceUnittypeId($('#resourceType').val());
        $.ajax({
            url: "?action=getResourceCostsInformation&category=repairOrder",
            data: {
                resourceQty: $('#resourceQty').val(),
                resourceRate: $('#resourceRate').val(),
                resourceUnittypeId: $('#selectUnitType').val(),
                resourceResourceUnittypeId: $('#resourceType').val()
            },
            type: "POST",
            dataType: "text",
            success: function (response) {
                //delete Resource 
                stepPage.stepEdit.step.deleteResource(stepPage.resourceId);
                var resourceCosts = eval("(" + response + ')');
                //set new information
                $('#resource_description_'+stepPage.resourceId).html(resource.getDescription());
                $('#material_cost_'+stepPage.resourceId).html('<div align="center">$'+resourceCosts.materialCost+'</div>');
                $('#labor_cost_'+stepPage.resourceId).html('<div align="center">$'+resourceCosts.laborCost+'</div>');
                $('#total_cost_'+stepPage.resourceId).html('<div align="center">$'+resourceCosts.totalCost+'</div>');
                //create new row for display 
                //add new Resource
                stepPage.stepEdit.step.addResource(resource);
            }
        });
    }
	
    //function for getting unitTypes
    this.getUnitType = function (){
        var departmentId = $('#departmentId').val();
        var sysType = $('#resourceType').val();
	
        $.ajax({
            //url: "modules/ajax/getUnitTypes.php",
            url: "?action=getUnittypeListForResourceEdit&category=repairOrder",
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
	
    /**
	 * function for updating resource id in dialog window and for getting common action add or edit 
	 * (We need distinguish resources and action as we use one template for dialog window)
	 * 
	 * @param {int} id Resource id
	 * @param {String} action current action add or edit
	 * 
	 */
    this.checkNewDialog = function(id, action){
        if(stepPage.resourceId!=id){
            stepPage.resourceId=id;
        }
        stepPage.action = action;
        if(action=='add'){
            $("#resourceDetailsContainer").dialog('option', 'title', 'Add new resource'); 
        }else{
            $("#resourceDetailsContainer").dialog('option', 'title', 'Edit new resource');
        }
    }
}

function StepSettings() {
    this.stepAddEditResource = new StepAddEditResource();
    this.stepEdit = new StepEdit();
	
    this.departmentId = departmentId;
    this.resourceId = false;
    this.stepId = stepId;
    this.action = false;
}

//class for edit step
function StepEdit(){
    this.step = step;
    //function for changing stepDescription
    this.changeStepDescription = function(){
        this.step.setDescription($('#stepDescription').val());
    }
    //function for saving steps and step resources
    this.saveStep = function(){
        var stepAttributes = this.step.toJson();
        var resourcesAttributes = new Array();
        var resources = this.step.getResources();
        var countResources = resources.length;
		
        for(var i = 0; i<countResources; i++){
            resourcesAttributes[i] = resources[i].toJson();
        }
		
        resourcesAttributes = JSON.stringify(resourcesAttributes);
        $.ajax({
            url: "?action=saveStep&category=repairOrder",
            type: "POST",
            async: false,
            data: {
                'stepAttributes': stepAttributes,
                'resourcesAttributes':resourcesAttributes
            },
            dataType: "json",
            success: function (response)
            {
                if(response.errors == false){
                    window.location.href = response.link+'&id='+repaitOrderId+'&departmentID='+departmentId;
                }else{
                    $('#showStepError').show();
                    $('#stepSaveErrors').html(response.errors);
                        
                }
            }
        });
    }
    //function for delete Resources
    this.deleteResources = function(){
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
            this.step.deleteResource(rowsToDelete[i]);
            $('#resource_detail_'+rowsToDelete[i]).remove();
        }
    }
}

var stepPage;

$(function() {
    //	ini global object
    stepPage = new StepSettings();
    stepPage.stepAddEditResource.iniDialog();
	
});



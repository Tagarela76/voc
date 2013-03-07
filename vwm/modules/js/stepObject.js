/**
 * step Object
 */

function Step(){
	var self = this;
	var id;
	var resources = [];
	
	// Private members are made by the constructor	
	var constructor = function () {
		var templateid = null;
		var description = null;
        var processId = null;
        var stepNumber = null;
		var resources = [];
		
		//setters
        self.setStepNumber = function(number) {
            stepNumber = number;
        }
        
        self.setProcessId = function(id) {
            processId = id;
        }
        
		self.setDescription = function(descr) {
			description = descr;
		}
	
		self.setId = function(stepId) {
			id = stepId;
		}
		
		self.setResources = function(stepResources) {
			resources = stepResources;
		}
		
		//getters
		self.getResources = function() {
			return resources;
		}
		
		self.getDescription = function() {
			return description;
		}
		
		self.getId = function() {
			return id;
		}
        
        self.getProcessId = function(){
            return processId;
        }
        
        self.getStepNumber = function(){
            return stepNumber;
        }
		
		self.deleteResource = function(resourceId){
			var count = resources.length;
			var newResources = new Array();
			for(var i = 0; i<count; i++){
				if(resources[i].getId()!=resourceId){
					 newResources.push(resources[i]);
				}
			}
			resources = newResources;
		}
		
		self.addResource = function(resource){
			resources.push(resource);
		}
		
		self.getResourceById = function(resourceId){
			var count = resources.length;
			for(var i = 0; i<count; i++){
				if(resources[i].getId()==resourceId){
					return resources[i];
					break;
				}
			}
		}
	};
	
	constructor();
	
	//function for getting attributes
	self.getAttributes = function(){
		var stepAttributes = {
				stepId : self.getId(),
				stepDescription : self.getDescription(),
                processId : self.getProcessId(),
                number : self.getStepNumber()
			}
			return stepAttributes;
	}
	//Convert Wastes to JSON format string
	self.toJson = function() {
		var stepAttributes = self.getAttributes();
		var encoded = $.toJSON(stepAttributes);
		return encoded;
	}
}

function Resource(){
	var self = this;
	var id = null;
	var description = null;
	var qty = null;
	var rate = null;
	var unittype_id = null;
	var resource_type_id = null;
	var step_id = null;
	// Private members are made by the constructor	
	var constructor = function () {
		var id = null;
		var description = null;
		var qty = null;
		var rate = null;
		var unittype_id = null;
		var resource_type_id = null;
		var step_id = null;
		
		//setters
		self.setId = function(resourceId) {
			id = resourceId;
		}
	
		self.setDescription = function(descr) {
			description = descr;
		}
	
		self.setQty = function(quantity) {
			qty = quantity;
		}
	
		self.setRate = function(resourceRate) {
			rate = resourceRate;
		}
	
		self.setUnittypeId = function(unittypeId) {
			unittype_id = unittypeId;
		}
		
		self.setResourceUnittypeId = function(resourceTypeId) {
			resource_type_id = resourceTypeId;
		}
		
		self.setStepId = function(stepId) {
			step_id = stepId;
		}
		
		//getters
		self.getId = function() {
			return id;
		}
	
		self.getDescription = function() {
			return description;
		}
	
		self.getQty = function() {
			return qty;
		}
	
		self.getRate = function() {
			return rate;
		}
	
		self.getUnittypeId = function() {
			return unittype_id;
		}
		self.getResourceUnittypeId = function() {
			return resource_type_id
		}
	
		self.getStepId = function() {
			return step_id;
		}
		//function for getting attributes
		self.getAttributes = function(){
			var resourceAttributes = {
				id : self.getId(),
				description : self.getDescription(),
				qty: self.getQty(),
				unittypeId: self.getUnittypeId(),
				resourceTypeId: self.getResourceUnittypeId(),
				rate: self.getRate()
			}
			return resourceAttributes;
		}
		
		//Convert Wastes to JSON format string
		self.toJson = function() {
			var resourceAttributes = self.getAttributes();
			var encoded = $.toJSON(resourceAttributes);
		
			return encoded;
		}
	};
	
	constructor();
	
	
}
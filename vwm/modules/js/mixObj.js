/**
 * Mix Object
 */

function CMix() {

	this.setDescription = function(descr) {
		this.description = descr;
	}

	this.setExcemptRule = function(rule) {
		this.excemptRule = rule;
	}

	this.setNotes = function(notes) {
		this.notes = notes;
	}

	this.setMixDate = function(date) {
		this.creationTime = date;
	}
	
	this.setSpentTime = function(spentTime) {
		this.spent_time = spentTime;
	}

	this.setAPMethod = function(m) {

		if(typeof $.browser.msie != "undefined" && $.browser.msie == true) {
			//do nothing
			//IE does not correct understand undefined and this.APMethod will 
			//be exist with value 'undefined', that PHP make crazy :D
			//
			this.APMethod = m;
		} else {
			this.APMethod = m;
		}

	}

	this.setEquipment = function(e) {
		this.equipment = e;
	}

	this.setRule = function(r) {
		this.rule = r;
	}

	this.setIteration = function(iteration) {
		this.iteration = iteration;
	}

	this.setParentID = function(parentID) {
		this.parentID = parentID;
	}

	this.setRepairOrderId = function(repairOrderId) {
		this.wo_id = repairOrderId;
	}
	
	
	this.setStepId = function(StepId) {
		this.step_id = StepId;
	}
	
	this.setPfpId = function(PfpId) {
		this.pfpId = PfpId;
	}
	
	
	//Convert Wastes to JSON format sting
	this.toJson = function() {
		var encoded = $.toJSON(this);

		return encoded;
	}
}
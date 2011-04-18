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
	
	this.setMixDate = function(date) {
		this.creationTime = date;
	}
	
	this.setAPMethod = function(m) {
		this.APMethod = m;
	}
	
	this.setEquipment = function(e) {
		this.equipment = e;
	}
	
	this.setRule = function(r) {
		this.rule = r;
	}
	
	//Convert Wastes to JSON format sting
	this.toJson = function() {
		var encoded = $.toJSON(this);
		
		return encoded;
	}
}
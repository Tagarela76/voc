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

	this.setAPMethod = function(m) {

		if(typeof $.browser.msie != "undefined" && $.browser.msie == true) {
			//do nothing
			//IE does not correct understand undefined and this.APMethod will be exist with value 'undefined', that PHP make crazy :D
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

	//Convert Wastes to JSON format sting
	this.toJson = function() {
		var encoded = $.toJSON(this);

		return encoded;
	}
}
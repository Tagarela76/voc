/*Waste Streams object*/

function wasteStreamsObj() {
	
	//this.wasteStreamId = wasteStreamId;
	//this.storageId = storageId;
	//this.counter = counter;
	
	this.pollutionsDisabled = false;
	
	this.addPollution = function(pollutionObj) {
		
		if(this.pollutions == undefined) {
			this.pollutions = [];
		}
		this.pollutions.push(pollutionObj);
	}
	
	this.getPollution = function(index) {
		if(this. pollutions != undefined && index > -1 && this.pollutions.length > 0) {
			return this.pollutions[index];
		} else {
			return false;
		}
	}
	
	this.isPollutionQuantityFilled = function() {
		
		if(this.pollutions == undefined) {
			return true;
		}
		for(i=0; i<this.pollutions.length; i++) {
			
			if(this.pollutions[i].getQuantity() == undefined || this.pollutions[i].getQuantity() == "") {
				return false;
			}
		}
		return true;
	}
	
	this.removePollution = function(index) {
		
		this.pollutions.splice(index,1);
	}
	
	this.setPollutionsDisabled = function(flag) {
		this.pollutionsDisabled = flag;
	}
	
	this.setWasteStreamId = function(val) {
		this.wasteStreamId = val;
	}
	
	this.setStorageId  = function(val) {
		this.storageId = val;
	}
	
	this.setQuantity = function(val) {
		this.quantity = val;
	}
	
	this.getQuantity = function() {
		return this.quantity;
	}
	
	this.setUnittypeId = function (val) {
		this.unittypeId = val;
	}
	
	this.toString = function() {
		return "wasteId=" + this.wasteStreamId + ",storageId=" + this.storageId + ",counter=" + this.counter;
	}
	
	this.pollutions = undefined;
}
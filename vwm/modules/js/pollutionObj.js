/* Pollution Object */

function pollutionObj() {
	
	
	
	this.setPollutionId = function(val) {
		this.pollutionId = val;
	}
	
	this.setQuantity = function(val) {
		this.quantity = val;
	}
	
	this.setUnittypeId = function (val) {
		this.unittypeId = val;
	}
	
	this.toJson = function() {
		var encoded = $.toJSON(this); 
		alert(encoded);
	}
	
	this.getQuantity = function() {
		return this.quantity;
	}
	
}
/* Waste Steams Objects Collection Class */
//alert("Waste Steams Objects Collection Class");
function CWasteStreamCollection() {
	
	this.counter = 0;
	this.wastes = [];
	//alert("CWasteStreamCollection.js");
	
	//Get count of wastes
	this.Count = function() {
		return this.wastes.length;
	}
	
	//Add WasteStream to collection
	this.addWasteStream = function(wasteStreamObj) {
		
		this.wastes.push(wasteStreamObj);
	}
	
	//Add pollution to wasteStreamObj
	this.addPollutionToWaste = function(wasteIndex,pollutionObj) {
		
		this.wastes[wasteIndex].addPollution(pollutionObj);
	}
	
	//Return waste element by index. if out of index = return false.
	this.getWaste = function(index) {
		
		if(index > -1 && index < this.wastes.length) {
			
			return this.wastes[index];
			
		} else {
			return false;
		}
	}
	
	//remove wasteStreamObj by wasteStreamID
	this.removeByIndex = function(index) {
		this.wastes.splice(index,1);
	}
	
	//Set wasteStreamId to specific waste object
	this.setWasteStreamId = function(value,index) {
		
		this.wastes[index].setWasteStreamId(value);
	}
	
	this.setStorageId = function(value, index) {
		
		this.wastes[index].setStorageId(value);
	}
	
	this.setUnittypeId = function(value, index) {
		this.wastes[index].setUnittypeId(value);
	}
	
	this.setQuantity = function(value , index) {
		this.wastes[index].setQuantity(value);
	}
	
	this.isQuantityFilled = function() {
		
		for(i=0; i<this.wastes.length; i++) {
			
			if(this.wastes[i].pollutionsDisabled == true) {
				
				q = this.wastes[i].getQuantity();
				if(q == undefined || q == "") {
					return false;
				}
				
			}  else {
				
				r = this.wastes[i].isPollutionQuantityFilled();
				if(r == false) {
					return false;
				}
			}
		}
		return true;
	}
	
	//Convert Wastes to JSON format sting
	this.toJson = function() {
		var encoded = $.toJSON(this.wastes);
		
		return encoded;
	}
}
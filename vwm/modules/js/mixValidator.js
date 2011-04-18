/** Mix Validator */

function CMixValidator () {
	
	this.mixValid = true;
	this.wasteVaLid = true;
	this.productVaLid = true;
	
	this.isValid = function() {
		
		return this.mixValid && this.wasteVaLid && this.productVaLid;
	}
}
/**
* productObj Collection
*/

function CProductCollectionObj() {
	
	this.products = [];
	
	this.addProduct = function(productID, quantity, selectUnittype, unittypeCLass) {
		
		product = new CProductObj(productID, quantity, selectUnittype, unittypeCLass);
		
		this.products.push(product);
	}
	
	this.addPFPProduct = function(productID, quantity, selectUnittype, unittypeCLass, ratio, isPrimary) {
		
		
		product = new CProductObj(productID, quantity, selectUnittype, unittypeCLass);
		product.ratio = ratio;
		product.isPrimary = isPrimary;
		
		
		this.products.push(product);
	}
	
	this.Count = function() {
		return this.products.length;
	}
	
	this.removeProduct = function(productID) {
		
		for(i = 0; i<this.products.length; i++) {
			
			if(this.products[i].productID == productID) {
				this.products.splice(i,1);
				return;
			}
		}
	}
	
	this.getProduct = function(productID) {
		
		for(i = 0; i<this.products.length; i++) {
			
			if(this.products[i].productID == productID) {
				
				return this.products[i];
			}
		}
		return false;
	}
	
	//Convert Wastes to JSON format sting
	this.toJson = function() {
		var encoded = $.toJSON(this.products);
		
		return encoded;
	}
}
/**
* Product Object Class
*/

function CProductObj (_pID,_Q,_Unittype,_unittypeClass) {
	
	this.productID = _pID;
	this.quantity = _Q;
	this.selectUnittype = _Unittype;
	this.unittypeClass = _unittypeClass;
	
	this.ratio;
	this.isPrimary;
	
	this.setUnittypeClass = function(uC) {
		
		this.unittypeClass = uC;
	}
}
/**
 * inspection Type Object
 */

function LogbookInspectionType() {
    var self = this;
    var name;
    var permit;
    var id;
    var facilityId;
    var subTypes = [];

    // Private members are made by the constructor	
    var constructor = function() {
        var name;
        var permit;
        var id;
        var facilityId;
        var subTypes = [];
        var that = this;
        
        //setters
        self.setId = function(typeId) {
            id = typeId;
        }
        self.setName = function(typeName) {
            name = typeName;
        }
        self.setPermit = function(typePermit) {
            permit = typePermit;
        }
        self.setFacilityId = function(typeFacilityId) {
            facilityId = typeFacilityId;
        }
        
        self.setSubTypes = function(typeSubTypes) {
            subTypes = typeSubTypes;
        }
        
        //getters
        self.getId = function() {
            return id
        }
        self.getName = function() {
            return name
        }
        self.getPermit = function() {
            return permit
        }
        self.getFacilityId = function() {
            return facilityId
        }
        self.getSubTypes = function() {
            return subTypes
        }
        
        self.addSubType = function(subType){
			subTypes.push(subType);
		}
        
    }
    
    constructor();
	
	//function for getting attributes
	self.getAttributes = function(){
        var typeSubTypes = [];
        var subTypes = self.getSubTypes();
        for (var i=0; i<subTypes.length; i++){
            typeSubTypes.push(subTypes[i].getAttributes());
        }
		var stepAttributes = {
				typeId : self.getId(),
				typeName : self.getName(),
                typePermit : self.getPermit(),
                typeFacilityId: self.getFacilityId(),
                typeSubTypes: typeSubTypes,
			}
			return stepAttributes;
	}
	//Convert Wastes to JSON format string
	self.toJson = function() {
		var stepAttributes = self.getAttributes();
		var encoded = $.toJSON(stepAttributes);
		return encoded;
	}
    
    self.deleteSubType = function(id) {
        subtypes = self.getSubTypes();
        var count = subtypes.length;
			var newSubTypes = new Array();
			for(var i = 0; i<count; i++){
				if(subtypes[i].getId()!=id){
					 newSubTypes.push(subtypes[i]);
				}
			}
			self.setSubTypes(newSubTypes);
    }
    /*
     * 
     * save logbookInspection Type
     * 
     * @returns {null}
     */
    self.save = function() {
        var inspectionTypeToJson = self.toJson();
        
        $.ajax({
            url: '?action=SaveInspectionType&category=logbook',
            type: 'post',
            data: {
                inspectionTypeToJson: inspectionTypeToJson,
            },
            dataType: 'html',
            success: function(response) {
                $('#subTypeList').html(response);
            }
        });
    }
}

/**
 * 
 * inspection sub type class
 * 
 * @returns {undefined}
 */
function LogbookInspectionSubType() {
    var self = this;
    var id = null;
    var name = null;
    var hasNotes = null;
    var hasQty = null;
    var hasGauge = null;

    // Private members are made by the constructor	
    var constructor = function() {

        var id = null;
        var name = null;
        var hasNotes = null;
        var hasQty = null;
        var hasGauge = null;
        var that = this;
        //setters
        self.setId = function(subTypeId) {
            id = subTypeId;
        }

        self.setName = function(subTypeName) {
            name = subTypeName;
        }

        self.setHasNotes = function(notes) {
            hasNotes = notes;
        }

        self.setHasQty = function(qty) {
            hasQty = qty;
        }

        self.setHasGauge = function(gauge) {
            hasGauge = gauge;
        }

        //getters
        self.getId = function() {
            return id
        }

        self.getName = function() {
            return name
        }

        self.getHasNotes = function() {
            return hasNotes
        }

        self.getHasQty = function() {
            return hasQty
        }

        self.getHasGauge = function() {
            return hasGauge
        }

        //function for getting attributes
        self.getAttributes = function() {
            var resourceAttributes = {
                id: self.getId(),
                name: self.getName(),
                notes: self.getHasNotes(),
                qty: self.getHasQty(),
                valueGauge: self.getHasGauge(),
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


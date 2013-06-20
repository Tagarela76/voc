/**
 * inspection Type Object
 */

function LogbookInspectionType() {
    var self = this;
    var name;
    var permit;
    var id;
    var subTypes = [];
    var gaugeTypes = [];
    var logbookTemplateIds;

    // Private members are made by the constructor	
    var constructor = function() {
        var name;
        var permit;
        var id;
        var subTypes = [];
        var gaugeTypes = [];
        var logbookTemplateIds;
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
        
        self.setlogbookTemplate = function(typeLogbookTemplate) {
            logbookTemplateIds = typeLogbookTemplate;
        }

        self.setSubTypes = function(typeSubTypes) {
            subTypes = typeSubTypes;
        }
        
        self.setGaugeTypes = function(typeGaugeTypes) {
            gaugeTypes = typeGaugeTypes;
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
        
        self.getLogbookTemplate = function() {
            return logbookTemplateIds
        }
        self.getSubTypes = function() {
            return subTypes
        }
        self.getGaugeTypes = function() {
            return gaugeTypes
        }

        self.addSubType = function(subType) {
            subTypes.push(subType);
        }
        
        self.addGaugeType = function(gaugeType){
            gaugeTypes.push(gaugeType);
        }
        
        self.load = function(json){
            var settings = eval('(' + json + ')');
            
            //intialize InspectionType
            self.setName(settings.typeName);
            self.setPermit(settings.permit);
            
            //initializa subtypes
            if (settings.subtypes != undefined) {
                var subtypesSettings = settings.subtypes;
                var subtypes = [];
                for (var i = 0; i < subtypesSettings.length; i++) {
                    var subtype = new LogbookInspectionSubType()
                    subtype.setId(i);
                    subtype.setName(subtypesSettings[i].name);
                    subtype.setHasNotes(subtypesSettings[i].notes);
                    subtype.setHasQty(subtypesSettings[i].qty);
                    subtype.setHasGauge(subtypesSettings[i].valueGauge);
                    subtype.setGaugeType(subtypesSettings[i].gaugeType);
                    subtypes.push(subtype);
                }
                self.setSubTypes(subtypes);
            }
            
            //initializa gauge typs if exist
            if (settings.additionFieldList != undefined) {
                var gaugetypesSettings = settings.additionFieldList;
                var gaugetypes = [];
                for (var i = 0; i < gaugetypesSettings.length; i++) {
                    var gaugetype = new LogbookInspectionGaugeType()
                    gaugetype.setId(i);
                    gaugetype.setName(gaugetypesSettings[i].name);
                    gaugetype.setGaugeType(gaugetypesSettings[i].gaugeType);

                    gaugetypes.push(gaugetype);
                    temporaryGaugeTypeId = i;
                    self.setGaugeTypes(gaugetypes);
                }
            }
        }
    }

    constructor();

    //function for getting attributes
    self.getAttributes = function() {
        var typeSubTypes = [];
        var typeGaugeTypes = [];
        var subTypes = self.getSubTypes();
        var gaugeTypes = self.getGaugeTypes();
        
        for (var i = 0; i < subTypes.length; i++) {
            typeSubTypes.push(subTypes[i].getAttributes());
        }
        
        for (var i = 0; i < gaugeTypes.length; i++) {
            typeGaugeTypes.push(gaugeTypes[i].getAttributes());
        }
        
        var typeAttributes = {
            typeId: self.getId(),
            typeName: self.getName(),
            permit: self.getPermit(),
            subtypes: typeSubTypes,
            additionFieldList: typeGaugeTypes,
            logbookTemplateIds: self.getLogbookTemplate(),
        }
        return typeAttributes;
    }
    //Convert Wastes to JSON format string
    self.toJson = function() {
        var typeAttributes = self.getAttributes();
        var encoded = $.toJSON(typeAttributes);
        return encoded;
    }

    self.deleteSubType = function(id) {
        var subtypes = self.getSubTypes();
        var count = subtypes.length;
        var newSubTypes = new Array();
        for (var i = 0; i < count; i++) {
            if (subtypes[i].getId() != id) {
                newSubTypes.push(subtypes[i]);
            }
        }
        self.setSubTypes(newSubTypes);
    }
    
    self.deleteGaugeType = function(id) {
        var gaugeTypes = self.getGaugeTypes();
        var count = gaugeTypes.length;
        var newGaugeTypes = new Array();
        for (var i = 0; i < count; i++) {
            if (gaugeTypes[i].getId() != id) {
                newGaugeTypes.push(gaugeTypes[i]);
            }
        }
        self.setGaugeTypes(newGaugeTypes);
    }
    /*
     * 
     * save logbookInspection Type
     * 
     * @returns {null}
     */
    self.save = function() {
        var inspectionTypeToJson = self.toJson();
        var id = $('#logbookInspectionTypeId').val();
        var companyId = $('#companyId').val();
        
        $.ajax({
            url: '?action=SaveInspectionType&category=logbook',
            type: 'post',
            data: {
                id: id,
                companyId:companyId,
                inspectionTypeToJson: inspectionTypeToJson
            },
            dataType: 'json',
            success: function(response) {
                if (response.errors == false) {
                    window.location.href = response.link;
                } else {
                    $('#showTypeError').show();
                    $('#typeSaveErrors').html(response.errors);
                }
            }
        });
    }
    
    /**
     * 
     * @param {int} id
     * 
     * @returns {Boolean|LogbookInspectionSubType}
     */
    self.getSubTypeById = function(id){
        var subtypes = self.getSubTypes();
        var count = subtypes.length;
        for(var i =0; i<count; i++){
            if(subtypes[i].getId() == id){
                return subtypes[i];
            }
        }
        return false;
    }
    
    self.getGaugeTypeById = function(id) {
        var gaugetypes = self.getGaugeTypes();
        var count = gaugetypes.length;
        for (var i = 0; i < count; i++) {
            if (gaugetypes[i].getId() == id) {
                return gaugetypes[i];
            }
        }
        return false;
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
    var gaugeType;

    // Private members are made by the constructor	
    var constructor = function() {

        var id = null;
        var name = null;
        var hasNotes = null;
        var hasQty = null;
        var hasGauge = null;
        var that = this;
        var gaugeType;
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

        self.setGaugeType = function(type) {
            gaugeType = type
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

        self.getGaugeType = function() {
            return gaugeType
        }

        //function for getting attributes
        self.getAttributes = function() {
            var subTypeAttributes = {
                id: self.getId(),
                name: self.getName(),
                notes: self.getHasNotes(),
                qty: self.getHasQty(),
                valueGauge: self.getHasGauge(),
                gaugeType: self.getGaugeType(),
            }
            return subTypeAttributes;
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

function LogbookInspectionGaugeType() {
    var self = this;
    var id = null;
    var name = null;
    var gaugeTypeId = null;

    // Private members are made by the constructor
    var constructor = function() {
        var id = null;
        var name = null;
        var gaugeType = null;
        //setters
        self.setId = function(gaugeId) {
            id = gaugeId;
        }
        self.setName = function(gaugeTypeName) {
            name = gaugeTypeName;
        }
        self.setGaugeType = function(gaugeTypeId) {
            gaugeType = gaugeTypeId;
        }
        //getters
        self.getId = function() {
            return id
        }
        //getters
        self.getName = function() {
            return name
        }
        //getters
        self.getGaugeType = function() {
            return gaugeType
        }
        
        //function for getting attributes
        self.getAttributes = function() {
            var gaugeAttributes = {
                id: self.getId(),
                name: self.getName(),
                gaugeType: self.getGaugeType()
            }
            return gaugeAttributes;
        }
        
    }
    constructor();
}


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
    var logbookDescriptions = [];
    var logbookTemplateIds;

    // Private members are made by the constructor	
    var constructor = function() {
        var name;
        var permit;
        var id;
        var subTypes = [];
        var gaugeTypes = [];
        var logbookDescriptions = [];
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
        
        self.setLogbookDescriptions = function(descriptions){
            logbookDescriptions = descriptions;
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
        self.getLogbookDescriptions = function(){
            return logbookDescriptions;
        }
        
        self.addSubType = function(subType) {
            subTypes.push(subType);
        }
        self.addLogbookDescription = function(description){
            logbookDescriptions.push(description);
        }
        
        self.addGaugeType = function(gaugeType){
            gaugeTypes.push(gaugeType);
        }
        
        self.load = function(json, descriptionList){
            var settings = eval('(' + json + ')');
            var descriptionList = eval('(' + descriptionList + ')');
            
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
            
            //check if we have description list
            if(typeof descriptionList != 'undefined' && descriptionList.length>0){
                var descriptions = [];
                for (var i = 0; i < descriptionList.length; i++) {
                    var description = new LogbookDescription();
                    description.setId(i);
                    description.setDescription(descriptionList[i].description);
                    description.setNotes(descriptionList[i].notes);
                    //set origin id
                    description.setOriginId(descriptionList[i].id);
                    
                    descriptions.push(description);
                }
                temporaryLogbookDescriptionId = i;
                self.setLogbookDescriptions(descriptions);
            }
            
        }
    }

    constructor();

    //function for getting attributes
    self.getAttributes = function() {
        var typeSubTypes = [];
        var typeGaugeTypes = [];
        var logbookDescriptions = [];
        
        var subTypes = self.getSubTypes();
        var gaugeTypes = self.getGaugeTypes();
        var descriptions = self.getLogbookDescriptions();
        
        for (var i = 0; i < subTypes.length; i++) {
            typeSubTypes.push(subTypes[i].getAttributes());
        }
        
        for (var i = 0; i < gaugeTypes.length; i++) {
            typeGaugeTypes.push(gaugeTypes[i].getAttributes());
        }
        
        for (var i = 0; i < descriptions.length; i++) {
            logbookDescriptions.push(descriptions[i].getAttributes());
        }
        
        var typeAttributes = {
            typeId: self.getId(),
            typeName: self.getName(),
            permit: self.getPermit(),
            subtypes: typeSubTypes,
            additionFieldList: typeGaugeTypes,
            logbookTemplateIds: self.getLogbookTemplate(),
            logbookDescriptions: logbookDescriptions
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
    
    self.deleteLogbookDescription = function(id){
        var logbookDescriptions = self.getLogbookDescriptions();
        var count = logbookDescriptions.length;
        var newLogbookDescriptions = new Array();
         for (var i = 0; i < count; i++) {
            if (logbookDescriptions[i].getId() != id) {
                newLogbookDescriptions.push(logbookDescriptions[i]);
            }
        }
        self.setLogbookDescriptions(newLogbookDescriptions);
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
               //$('#typeSaveErrors').html(response);
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
    
    self.getLogbookDescriptionById = function(id) {
        var logbookDescriptions = self.getLogbookDescriptions();
        var count = logbookDescriptions.length;
        for (var i = 0; i < count; i++) {
            if (logbookDescriptions[i].getId() == id) {
                return logbookDescriptions[i];
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
                gaugeType: self.getGaugeType()
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

/**
 * 
 * logbook description class
 * 
 * @returns {undefined}
 */
function LogbookDescription() {
    var self = this;
    var id = null;
    var description = null;
    var notes = null;
    var origin_id = null;
    // Private members are made by the constructor
    var constructor = function()
    {
        /**
         * 
         * inspection type description id
         * 
         * @int id
         */
        var id = null;
        /**
         * 
         * logbook description
         * 
         * @string description
         */
        var description = null;
        /**
         * 
         * logbook description notes
         * 
         * @bool notes
         */
        var notes = null;
        /**
         * 
         * origin logbook Description type Id
         * 
         * @int originId
         */
        var origin_id = null;
        
        //setters
        self.setId = function(gaugeId) {
            id = gaugeId;
        }
        self.setDescription = function(logbookDescription) {
            description = logbookDescription;
        }
        self.setNotes = function(logbookNotes) {
            notes = logbookNotes;
        }
        self.setOriginId = function(originId) {
            origin_id = originId;
        }
        //getters
        self.getId = function() {
            return id
        }
        self.getDescription = function() {
            return description
        }
        self.getNotes = function() {
            return notes
        }
        self.getOriginId = function() {
            return origin_id
        }
        
        //function for getting attributes
        self.getAttributes = function() {
            var gaugeAttributes = {
                id: self.getId(),
                description: self.getDescription(),
                notes: self.getNotes(),
                originId: self.getOriginId()
            }
            return gaugeAttributes;
        }
    }
    constructor();
}


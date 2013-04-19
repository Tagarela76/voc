/**
 * 
 * class for manage Logbook types and subtypes
 * 
 * @returns {InspectionTypeList}
 */
function InspectionTypeList() {
    var self = this;

    // Private members are made by the constructor	
    var constructor = function() {
        var inspectionTypes = null;

        //setters
        self.setInspectionTypes = function(types) {
            inspectionTypes = types;
        }

        self.getInspectionTypes = function() {
            return inspectionTypes
        }
    };
    constructor();

    /**
     * 
     * @param {string} inspectionTypeName
     * 
     * @returns {std[]|Boolean}
     */
    this.getInspectionTypeByTypeName = function(inspectionTypeName) {
        var inspectionTypes = this.getInspectionTypes();
        for(var i=0; i<inspectionTypes.length; i++){
            if(inspectionTypes[i].typeName == inspectionTypeName){
                return inspectionTypes[i];
            }
        }
        return false;
    }

    /**
     * update unit sub types list
     * 
     * @returns null
     */
    this.changeSubTypeList = function() {
        var inspectionTypeName = $('#inspectionType').val();
        //clear addition field
        
        $('#qty').val('');
        $('#subTypeNotes').val('');
        $("#gaugeValue").val('')
        $('#permit').removeAttr('checked');
        
        //get subTypeList
        var inspectionType = this.getInspectionTypeByTypeName(inspectionTypeName);
        var html = '';
        for (var i = 0; i < inspectionType.subtypes.length; i++) {
            html += "<option value='" + inspectionType.subtypes[i].name + "'>";
            html += inspectionType.subtypes[i].name;
            html += "</option>";
        }
        //change subType list
        $('#inspectionSubType').html(html);
        this.getSubTypesAdditionFields();
    }
    
    /**
     * get sub type
     * 
     * @param {string} inspectionTypeName
     * @param {string} inspectionSubTypeName
     * 
     * @returns {std|Boolean}
     */
    this.getInspectionSubType = function(inspectionTypeName, inspectionSubTypeName){
        var inspectionTypes = this.getInspectionTypes();
        var inspectionSubTypes;
        
        //get subTypes
        for (var i = 0; i < inspectionTypes.length; i++) {
            if (inspectionTypes[i].typeName == inspectionTypeName) {
                inspectionSubTypes = inspectionTypes[i].subtypes;
                break;
            }
        }
        
        //get subtype
        for(var i = 0; i < inspectionSubTypes.length; i++){
            if(inspectionSubTypes[i].name == inspectionSubTypeName){
                return inspectionSubTypes[i];
            }
        }
        return false;
    }
    
    /**
     * display sub type addition fields
     * 
     * @returns {null}
     */
    this.getSubTypesAdditionFields = function(){
        var inspectionTypeName = $('#inspectionType').val();
        var inspectionSubTypeName = $('#inspectionSubType').val();
        var inspectionType = this.getInspectionTypeByTypeName(inspectionTypeName)
        var inspectionSubType = this.getInspectionSubType(inspectionTypeName,inspectionSubTypeName);
        
        if(inspectionSubType.qty == 0){
            $('#subTypeQty').hide();
        }else{
            $('#subTypeQty').show();
        }
        
        if (inspectionSubType.notes == 0) {
            $('#logBookSubTypeNotes').hide();
        }else{
            $('#logBookSubTypeNotes').show();
        }
        
        if(inspectionSubType.valueGauge == 0){
            $('#logbookValueGauge').hide();
        }else{
            $('#logbookValueGauge').show();
        }
        
        if(inspectionType.permit == 0){
            $('#logBookPermit').hide();
        }else{
            $('#logBookPermit').show();
        }
    }

}

/**
 * logbook description class
 * 
 */
function Description() {
    var self = this;
    
    var constructor = function() {
        var descriptionList = null;
        //setters
        self.setDescriptions = function(descriptions) {
            //alert(types);
            descriptionList = descriptions;
        }

        self.getDescriptions = function() {
            return descriptionList
        }
    };
    constructor();
    
    /**
     * 
     * get description by description name
     * 
     * @param {string} descriptionName
     * 
     * @returns {std|Boolean}
     */
    this.getDescriptionByName = function(descriptionName){
        var descriptionList = this.getDescriptions();
        for(var i=0; i<descriptionList.length; i++){
            if(descriptionList[i].name == descriptionName){
                return descriptionList[i];
            }
        }
        return false;
    }
    
    /**
     * 
     * change description
     * 
     * @returns null
     */
    this.changeDescription = function(){
        $('#logBookDescriptionNotes').val('');
        this.showNotes();
    }
    /**
     * 
     * show description notes
     * 
     * @returns {null}
     */
    this.showNotes = function(){
        var descriptionName = $('#logBookDescription').val();
        var description = this.getDescriptionByName(descriptionName);
        if(description.notes == 0){
            $('#logBookDescriptionNotes').hide();
        }else{
            $('#logBookDescriptionNotes').show();
        }
    };
}


function ManageLogbookRecord() {
    var self = this;

    this.inspectionTypeList = new InspectionTypeList();
    this.description = new Description();

    this.setjSon = function(json) {
        self.inspectionTypeList.setInspectionTypes(json.inspectionTypes);
        self.description.setDescriptions(json.description);
    }
}


/**
 * class for add new inspection Person 
 */
/**
 * Dialog Window for add inspection Person
 * 
 * @returns null
 */
function AddInspectionPerson(){
    this.divId = 'addInspectionPersonContainer';
    this.isLoaded = false;
    
    this.iniDialog = function(divId) {
        divId = typeof divId !== 'undefined' ? divId : this.divId;
        if(divId != this.divId) {
            this.divId = divId;
        }

        var that = this;
        $("#"+divId).dialog({
            width: 350,
            height: 300,
            autoOpen: false,
            resizable: true,
            dragable: true,
            modal: true,
            buttons: {
                'Cancel': function() {
                    that.isLoaded = false;
                    $(this).dialog('close');
                    that.allUsers = [];
                },
                'Save': function() {
                    that.save();
                    $(this).dialog('close');
                }
            }
        });
    }
    
    this.openDialog = function() {
        $('#addInspectionPersonContainer').html('');
        $('#'+this.divId).dialog('open');
        if(!this.isLoaded) {
            this.loadContent();
        }
        return false;
    }
    
    this.loadContent = function() {
        var that = this;
        $.ajax({
            url: "?action=loadAddInspectionPersonDetails&category=logbook",
            data: {
                facilityId: inspectionPerson.facilityId,
            },
            type: "POST",
            dataType: "text",
            success: function (response) {
                $("#"+that.divId).html(response);
                that.isLoaded = true;
                //check action
            }
        });
    }
    
    this.save = function(){
        var that = this;
        var InspectionPersonName = $('#InspectionPersonName').val();
        var facilityId = $('#facilityId').val();
        $.ajax({
            url: "?action=saveInspectionPerson&category=logbook",
            data: {
                personName: InspectionPersonName,
                facilityId: facilityId
            },
            type: "POST",
            dataType: "html",
            success: function (response) {
                var id = response;
                that.isLoaded = false;
                var html = $('#InspectionPersons').html();
                html += "<option value = '"+response+"'>";
                html += InspectionPersonName;
                html += "</option>";
                
                $('#InspectionPersons').html(html);
            }
        });
    }
}

function InspectionPersonSettings() {
   this.addInspectionPerson = new AddInspectionPerson();
   this.facilityId = facilityId
}
var inspectionPerson;

$(function() {
    //	ini global object
    inspectionPerson = new InspectionPersonSettings();
    inspectionPerson.addInspectionPerson.iniDialog();
	
});
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
     * @param {int} number
     * 
     * @returns {std[]}
     */
    this.getInspectionTypeByTypeId = function(inspectionTypeId) {
        var inspectionTypes = this.getInspectionTypes();
        return inspectionTypes[inspectionTypeId];
    }

    /**
     * update unit sub types list
     * 
     * @returns null
     */
    this.changeSubTypeList = function() {
        var inspectionTypeId = $('#inspectionType').val();
        //get subTypeList
        var inspectionType = this.getInspectionTypeByTypeId(inspectionTypeId)
        var html = '';
        for (var i = 0; i < inspectionType.subtypes.length; i++) {
            html += "<option value='" + i + "'>";
            html += inspectionType.subtypes[i].name;
            html += "</option>";
        }
        //change subType list
        $('#inspectionSubType').html(html);
        if(inspectionType.permit == 0){
            $('#logBookPermit').hide();
        }else{
            $('#logBookPermit').show();
        }
        this.getSubTypesAdditionFields();
    }
    
    /**
     * get sub type
     * 
     * @param {int} inspectionTypeId
     * @param {int} inspectionSubTypeId
     * 
     * @returns {std}
     */
    this.getInspectionSubType = function(inspectionTypeId, inspectionSubTypeId){
        var inspectionTypes = this.getInspectionTypes();
        
        return inspectionTypes[inspectionTypeId].subtypes[inspectionSubTypeId];
    }
    
    /**
     * display sub type addition fields
     * 
     * @returns {null}
     */
    this.getSubTypesAdditionFields = function(){
        var inspectionTypeId = $('#inspectionType').val();
        var inspectionSubTypeId = $('#inspectionSubType').val();
        var inspectionSubType = this.getInspectionSubType(inspectionTypeId,inspectionSubTypeId);
        
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
    
    this.getDescriptionById = function(number){
        var descriptionList = this.getDescriptions();
        return descriptionList[number];
    }
    
    this.showNotes = function(){
        var descriptionId = $('#logBookDescription').val();
        var description = this.getDescriptionById(descriptionId);
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
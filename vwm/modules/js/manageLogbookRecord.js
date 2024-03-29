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
        for (var i = 0; i < inspectionTypes.length; i++) {
            if (inspectionTypes[i].typeName == inspectionTypeName) {
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
        //var inspectionTypeName = $('#inspectionType').val();
        var inspectionTypeName = $('#inspectionType option:selected').text();
        
        //get subTypeList
        var inspectionType = this.getInspectionTypeByTypeName($.trim(inspectionTypeName));
        
        //check for addition fields
        var html = '';
        if (inspectionType.subtypes != undefined) {
            for (var i = 0; i < inspectionType.subtypes.length; i++) {
                html += "<option value='" + inspectionType.subtypes[i].name + "'>";
                html += inspectionType.subtypes[i].name;
                html += "</option>";
            }
            //change subType list
            $('#inspectionSubType').show();
        }else{
            $('#inspectionSubType').hide();
        }
        $('#inspectionSubType').html(html);
        this.changeSubType();
        this.getAdditionFieldTypesList();
    }
    
    this.changeLogbookDescriptionList = function(){
        var inspectionTypeId = $('#inspectionType').val();
        $.ajax({
            url: "?action=getLogbookDescriptionList&category=logbook",
            data: {
                inspectionTypeId: inspectionTypeId
            },
            type: "POST",
            dataType: 'json',
            success: function(response) {
                var html = '<option value="None">None</option>';
                for(var i = 0;i<response.length; i++ ){
                    html += '<option value="'+response[i].id+'">'+response[i].description+'</option>';
                }
                $('#logBookDescription').html(html);
                itlManager.setjSonDescriptionType(response);
            }
        });
        
    }

    this.getAdditionFieldTypesList = function() {
        //var inspectionTypeName = $('#inspectionType').val();
        var inspectionTypeName = $('#inspectionType option:selected').text();
        
        var inspectionType = this.getInspectionTypeByTypeName($.trim(inspectionTypeName));
        if (inspectionType.additionFieldList != undefined) {
            $('#inspectionAdditionListTypeContainer').show();
            var html = '';
            for (var i = 0; i < inspectionType.additionFieldList.length; i++) {
                html += "<option value='" + inspectionType.additionFieldList[i].name + "'>";
                html += inspectionType.additionFieldList[i].name;
                html += "</option>";
            }
            $('#inspectionAdditionListType').html(html);
        } else {
            $('#inspectionAdditionListTypeContainer').hide();
        }
    }

    /**
     * get sub type
     * 
     * @param {string} inspectionTypeName
     * @param {string} inspectionSubTypeName
     * 
     * @returns {std|Boolean}
     */
    this.getInspectionSubType = function(inspectionTypeName, inspectionSubTypeName) {
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
        if (inspectionSubTypes != undefined) {
            for (var i = 0; i < inspectionSubTypes.length; i++) {
                if (inspectionSubTypes[i].name == inspectionSubTypeName) {
                    return inspectionSubTypes[i];
                    break;
                }
            }
        }
        return false;
    }

    /**
     * 
     * get addition type if exist
     * 
     * @param {string} inspectionTypeName
     * @param {string} inspectionAdditionType
     * @returns {std|Boolean}
     */
    this.getInspectionAdditionType = function(inspectionTypeName, inspectionAdditionTypeName) {
        var inspectionTypes = this.getInspectionTypes();
        var inspectionAdditionTypes;
        //get addition Types
        for (var i = 0; i < inspectionTypes.length; i++) {
            if (inspectionTypes[i].typeName == inspectionTypeName) {
                inspectionAdditionTypes = inspectionTypes[i].additionFieldList;
                break;
            }
        }
        if (inspectionAdditionTypes == undefined) {
            return false;
        } else {
            for (var i = 0; i < inspectionAdditionTypes.length; i++) {
                if (inspectionAdditionTypes[i].name == inspectionAdditionTypeName) {
                    return inspectionAdditionTypes[i];
                }
            }
        }
    }
    /**
     * display sub type addition fields
     * 
     * @returns {null}
     */
    this.getSubTypesAdditionFields = function(gaugeType) {
        //var inspectionTypeName = $('#inspectionType').val();
        var inspectionTypeName = $('#inspectionType option:selected').text();
        inspectionTypeName = $.trim(inspectionTypeName);

        var inspectionSubTypeName = $('#inspectionSubType').val();
        var inspectionAdditionTypeName = $('#inspectionAdditionListType').val();
        var inspectionType = this.getInspectionTypeByTypeName(inspectionTypeName)
        var inspectionSubType = this.getInspectionSubType(inspectionTypeName, inspectionSubTypeName);
        var inspectionAdditionListType = this.getInspectionAdditionType(inspectionTypeName, inspectionAdditionTypeName);
        
        if (inspectionSubType.qty == 0 || inspectionSubType == false) {
            $('#subTypeQty').hide();
        } else {
            $('#subTypeQty').show();
        }

        if (inspectionSubType.notes == 0 || inspectionSubType == false) {
            $('#logBookSubTypeNotes').hide();
        } else {
            $('#logBookSubTypeNotes').show();
        }
        
        
        if (inspectionSubType.valueGauge == 0 || inspectionSubType == false) {
            $('#logbookValueGauge').hide();
            $('#logbookReplacedBulbs').hide();
        } else {
            if (gaugeType == undefined) {
                var gaugeType = 'null';
                //get gauge type by sub type
                if (inspectionAdditionListType != undefined) {
                    gaugeType = inspectionAdditionListType.gaugeType
                }
                if (inspectionSubType.gaugeType != undefined) {
                    gaugeType = inspectionSubType.gaugeType;
                }
            }
            //set gauge type
            $('#logbookValueGauge').show();
            $('#logbookReplacedBulbs').show();
        }
        $('#gaugeType').val(gaugeType);
        /*if (inspectionType.permit == 0) {
            $('#logBookPermit').hide();
        } else {
            $('#logBookPermit').show();
        }*/

        if (inspectionType.additionFieldList != undefined) {
            $('#inspectionAdditionListTypeContainer').show();
        } else {
            $('#inspectionAdditionListTypeContainer').hide();
        }

    }

    this.changeSubType = function() {
        //clear addition field
        $('#qty').val('');
        $('#subTypeNotes').val('');

        this.getSubTypesAdditionFields();
        itlManager.gauges.changeGauge();
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
    this.getDescriptionById = function(descriptionId) {
        var descriptionList = this.getDescriptions();
        for (var i = 0; i < descriptionList.length; i++) {
            if (descriptionList[i].id == descriptionId) {
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
    this.changeDescription = function() {
        $('#logBookDescriptionNotes').val('');
        this.showNotes();
    }
    /**
     * 
     * show description notes
     * 
     * @returns {null}
     */
    this.showNotes = function() {
        var descriptionId = $('#logBookDescription').val();
        
        var description = this.getDescriptionById(descriptionId);
        if (description.notes != 1) {
            $('#logBookDescriptionNotes').hide();
        } else {
            $('#logBookDescriptionNotes').show();
        }
    };
}

function Equipmant() {

    this.getEquipmantList = function() {
        var departmentId = $('#equipmantdepartmentIdList').val();
        if (departmentId == 'null') {
            $('#equipmantListContainer').hide();
        } else {
            $('#equipmantListContainer').show();
        }
        $.ajax({
            url: "?action=getEquipmantList&category=logbook",
            data: {
                departmentId: departmentId
            },
            type: "POST",
            dataType: 'json',
            success: function(response) {
                var html = ''
                for (var i = 0; i < response.length; i++) {
                    html += "<option value=" + response[i].equipment_id + ">";
                    html += response[i].equip_desc;
                    html += "</option>";
                }
                $('#equipmantList').html(html);
            }
        });

    }
    this.showEquipmentList = function() {
        var isShowEquipment = $('#isEquipment').val();
        //we can't create recurring in equipment and if is pending
        if(isShowEquipment == 'equipment'){
            $('#showEquipmentList').show();
            $('#isRecurring').attr('checked', false);
        }else{
            $('#showEquipmentList').hide();
        }
        
        if(itlManager.isPending){
          $('#isRecurring').attr("disabled", "disabled");  
        }
        itlManager.showLogbookPeriodicity();
        
    }
}

function ManageLogbookRecord() {
    var self = this;
    this.isPending = 0;

    this.inspectionTypeList = new InspectionTypeList();
    this.description = new Description();
    this.equipmant = new Equipmant();
    this.gauges = new Gauges();

    this.setjSonInspectionType = function(json) {
        self.inspectionTypeList.setInspectionTypes(json);
    }
    this.setjSonDescriptionType = function(json) {
        self.description.setDescriptions(json);
    }
    
    this.showLogbookPeriodicity = function(){
        var showPeriodicity = $('#isRecurring').is(':checked');
        if(showPeriodicity){
            $('#periodicityContainer').show();
        }else{
            $('#periodicityContainer').hide();
        }
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
function AddInspectionPerson() {
    this.divId = 'addInspectionPersonContainer';
    this.isLoaded = false;

    this.iniDialog = function(divId) {
        divId = typeof divId !== 'undefined' ? divId : this.divId;
        if (divId != this.divId) {
            this.divId = divId;
        }

        var that = this;
        $("#" + divId).dialog({
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
        $('#' + this.divId).dialog('open');
        if (!this.isLoaded) {
            this.loadContent();
        }
        return false;
    }

    this.loadContent = function() {
        var that = this;
        $.ajax({
            url: "?action=loadAddInspectionPersonDetails&category=logbook",
            data: {
                facilityId: inspectionPerson.facilityId
            },
            type: "POST",
            dataType: "text",
            success: function(response) {
                $("#" + that.divId).html(response);
                that.isLoaded = true;
                //check action
            }
        });
    }

    this.save = function() {
        var that = this;
        var InspectionPersonName = $('#InspectionPersonName').val();
        var facilityId = $('#facilityId').val();
        $.ajax({
            url: "?action=saveDialogInspectionPerson&category=logbook",
            data: {
                personName: InspectionPersonName,
                facilityId: facilityId
            },
            type: "POST",
            dataType: "html",
            success: function(response) {
                var id = response;
                that.isLoaded = false;
                var html = $('#InspectionPersons').html();
                html += "<option value = '" + response + "'>";
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

function ManageLogbookList(){
    
    this.filterlogbookList = function() {
        var facilityId = $('#facilityId').val();
        var logbookFilter = $('#logbookFilter').val();
        location.href = '?action=browseCategory&category=facility&id='+facilityId+'&bookmark=logbook&tab=logbook&filter='+logbookFilter;
    }
}
/**
 * 
 * class gauges
 * 
 * @returns {Gauges}
 */
function Gauges() {
    this.gaugeRanges = Array();
    /**
     * 
     * get gauge range
     * 
     * @param {array} gauge
     * 
     * @returns {null}
     */
    this.setGaugeRanges = function(gauges) {
        this.gaugeRanges = gauges;
    }

    this.initGauges = function(from, to) {

        jSlider("#LogbookGauge").slider({
            from: -100,
            to: 100,
            dimension: '',
            onstatechange: function(value) {
                var myarr = value.split(";");
                var fromCel = (myarr[0] - 32) * 5 / 9;
                var fromTo = (myarr[1] - 32) * 5 / 9;
                fromCel = fromCel.toPrecision(2) + 'C'
                fromTo = fromTo.toPrecision(2) + 'C'
                $('#celFrom').val(fromCel);
                $('#celTo').val(fromTo);
            }
        });
        var gaugeType = $('#gaugeType').val();
        
        this.updateGauge();
    }

    /**
     * 
     * show or change gauge slider
     * 
     * @returns null
     */
    this.updateGauge = function() {
        this.checkGaugeValueRange();
        var gaugeType = $('#gaugeType').val();

        if (gaugeType == 'null') {
            $('#gaugeSlider').hide();
        } else {
            $('#gaugeSlider').show();
        }

        var from = parseInt($('#gaugeRangeFrom').val());
        var to = parseInt($('#gaugeRangeTo').val());
        /*calculate scale*/
        var scaleStep = (to - from) / 10;
        var division = from;

        var scale = new Array();
        var i = 1;
        while (i != 10) {
            division += scaleStep;
            scale.push(division.toPrecision(2));
            i++;
        }
        scale.push(to);
        $("#temperatureCelContainer").hide();
        //get Gauge Dimension
        var dimension = $('#gaugeUnitTypeDescription').val();
        //var dimension = '';
        if (gaugeType == 0 || gaugeType == 6 || gaugeType == 7) {
            $("#temperatureCelContainer").show();
        } 
        
        //$('#LogbookGauge').val(from+';'+from);
        this.initNewGauge(from, to, scale, dimension);
    }

    /**
     * 
     * set defaul range parameters and update gauge slider
     * 
     * @returns {null}
     */
    this.changeGauge = function() {
        var gaugeType = $('#gaugeType').val();
        
        this.getGaugeUnitTypeList(gaugeType);
        if (gaugeType == 'null' || gaugeType == null) {
            $('#gaugeRangeFrom').val(0);
            $('#gaugeRangeTo').val(1);
        } else {
            $('#gaugeRangeFrom').val(this.gaugeRanges[gaugeType].min);
            $('#gaugeRangeTo').val(this.gaugeRanges[gaugeType].max);
        }
        //this.checkGaugeValueRange();
        //this.updateGauge();
        //this.changeGaugeUnitType();
    }

    /**
     * 
     * change temperature gauge
     * 
     * @returns {null}
     */
    this.changeGaugeUnitType = function() {
        var gaugeType = $('#gaugeType').val();
        
        var gaugeUnitTypeId = '';
        var gaugeUnitTypeDesc='';
        
        //get new Unit type
        gaugeUnitTypeId = $('#gaugeDimension option:selected').val();
        gaugeUnitTypeDesc = $('#gaugeDimension option:selected').text();
        
        //set gauge dimension
        $('#gaugeUnitTypeId').val(gaugeUnitTypeId);
        $('#gaugeUnitTypeDescription').val(gaugeUnitTypeDesc);
        this.updateGauge();
    }
    
    this.getGaugeUnitTypeList = function(gaugeType) {
        var that = this;
        $.ajax({
            url: "?action=getGaugeUnitTypeList&category=logbook",
            data: {
                gaugeType: gaugeType,
                facilityId:facilityId
            },
            type: "POST",
            dataType: 'html',
            success: function(response) {
                $('#temperatureCelContainer').html(response);
                that.changeGaugeUnitType();
                
            }
        });
    }
    
    /**
     * 
     * initialize temperature gauge
     * 
     * @returns null
     */
    this.initNewGauge = function(from, to, scale, dimension) {
        var step = 1;
        var round = 1;
        var format = '##.0';

        if ((to - from) < 10) {
            step = 0.1;
            round = 0.1;
            format = '##.00';
        }

        jSlider("#LogbookGauge").slider("redraw", {
            from: from,
            to: to,
            scale: scale,
            step: step,
            round: round,
            format: {format: format, locale: 'de'},
            dimension: dimension
        });
    }
    /**
     * 
     * The function checks if the values ​​do not go out of range 
     * 
     * @returns {null}
     */
    this.checkGaugeValueRange = function() {
        var values = $('#LogbookGauge').val();
        var maxRangeTo = $('#gaugeRangeTo').val();
        var minRangeFrom = $('#gaugeRangeFrom').val();
        values = values.split(';');
        //check if values less than max raunge
        if ((maxRangeTo <= values[0]) && (maxRangeTo <= values[1])) {
            $('.jslider-pointer-to').css('z-index', '-1');
        }
        if ((minRangeFrom >= values[0]) && (minRangeFrom >= values[1])) {
            $('.jslider-pointer-to').css('z-index', '2');
        }
    }
}


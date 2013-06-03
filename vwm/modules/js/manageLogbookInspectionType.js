/**
 * 
 * manage logbook inspection type page
 * 
 * @returns {ManageInspectionType}
 */
function ManageInspectionType() {
    var self = this;

    this.getFacilityList = function() {
        var companyId = $('#companyId').val();
        var action = $('#action').val();
        $.ajax({
            url: '?action=getFacilityList&category=logbook',
            type: 'post',
            data: {
                companyId: companyId
            },
            dataType: 'json',
            success: function(response) {
                var html = " <option value='null'>All</option>";
                for (var i = 0; i < response.length; i++) {
                    html += "<option value='" + response[i].id + "'>";
                    html += response[i].name;
                    html += "</option>";
                }
                $('#facilityId').html(html);
                if (action == 'browseCategory') {
                    self.getInspectionTypeList();
                }
            }
        });
    }

    this.getInspectionTypeList = function() {
        var companyId = $('#companyId').val();
        var facilityId = $('#facilityId').val();

        $.ajax({
            url: '?action=getInspectionTypeList&category=logbook',
            type: 'post',
            data: {
                companyId: companyId,
                facilityId: facilityId
            },
            dataType: 'html',
            success: function(response) {
                $('#inspectionTypeList').html(response);
            }
        });
    }

    this.showSubTypesList = function() {
        if ($('#showSubTypesList').is(':checked')) {
            $('#subTypeList').show();
        } else {
            $('#subTypeList').hide();
        }
    }

    this.showGaugeTypeList = function() {
        if ($('#showGaugeTypeList').is(':checked')) {
            $('#gaugeTypeList').show();
        } else {
            $('#gaugeTypeList').hide();
        }
    }

    this.saveInspectionType = function() {
        var permit = $('#inspectionTypePermit:checked').val() ? 1 : 0;
        logbookInspectionType.setName($('#inspectionTypeName').val());
        logbookInspectionType.setFacilityId($('#facilityId').val());
        logbookInspectionType.setPermit(permit);
        logbookInspectionType.save();
    }

    //function for delete Sub Types
    this.deleteInspectionSubTypes = function() {
        var rowsToDelete = new Array();
        var checkboxes = $("#inspectionSubTypeDetails").find("input[type='checkbox']");

        checkboxes.each(function(i) {
            var id = this.value;
            if (this.checked) {
                rowsToDelete.push(id);
            }
        });

        var count = rowsToDelete.length;
        for (var i = 0; i < count; i++) {
            logbookInspectionType.deleteSubType(rowsToDelete[i]);
            $('#subType_detail_' + rowsToDelete[i]).remove();
        }
    }
    
    this.deleteInspectionGaugeTypes = function() {
        var rowsToDelete = new Array();
        var checkboxes = $("#inspectionGaugeTypeDetails").find("input[type='checkbox']");

        checkboxes.each(function(i) {
            var id = this.value;
            if (this.checked) {
                rowsToDelete.push(id);
            }
        });
        var count = rowsToDelete.length;
        for (var i = 0; i < count; i++) {
            logbookInspectionType.deleteGaugeType(rowsToDelete[i]);
            $('#gaugeType_detail_' + rowsToDelete[i]).remove();
        }
    }

}


/**
 * 
 * CLASS InspectionSubTypeAddDialog
 * 
 * add inspection sub type dialog
 * 
 * @returns {inspectionSubTypeAddDialog}
 */
function InspectionSubTypeAddDialog() {
    this.divId = 'addInspectionSubTypeContainer';

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
                },
                'Save': function() {
                    that.save();
                    $(this).dialog('close');
                    that.isLoaded = false;
                }
            }
        });
    }

    this.openDialog = function() {
        $('#addInspectionSubTypeContainer').html('');
        $('#' + this.divId).dialog('open');
        if (!this.isLoaded) {
            this.loadContent();
        }
        return false;
    }

    this.loadContent = function() {
        var that = this;
        $.ajax({
            url: "?action=loadAddLogbookInspectionSubType&category=logbook",
            dataType: "text",
            success: function(response) {
                $("#" + that.divId).html(response);
                that.isLoaded = true;
                if(inspection.action == 'edit'){
                    var id = inspection.elementId;
                    var subType = logbookInspectionType.getSubTypeById(id);
                    $('#subTypeName').val(subType.getName());
                    if(subType.getHasNotes()){
                        $('input[name=hasNotes]').attr('checked', true);
                    }
                    if(subType.getHasQty()){
                        $('input[name=hasQty]').attr('checked', true);
                    }
                    if(subType.getHasGauge()){
                        $('input[name=hasGauge]').attr('checked', true);
                    }
                }
            }
        });
    }


    //save function
    this.save = function() {
        if (inspection.action == 'add') {
            this.saveAddSubType();
        } else {
            this.saveEditSubType();
        }
        this.isLoaded = false;
    }
    //save function for edit sub type
    this.saveEditSubType = function() {
        var subTypeName = $('#subTypeName').val();
        var hasNotes = $('#hasNotes:checked').val() ? 1 : 0;
        var hasQty = $('#hasQty:checked').val() ? 1 : 0;
        var hasGauge = $('#hasGauge:checked').val() ? 1 : 0;
        var id = inspection.elementId;
        $('#subtype_name_'+id).html(subTypeName);
        if(hasNotes){
            $('#subtype_notes_'+id).html('yes');
        }else{
            $('#subtype_notes_'+id).html('no');
        }
        if(hasQty){
            $('#subtype_qty_'+id).html('yes');
        }else{
            $('#subtype_qty_'+id).html('no');
        }
        if(hasGauge){
            $('#subtype_gauge_'+id).html('yes');
        }else{
            $('#subtype_gauge_'+id).html('no');
        }
        //update inspection Sub type 
        var newInspectionSubType = new LogbookInspectionSubType();
        newInspectionSubType.setId(id);
        newInspectionSubType.setName(subTypeName);
        newInspectionSubType.setHasNotes(hasNotes);
        newInspectionSubType.setHasQty(hasQty);
        newInspectionSubType.setHasGauge(hasGauge);
        //delete old sub type
        logbookInspectionType.deleteSubType(id);
        logbookInspectionType.addSubType(newInspectionSubType);
    }
    //save function for add sub type
    this.saveAddSubType = function() {
        var logbookInspectionSubType = new LogbookInspectionSubType();
        var subTypeName = $('#subTypeName').val();
        var hasNotes = $('#hasNotes:checked').val() ? 1 : 0;
        var hasQty = $('#hasQty:checked').val() ? 1 : 0;
        var hasGauge = $('#hasGauge:checked').val() ? 1 : 0;
        //setSubTypeId = 
        temporarySubTypeId++;
        logbookInspectionSubType.setId(temporarySubTypeId);
        logbookInspectionSubType.setName(subTypeName);
        logbookInspectionSubType.setHasNotes(hasNotes);
        logbookInspectionSubType.setHasQty(hasQty);
        logbookInspectionSubType.setHasGauge(hasGauge);
        logbookInspectionType.addSubType(logbookInspectionSubType);

        var html = '';
        html += '<tr id="subType_detail_' + logbookInspectionSubType.getId() + '">';
        html +=      '<td class="border_users_b border_users_r">';
        html +=         '<div>';
        html +=             '<input type="checkbox" value="' + logbookInspectionSubType.getId() + '">';
        html +=         '</div>';
        html +=     '</td>';
        html +=     '<td class="border_users_b border_users_r">';
        html +=         '<div id="subtype_name_'+temporarySubTypeId+'">';
        html +=             subTypeName;
        html +=         '</div>';
        html +=     '</td>';
        html +=     '<td class="border_users_b border_users_r">';
        html +=         '<div id="subtype_notes_'+temporarySubTypeId+'">';
                    if (hasNotes) {
                            html += 'yes';
                        } else {
                            html += 'no';
                    }
        html +=         '</div>';
        html +=     '</td>';
        html +=     '<td class="border_users_b border_users_r">';
        html +=         '<div id="subtype_qty_'+temporarySubTypeId+'">';
                if (hasQty) {
                    html += 'yes';
                } else {
                    html += 'no';
                }
        html +=         '</div>';
        html +=     '</td>';
        html +=     '<td class="border_users_b border_users_r">';
        html +=         '<div id="subtype_gauge_'+temporarySubTypeId+'">';
        if (hasGauge) {
            html += 'yes';
        } else {
            html += 'no';
        }
        html +=         '</div>';
        html +=     '</td>';
        html +=     '<td class="border_users_b border_users_r">';
        html +=         '<a onclick="inspection.checkNewDialog('+temporarySubTypeId+', \'edit\'); inspection.inspectionSubTypeAddDialog.openDialog();">edit</a>';
        html +=     '</td>';
        html += '</tr>';

        $('#inspectionSubTypeDetails').append(html);

    }

}

/**
 * 
 * CLASS InspactionGaugeTypeDialog
 * 
 * add or edit inspection gauge type dialog
 * 
 * @returns {nspactionGaugeTypeDialog}
 */
function InspactionGaugeTypeDialog(){
    this.divId = 'inspectionGaugeTypeContainer';

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
                },
                'Save': function() {
                    that.save();
                    $(this).dialog('close');
                    that.isLoaded = false;
                }
            }
        });
    }

    this.openDialog = function() {
        $('#inspectionGaugeTypeContainer').html('');
        $('#' + this.divId).dialog('open');
        if (!this.isLoaded) {
            this.loadContent();
        }
        return false;
    }

    this.loadContent = function() {
        var that = this;
        $.ajax({
            url: "?action=loadInspectionGaugeType&category=logbook",
            dataType: "text",
            success: function(response) {
               $("#" + that.divId).html(response);
                that.isLoaded = true;
                if(inspection.action == 'edit'){
                    var id = inspection.elementId;
                    var gaugeType = logbookInspectionType.getGaugeTypeById(id);
                    $('#inspectionGaugeName').val(gaugeType.getName());
                    $("#inspectionGaugeType [value='"+gaugeType.getGaugeType()+"']").attr("selected", "selected");
                }
            }
        });
    }
    
    //save function
    this.save = function() {
        if (inspection.action == 'add') {
            this.saveAddGaugeType();
        } else {
            this.saveEditGaugeType();
        }
        this.isLoaded = false;
    }
    this.saveAddGaugeType = function() {
        var logbookInspectionGaugeType = new LogbookInspectionGaugeType();
        var gaugeName = $('#inspectionGaugeName').val();
        var gaugeTypeId = $('#inspectionGaugeType').val();
        var gaugeTypeName = $('#inspectionGaugeType').find(':selected').text()

        temporaryGaugeTypeId++;
        logbookInspectionGaugeType.setId(temporaryGaugeTypeId);
        logbookInspectionGaugeType.setGaugeType(gaugeTypeId);
        logbookInspectionGaugeType.setName(gaugeName);
        logbookInspectionType.addGaugeType(logbookInspectionGaugeType);
        
        var html = '';
        html += '<tr id="gaugeType_detail_' + temporaryGaugeTypeId + '">';
        html +=     '<td class="border_users_b border_users_r">';
        html +=         '<div>';
        html +=             '<input type="checkbox" value="'+temporaryGaugeTypeId+'">';
        html +=         '</div>';
        html +=     '</td>';
        html +=     '<td class="border_users_b border_users_r">';
        html +=         '<div id="gauge_name_'+temporaryGaugeTypeId+'">';
        html +=             gaugeName
        html +=         '</div>';
        html +=     '</td>';
        html +=     '<td class="border_users_b border_users_r" id="gauge_type_'+temporaryGaugeTypeId+'">';
        html +=         '<div>';
        html +=             gaugeTypeName
        html +=         '</div>';
        html +=     '</td>';
        html +=     '<td class="border_users_b border_users_r">';
        html +=         '<div>';
        html +=             '<a onclick="inspection.checkNewDialog('+temporaryGaugeTypeId+', \'edit\'); inspection.inspactionGaugeTypeDialog.openDialog();">edit</a>';
        html +=         '</div>';
        html +=     '</td>';
        html += '</tr>';

        $('#inspectionGaugeTypeDetails').append(html);
    }
    
    this.saveEditGaugeType = function() {
        var logbookInspectionGaugeType = new LogbookInspectionGaugeType();
        var gaugeName = $('#inspectionGaugeName').val();
        var gaugeTypeId = $('#inspectionGaugeType').val();
        var gaugeTypeName = $('#inspectionGaugeType').find(':selected').text();
        
        var id = inspection.elementId;
        $('#gauge_name_'+id).html(gaugeName);
        $('#gauge_type_'+id).html(gaugeTypeName);
        
        //update inspection Sub type 
        var newInspectionGaugeType = new LogbookInspectionGaugeType();
        newInspectionGaugeType.setId(id);
        newInspectionGaugeType.setName(gaugeName);
        newInspectionGaugeType.setGaugeType(gaugeTypeId);
        
        //delete old sub type
        logbookInspectionType.deleteGaugeType(id);
        logbookInspectionType.addGaugeType(newInspectionGaugeType);
    }
}

var manager = new ManageInspectionType();
var inspection;


function InspectionTypeDialog() {
    this.elementId = '';
    this.action = '';
    var that = this;
    this.inspectionSubTypeAddDialog = new InspectionSubTypeAddDialog();
    this.inspactionGaugeTypeDialog = new InspactionGaugeTypeDialog();
    
    
    /**
     * function for updating resource id in dialog window and for getting common action add or edit 
     * (We need distinguish resources and action as we use one template for dialog window)
     * 
     * @param {int} id Resource id
     * @param {String} action current action add or edit
     * 
     */
    this.checkNewDialog = function(id, action) {
        if (that.elementId != id) {
            that.elementId = id;
        }
        inspection.action = action;
        if (action == 'add') {
            $("#addInspectionSubTypeContainer").dialog('option', 'title', 'Add inspection sub type');
            $("#inspectionGaugeTypeContainer").dialog('option', 'title', 'Add inspection gauge type');
        } else {
            $("#addInspectionSubTypeContainer").dialog('option', 'title', 'Edit inspection sub type');
            $("#inspectionGaugeTypeContainer").dialog('option', 'title', 'Edit inspection gauge type');
        }
    }
    
}

$(function() {
    //	ini global object
    inspection = new InspectionTypeDialog();
    inspection.inspectionSubTypeAddDialog.iniDialog();
    inspection.inspactionGaugeTypeDialog.iniDialog();
});
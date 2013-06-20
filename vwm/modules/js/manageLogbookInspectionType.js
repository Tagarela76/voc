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
                logbookTemplateManager.changelogbookTemplateList();
            }
        });
    }

    this.getInspectionTypeList = function() {
        var companyId = $('#companyId').val();
        var facilityId = $('#facilityId').val();
        var logbookTemplateId = $('#logbookTemplateId').val();
        window.location.href = '?action=browseCategory&category=logbook&facilityId=' + facilityId + '&companyId=' + companyId + '&logbookTemplateId='+logbookTemplateId+'&bookmark=logbookInspectionType';
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
    
    this.showLogbookDescriptionList = function(){
       if ($('#showLogbookDescription').is(':checked')) {
            $('#LogbookDescriptionList').show();
        } else {
            $('#LogbookDescriptionList').hide();
        } 
    }

    this.saveInspectionType = function() {
        var permit = $('#inspectionTypePermit:checked').val() ? 1 : 0;
        logbookInspectionType.setName($('#inspectionTypeName').val());
        logbookInspectionType.setlogbookTemplate($('#selectedLogbookTemplatesIds').val());
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
    
    this.deleteLogbookDescription = function(){
        var rowsToDelete = new Array();
        var checkboxes = $("#inspectionLogbookDescriptionDetails").find("input[type='checkbox']");
        checkboxes.each(function(i) {
            var id = this.value;
            if (this.checked) {
                rowsToDelete.push(id);
            }
        });
        var count = rowsToDelete.length;
        for (var i = 0; i < count; i++) {
            logbookInspectionType.deleteLogbookDescription(rowsToDelete[i]);
            $('#logbook_description_detail_' + rowsToDelete[i]).remove();
        }
    }
    

}

function ManagerLogbookTemplate() {
    
    /**
     * 
     * show company facility list
     * 
     * @param {int} id
     * @returns {null}
     */
    this.showFacilityList = function(id) {
        var isShowFacility = $('#showFacilityList_' + id + ':checked').val() ? 1 : 0;

        if (isShowFacility) {
            $('#companyFacilityList_' + id).show();
        } else {
            var checkboxes = $("#companyListContainer_" + id).find("input[type='checkbox']");
            checkboxes.each(function(i) {
                var id = this.id;
                $("#" + id).removeAttr("checked");
            });
            $('#companyFacilityList_' + id).hide();
        }
    }

    /**
     * 
     * check all facilities 
     * 
     * @returns {null}
     */
    this.checkAllFacilityTemplate = function() {
        var companyCount = $('#companyCount').val();
        CheckAll(this);
        for (var i = 0; i < companyCount; i++) {
            //show facility list of selected companies
            $('#companyFacilityList_' + i).show();
        }
    }
    /**
     * 
     * unCheck all facilities 
     * 
     * @returns {null}
     */
    this.unCheckAllFacilityTemplate = function() {
        //get company count
        var companyCount = $('#companyCount').val();
        unCheckAll(this);
        for (var i = 0; i < companyCount; i++) {
            //hide facility list of not selected companies
            $('#companyFacilityList_' + i).hide();
        }
    }
    
    this.changelogbookTemplateList = function(){
        var companyId = $('#companyId').val();
        var facilityId = $('#facilityId').val();
        window.location.href = '?action=browseCategory&category=logbook&facilityId='+facilityId+'&companyId='+companyId;
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
        if (inspection.action == 'edit') {
            var id = inspection.elementId;
            var subType = logbookInspectionType.getSubTypeById(id);
            var gaugeType = subType.getAttributes().gaugeType;
        }
        
        $.ajax({
            url: "?action=loadAddLogbookInspectionSubType&category=logbook",
            type: 'post',
            data:{
                gaugeTypeId: gaugeType
            },
            dataType: "text",
            success: function(response) {
                $("#" + that.divId).html(response);
                that.isLoaded = true;
                if (inspection.action == 'edit') {
                    $('#subTypeName').val(subType.getName());
                    if (subType.getHasNotes()) {
                        $('input[name=hasNotes]').attr('checked', true);
                    }
                    if (subType.getHasQty()) {
                        $('input[name=hasQty]').attr('checked', true);
                    }
                    if (subType.getHasGauge()) {
                        $('input[name=hasGauge]').attr('checked', true);
                        $('#defaultGauge').show();
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
        var gaugeType = $('#gaugeType').val();
        var id = inspection.elementId;
        $('#subtype_name_' + id).html(subTypeName);
        if (hasNotes) {
            $('#subtype_notes_' + id).html('yes');
        } else {
            $('#subtype_notes_' + id).html('no');
        }
        if (hasQty) {
            $('#subtype_qty_' + id).html('yes');
        } else {
            $('#subtype_qty_' + id).html('no');
        }
        if (hasGauge) {
            $('#subtype_gauge_' + id).html('yes');
        } else {
            $('#subtype_gauge_' + id).html('no');
        }
        //update inspection Sub type 
        var newInspectionSubType = new LogbookInspectionSubType();
        newInspectionSubType.setId(id);
        newInspectionSubType.setName(subTypeName);
        newInspectionSubType.setHasNotes(hasNotes);
        newInspectionSubType.setHasQty(hasQty);
        newInspectionSubType.setHasGauge(hasGauge);
        if(gaugeType!='none'){
            newInspectionSubType.setGaugeType(gaugeType);
        }
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
        var gaugeType = $('#gaugeType').val();
        
        //setSubTypeId = 
        temporarySubTypeId++;
        logbookInspectionSubType.setId(temporarySubTypeId);
        logbookInspectionSubType.setName(subTypeName);
        logbookInspectionSubType.setHasNotes(hasNotes);
        logbookInspectionSubType.setHasQty(hasQty);
        logbookInspectionSubType.setHasGauge(hasGauge);
        if(gaugeType!='none'){
            logbookInspectionSubType.setGaugeType(gaugeType);
        }
        
        logbookInspectionType.addSubType(logbookInspectionSubType);

        var html = '';
        html += '<tr id="subType_detail_' + logbookInspectionSubType.getId() + '">';
        html += '<td class="border_users_b border_users_r">';
        html += '<div>';
        html += '<input type="checkbox" value="' + logbookInspectionSubType.getId() + '">';
        html += '</div>';
        html += '</td>';
        html += '<td class="border_users_b border_users_r">';
        html += '<div id="subtype_name_' + temporarySubTypeId + '">';
        html += subTypeName;
        html += '</div>';
        html += '</td>';
        html += '<td class="border_users_b border_users_r">';
        html += '<div id="subtype_notes_' + temporarySubTypeId + '">';
        if (hasNotes) {
            html += 'yes';
        } else {
            html += 'no';
        }
        html += '</div>';
        html += '</td>';
        html += '<td class="border_users_b border_users_r">';
        html += '<div id="subtype_qty_' + temporarySubTypeId + '">';
        if (hasQty) {
            html += 'yes';
        } else {
            html += 'no';
        }
        html += '</div>';
        html += '</td>';
        html += '<td class="border_users_b border_users_r">';
        html += '<div id="subtype_gauge_' + temporarySubTypeId + '">';
        if (hasGauge) {
            html += 'yes';
        } else {
            html += 'no';
        }
        html += '</div>';
        html += '<div >';
        html += '<input type="hidden" id="subtype_gauge_type_' + temporarySubTypeId + '" value="'+gaugeType+'">'
        html += '</div>';
        html += '</td>';
        html += '<td class="border_users_b border_users_r">';
        html += '<a onclick="inspection.checkNewDialog(' + temporarySubTypeId + ', \'edit\'); inspection.inspectionSubTypeAddDialog.openDialog();">edit</a>';
        html += '</td>';
        html += '</tr>';

        $('#inspectionSubTypeDetails').append(html);

    }
    
    this.getSubTypeDefaultGauge = function(){
        var checked = $('#hasGauge').is(':checked'); 
        if(checked){
            $('#defaultGauge').show();
        }else{
            $('#defaultGauge').hide();
        }
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
function InspactionGaugeTypeDialog() {
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
                if (inspection.action == 'edit') {
                    var id = inspection.elementId;
                    var gaugeType = logbookInspectionType.getGaugeTypeById(id);
                    $('#inspectionGaugeName').val(gaugeType.getName());
                    $("#inspectionGaugeType [value='" + gaugeType.getGaugeType() + "']").attr("selected", "selected");
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
        html += '<td class="border_users_b border_users_r">';
        html += '<div>';
        html += '<input type="checkbox" value="' + temporaryGaugeTypeId + '">';
        html += '</div>';
        html += '</td>';
        html += '<td class="border_users_b border_users_r">';
        html += '<div id="gauge_name_' + temporaryGaugeTypeId + '">';
        html += gaugeName
        html += '</div>';
        html += '</td>';
        html += '<td class="border_users_b border_users_r" id="gauge_type_' + temporaryGaugeTypeId + '">';
        html += '<div>';
        html += gaugeTypeName
        html += '</div>';
        html += '</td>';
        html += '<td class="border_users_b border_users_r">';
        html += '<div>';
        html += '<a onclick="inspection.checkNewDialog(' + temporaryGaugeTypeId + ', \'edit\'); inspection.inspactionGaugeTypeDialog.openDialog();">edit</a>';
        html += '</div>';
        html += '</td>';
        html += '</tr>';

        $('#inspectionGaugeTypeDetails').append(html);
    }

    this.saveEditGaugeType = function() {
        var logbookInspectionGaugeType = new LogbookInspectionGaugeType();
        var gaugeName = $('#inspectionGaugeName').val();
        var gaugeTypeId = $('#inspectionGaugeType').val();
        var gaugeTypeName = $('#inspectionGaugeType').find(':selected').text();

        var id = inspection.elementId;
        $('#gauge_name_' + id).html(gaugeName);
        $('#gauge_type_' + id).html(gaugeTypeName);

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

/**
 * 
 * CLASS AddLogbookTemplateFacilityDialog
 * 
 * assign facility to logbook template
 * 
 * @returns {null}
 */
function AddLogbookTemplateFacilityDialog() {
    this.divId = 'addLogbookTemplateFacilityContainer';

    this.isLoaded = false;

    this.iniDialog = function(divId) {
        divId = typeof divId !== 'undefined' ? divId : this.divId;
        if (divId != this.divId) {
            this.divId = divId;
        }

        var that = this;
        $("#" + divId).dialog({
            width: 350,
            height: 500,
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
        $('#addLogbookTemplateFacilityContainer').html('');
        $('#' + this.divId).dialog('open');
        if (!this.isLoaded) {
            this.loadContent();
        }
        return false;
    }

    this.loadContent = function() {
        var that = this;
        var facilityIds = $('#selectedFacilityIds').val();
        var companyIds = $('#selectedCompanyIds').val();
        $.ajax({
            url: "?action=loadLogbookTemplateFacility&category=logbook",
            data:{ 
                selectedFacilityIds: facilityIds,
                selectedCompanyIds: companyIds
            },
            dataType: "text",
            success: function(response) {
                $("#" + that.divId).html(response);
                that.isLoaded = true;
            }
        });
    }

    //save function
    this.save = function() {
        var companyIds = new Array();
        var facilityIds = new Array();
        //get companies count
        var companyCount = $('#companyCount').val();
        
        for (var i = 0; i < companyCount; i++) {
            //if company checked
            var isShowFacility = $('#showFacilityList_' + i + ':checked').val() ? 1 : 0;
            if (isShowFacility) {
                //add selected companies ids
                companyIds.push($('#showFacilityList_' + i).val());
                var checkboxes = $("#companyListContainer_" + i).find("input[type='checkbox']");
                checkboxes.each(function(i) {
                    var id = this.value;
                    if (this.checked) {
                        //add selected facilities ids
                        facilityIds.push(id);
                    }
                });
            }
        }
        //transform to string
        companyIds = companyIds.join(',');
        facilityIds = facilityIds.join(',');
        //display facility ids
        $('#addFacilityIdsContainer').html(facilityIds);
        //save facility ids in hidden input
        $('#selectedCompanyIds').val(companyIds);
        $('#selectedFacilityIds').val(facilityIds);
    }
}


/**
 * 
 * CLASS SetInspectionTypeToTemplateDialog
 * 
 * assign facility to logbook template
 * 
 * @returns {null}
 */
function SetInspectionTypeToTemplateDialog() {
    this.divId = 'setInspectionTypeToTemplateContainer';

    this.isLoaded = false;

    this.iniDialog = function(divId) {
        divId = typeof divId !== 'undefined' ? divId : this.divId;
        if (divId != this.divId) {
            this.divId = divId;
        }

        var that = this;
        $("#" + divId).dialog({
            width: 350,
            height: 500,
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
        $('#setInspectionTypeToTemplateContainer').html('');
        $('#' + this.divId).dialog('open');
        if (!this.isLoaded) {
            this.loadContent();
        }
        return false;
    }

    this.loadContent = function() {
        var that = this;
        var logbookTemplatesIds = $('#selectedLogbookTemplatesIds').val();
        var companyId = $('#companyId').val();
        var facilityId = $('#facilityId').val();
        $.ajax({
            url: "?action=loadInspectionTypeLogbookTemplate&category=logbook",
            data: {
                logbookTemplatesIds: logbookTemplatesIds,
                facilityId: facilityId,
                companyId: companyId
            },
            dataType: "text",
            success: function(response) {
                $("#" + that.divId).html(response);
                that.isLoaded = true;
            }
        });
    }

    //save function
    this.save = function() {
        var logbookTemplateIds = new Array();
        var checkboxes = $("#logbookTemplateList").find("input[type='checkbox']");

        checkboxes.each(function(i) {
            var id = this.value;
            if (this.checked) {
                logbookTemplateIds.push(id);
            }
        });
        logbookTemplateIds = logbookTemplateIds.join(',');
        $('#selectedLogbookTemplatesIds').val(logbookTemplateIds);
        $('#showSelectedLogbookTemplatesIds').html(logbookTemplateIds);
    }
}

/**
 * 
 * CLASS AddLogbookDescriptionDialog
 * 
 * add logbook description to inspection Type
 * 
 * @returns {null}
 */
function AddLogbookDescriptionDialog() {
    this.divId = 'addLogbookDescriptionContainer';

    this.isLoaded = false;

    this.iniDialog = function(divId) {
        divId = typeof divId !== 'undefined' ? divId : this.divId;
        if (divId != this.divId) {
            this.divId = divId;
        }

        var that = this;
        $("#" + divId).dialog({
            width: 350,
            height: 500,
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
        $('#addLogbookDescriptionContainer').html('');
        $('#' + this.divId).dialog('open');
        if (!this.isLoaded) {
            this.loadContent();
        }
        return false;
    }

    this.loadContent = function() {
        
        var that = this;
        $.ajax({
            url: "?action=loadAddLogbookDescription&category=logbook",
            dataType: "text",
            success: function(response) {
                $("#" + that.divId).html(response);
                that.isLoaded = true;
                if (inspection.action == 'edit') {
                    var id = inspection.elementId;
                    var logbookDescription = logbookInspectionType.getLogbookDescriptionById(id);
                    $('#description').val(logbookDescription.getDescription());
                    if (logbookDescription.getNotes()) {
                        $('input[name=notes]').attr('checked', true);
                    }
                }
            }
        });
    }

    //save function
    this.save = function() {
        if (inspection.action == 'add') {
            this.addLogbookDescription();
        } else {
            this.editLogbookDescription();
        }
        this.isLoaded = false;
    }
    
    //add function
    this.addLogbookDescription = function() {
        var logbookDescription = new LogbookDescription();
        
        var description = $('#description').val();
        var notes = $('#notes:checked').val() ? 1 : 0;
        var html='';
        
        temporaryLogbookDescriptionId++;
        logbookDescription.setId(temporaryLogbookDescriptionId);
        logbookDescription.setDescription(description);
        logbookDescription.setNotes(notes);
        logbookInspectionType.addLogbookDescription(logbookDescription);
        
        html='<tr id="logbook_description_detail_' + temporaryLogbookDescriptionId + '">';
        
        html+=  '<td class="border_users_b border_users_r">';
        html+=    '<input type="checkbox" value="'+temporaryLogbookDescriptionId+'">';
        html+=  '</td>';
        
        html += '<td class="border_users_b border_users_r">';
        html +=     '<div id="description_description_' + temporaryLogbookDescriptionId + '">';
        html +=         description;
        html +=     '</div>';
        html += '</td>';
        
        html += '<td class="border_users_b border_users_r">';
        html +=     '<div id="description_notes_' + temporaryLogbookDescriptionId + '">';
        if (notes) {
            html += 'yes';
        } else {
            html += 'no';
        }
        html +=     '</div>';
        html += '</td>';
        
        html += '<td class="border_users_b border_users_r">';
        html +=     '<a onclick="inspection.checkNewDialog(' + temporaryLogbookDescriptionId + ', \'edit\'); inspection.addLogbookDescription.openDialog();">edit</a>';
        html += '</td>';
        
        html+='</tr>';
        
        $('#inspectionLogbookDescriptionDetails').append(html);
    }
    //edit function
    this.editLogbookDescription = function() {
        var description = $('#description').val();
        var notes = $('#notes:checked').val() ? 1 : 0;
        var id = inspection.elementId;
        
        $('#description_description_'+id).html(description);
        if(notes){
            $('#description_notes_'+id).html('yes');
        }else{
            $('#description_notes_'+id).html('no');
        }
        
        var logbookDescription = new LogbookDescription();
        logbookDescription.setId(id);
        logbookDescription.setDescription(description);
        logbookDescription.setNotes(notes);
        //update description
        logbookInspectionType.deleteLogbookDescription(id);
        logbookInspectionType.add
        
        //update Description
        logbookInspectionType.deleteSubType(id);
        logbookInspectionType.addLogbookDescription(logbookDescription);
        
        
    }
}

var manager = new ManageInspectionType();
var logbookTemplateManager = new ManagerLogbookTemplate();
var inspection;


function InspectionTypeDialog() {
    this.elementId = '';
    this.action = '';
    var that = this;
    this.inspectionSubTypeAddDialog = new InspectionSubTypeAddDialog();
    this.inspactionGaugeTypeDialog = new InspactionGaugeTypeDialog();
    this.addLogbookTemplateFacilityDialog = new AddLogbookTemplateFacilityDialog();
    this.setInspectionTypeToTemplate = new SetInspectionTypeToTemplateDialog();
    this.addLogbookDescription = new AddLogbookDescriptionDialog();


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
    inspection.addLogbookTemplateFacilityDialog.iniDialog();
    inspection.setInspectionTypeToTemplate.iniDialog();
    inspection.addLogbookDescription.iniDialog();
});
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
                if (action == 'browseCategory') {
                    var html = " <option value='null'>All</option>";
                } else {
                    var html = '';
                }
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
        console.log(logbookInspectionType.getAttributes());
    }
    
    //function for delete Resources
    this.deleteInspectionSubTypes = function(){
        var rowsToDelete = new Array();
        var checkboxes = $("#inspectionSubTypeDetails").find("input[type='checkbox']");
	 
        checkboxes.each(function(i){
            var id = this.value;
            if(this.checked) {
                rowsToDelete.push(id);
            }
        });
        
        var count = rowsToDelete.length;
        for(var i = 0; i<count; i++){
            logbookInspectionType.deleteSubType(rowsToDelete[i]);
            $('#subType_detail_'+rowsToDelete[i]).remove();
        }
    }

}


/**
 * 
 * add inspection sub type dialog
 * 
 * @returns {inspectionSubTypeAddEdit}
 */
function inspectionSubTypeAddEdit() {
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
                    that.allUsers = [];
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
                //check action
            }
        });
    }

    //save function
    this.save = function() {
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
        html += '<tr id="subType_detail_'+logbookInspectionSubType.getId()+'">';
        html += '<td class="border_users_b border_users_r">';
        html += '<input type="checkbox" value="'+logbookInspectionSubType.getId()+'">';
        html += '</td>';
        html += '<td class="border_users_b border_users_r">';
        html += subTypeName;
        html += '</td>';
        html += '<td class="border_users_b border_users_r">';
        if (hasNotes) {
            html += 'yes';
        } else {
            html += 'no';
        }
        html += '</td>';
        html += '<td class="border_users_b border_users_r">';
        if (hasQty) {
            html += 'yes';
        } else {
            html += 'no';
        }
        html += '</td>';
        html += '<td class="border_users_b border_users_r">';
        if (hasGauge) {
            html += 'yes';
        } else {
            html += 'no';
        }
        html += '</td>';
        html += '<td class="border_users_b border_users_r">';
        html += 'edit';
        html += '</td>';
        html += '</tr>';

        $('#inspectionSubTypeDetails').append(html);

    }


}

var manager = new ManageInspectionType();
var inspection;

function InspectionTypeDialog() {
    this.inspectionSubTypeAddEdit = new inspectionSubTypeAddEdit();
}

$(function() {
    //	ini global object
    inspection = new InspectionTypeDialog();
    inspection.inspectionSubTypeAddEdit.iniDialog();

});
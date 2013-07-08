function showEquipment(){
    var hasPermit = $('#hasPermit').is(':checked')
    if(hasPermit){
        $('#showPermitNumber').show();
    }else{
        $('#showPermitNumber').hide();
        $('#permitNumber').val('');
    }
}


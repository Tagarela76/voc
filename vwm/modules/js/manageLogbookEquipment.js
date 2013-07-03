function showEquipment(){
    var hasPermit = $('#hasPermit').is(':checked')
    console.log(hasPermit);
    if(hasPermit){
        $('#showPermitNumber').show();
    }else{
        $('#showPermitNumber').hide();
        $('#permitNumber').val('');
    }
}


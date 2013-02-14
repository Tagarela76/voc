function addstepToUrl(){
	var url = $('#urlMixAdd').val();
	var step = $('#availableSteps option:selected').val();
	var count = url.indexOf('&stepID');
	url = url.substr(0, count);
	url += '&stepID='+step;
	$('#urlMixAdd').val(url);
	
}

function addStepWithOutMix(){
	var step = $('#availableSteps option:selected').val();
	var processInstanceId = $('#processInstanceId').val();
	
	if(step=='No Process'){
		alert('select process step');
	}else{
		$.ajax({
			url: "?action=addStepWithOutMix&category=repairOrder",
			async: false,
			data: {
				"stepId":step,
				"processInstanceId":processInstanceId
			},
			type: "POST",
			success: function (result) {
				if(result == 1){
					window.location.reload();
					
				}else{
					alert('fail to save step.Try again please.');
				}
			//	generateNotify('ololo', 'blue');
             
			}
		});
	}

}



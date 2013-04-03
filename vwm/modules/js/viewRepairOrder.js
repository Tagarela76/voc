
function StepManager(){
	
	this.addstepToUrl = function(){
		var url = $('#urlMixAdd').val();
		var stepId = $('#availableSteps option:selected').val();
		var count = url.indexOf('&stepID');
		url = url.substr(0, count);
		url += '&stepID='+stepId;
		$('#urlMixAdd').val(url);
		
		if(stepId!=0){
			$('#editStep').show();
		}else{
			$('#editStep').hide();
		}
	
	}

	this.addStepWithOutMix = function() {

        var stepId = $('#availableSteps option:selected').val();
        //check if step exist. If not - create step
        if (stepId == 'create') {
            this.createStep($('#processInstanceId').val());
        } else {
            var processInstanceId = $('#processInstanceId').val();

            if (stepId == 0) {
                alert('select process step');
            } else {
                $.ajax({
                    url: "?action=addStepWithOutMix&category=repairOrder",
                    async: false,
                    data: {
                        "stepId": stepId,
                        "processInstanceId": processInstanceId
                    },
                    type: "POST",
                    success: function(result) {
                        if (result == 1) {
                            window.location.reload();
                        } else {
                            alert('fail to save step.Try again please.');
                        }
                    }
                });
            }
        }
    }
	
	this.editStep = function(stepId){
		var url = $('#urlMixEdit').val();
		document.location.href = url+"&stepId="+stepId;
	}
    
    this.createStep = function(processId){
        var url = $('#urlMixEdit').val();
        document.location.href = url+"&processId="+processId;
    }
    
     //function for adding step
    this.addStep = function(){
        var stepId = $('#availableSteps option:selected').val();
        
        if(stepId == 0 || stepId==undefined){
            //add mix with out Step
            document.location.href=$('#urlMixAdd').val();
        }else{
        $.ajax({
				url: "?action=isStepCanHaveMix&category=repairOrder",
				async: false,
				data: {
					"stepId":stepId,
				},
				type: "POST",
				success: function (result) {
					if(result == 1){
                        document.location.href=$('#urlMixAdd').val();
                    }else{
                        stepManager.addStepWithOutMix();
                    }
				}
			});
        }
    }
}


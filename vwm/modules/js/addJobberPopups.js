	function showJobber()
	{	
		$('input[id^=jobber]').each(function(el)
		{
			if($(this).attr('checked')==true)
				$(this).attr('temp','true')
		});
		$("#Jobberlist").dialog('open');
		RemoveJobbersAtTheBegining();
	}
	
	function showSupplier()
	{	
		$('input[id^=supplier]').each(function(el)
		{
			if($(this).attr('checked')==true)
				$(this).attr('temp','true')
		});
		$("#Supplierlist").dialog('open');
	}	

	function cancelPopupSupplier()
	{	
		var num=0;
		$('input[id^=supplier]').each(function(el)
		{
			var val=$(this).attr('temp');			
			if (val=='true')
				$(this).attr('checked',true);	
			else
				$(this).attr('checked',false);						
			num++;			
		});		
		$("#Supplierlist").dialog('close');
	}
		function cancelPopupJobber()
	{	
		var num=0;
		$('input[id^=jobber]').each(function(el)
		{
			var val=$(this).attr('temp');			
			if (val=='true')
				$(this).attr('checked',true);	
			else
				$(this).attr('checked',false);						
			num++;			
		});	
		
		$("#Jobberlist").dialog('close');
	}
	
	function addSupplierData() {
		var supplierData = document.getElementById("supplier_data");
		supplierData.innerHTML = "";
		$('input[id^=supplier]').each(function(el) {
			if ($(this).attr('checked')) {
				var checkInput = '<input type="checkbox" name="' + $(this).attr('name') + '" id="' + $(this).attr('name') + '" value="' + $(this).attr('value') + '" checked />';
				$("#supplier_data").append(checkInput);
			}
		});	
	}
	
	function addJobberData() {
		var jobberData = document.getElementById("jobber_data");
		jobberData.innerHTML = "";
		$('input[id^=jobber]').each(function(el) {
			if ($(this).attr('checked')) {
				var checkInput = '<input type="checkbox" name="' + $(this).attr('name') + '" id="' + $(this).attr('name') + '" value="' + $(this).attr('value') + '" checked />';
				$("#jobber_data").append(checkInput);
			}
		});	
	}	

	$(function() {
		$("#Supplierlist").dialog({
				width: 800,
				autoOpen: false,
				resizable: true,
				dragable: true,			
				modal: true,
				buttons: {				
					'Cancel': function() {					
						cancelPopupSupplier()
					},
					'Save': function() {
						addSupplierData();
						$(this).dialog('close');
					}
				}	
			});
	});
	
	$(function() {
		$("#Jobberlist").dialog({
				width: 800,
				autoOpen: false,
				resizable: true,
				dragable: true,			
				modal: true,
				buttons: {				
					'Cancel': function() {					
						cancelPopupJobber()
					},
					'Save': function() {
						addJobberData();
						$(this).dialog('close');
					}
				}	
			});
	});	

function checkJobbers(jobberID){

	$.ajax({
      url: "modules/ajax/jobbers4facility.php",      		
      type: "POST",
      async: false,
      data: {"id":jobberID},      			
      dataType: "html",
      success: 	function (response) 
     			{   
      				jsonResponse=eval("("+response+")");
					answerResult(jsonResponse);										
      			}        		   			   	
	});
}
function RemoveJobbersAtTheBegining(){
		$('input[id^=jobber]').each(function(el)
		{	$(this).css('display','inline-block');
			if($(this).attr('checked')==true)
				
				checkJobbers($(this).attr('value'));
		
				
		});	
}

function answerResult(jsonResponse) {
arr = isArray(jsonResponse);

	if (jsonResponse != 'false') {	//	It's more than 1 jobber with the same supplier		
		if ( isArray(jsonResponse) ){
			for(var i =0; i < jsonResponse.length; i++){
		
				$('input[id^=jobber]').each(function(el)
				{
					if($(this).attr('value')==jsonResponse[i]){
						if ($(this).css('display') == 'none'){
							$(this).show("slow");
						}else{
							$(this).hide("slow");
							$(this).attr('checked',false);
						}
						
					}
						
						
				});
			}
		}		
		
	}

}

function isArray(obj) {
    return obj.constructor == Array;
}
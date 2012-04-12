	function addEvent(day,month,year)
	{	var d = day;
		var m = month;
		var y = year;
		
		$("#addEvent").dialog('open');

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
		function cancelPopupEvent()
	{	
		var num=0;
		$('input[id^=Event]').each(function(el)
		{
			var val=$(this).attr('temp');			
			if (val=='true')
				$(this).attr('checked',true);	
			else
				$(this).attr('checked',false);						
			num++;			
		});	
		
		$("#addEvent").dialog('close');
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
	
	function addEventData() {
		var EventData = document.getElementById("Event_data");
		EventData.innerHTML = "";
		$('input[id^=Event]').each(function(el) {
			if ($(this).attr('checked')) {
				var checkInput = '<input type="checkbox" name="' + $(this).attr('name') + '" id="' + $(this).attr('name') + '" value="' + $(this).attr('value') + '" checked />';
				$("#Event_data").append(checkInput);
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
		$("#addEvent").dialog({
				width: 800,
				autoOpen: false,
				resizable: true,
				dragable: true,			
				modal: true,
				buttons: {				
					'Cancel': function() {					
						cancelPopupEvent()
					},
					'Save': function() {
						addEventData();
						$(this).dialog('close');
					}
				}	
			});
	});	

function checkEvents(EventID){

	$.ajax({
      url: "modules/ajax/Events4facility.php",      		
      type: "POST",
      async: false,
      data: {"id":EventID},      			
      dataType: "html",
      success: 	function (response) 
     			{   
      				jsonResponse=eval("("+response+")");
					answerResult(jsonResponse);										
      			}        		   			   	
	});
}
function RemoveEventsAtTheBegining(){
		$('input[id^=Event]').each(function(el)
		{	$(this).css('display','inline-block');
			if($(this).attr('checked')==true)
				
				checkEvents($(this).attr('value'));
		
				
		});	
}

function answerResult(jsonResponse) {
arr = isArray(jsonResponse);

	if (jsonResponse != 'false') {	//	It's more than 1 Event with the same supplier		
		if ( isArray(jsonResponse) ){
			for(var i =0; i < jsonResponse.length; i++){
		
				$('input[id^=Event]').each(function(el)
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
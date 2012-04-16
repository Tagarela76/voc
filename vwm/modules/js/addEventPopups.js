		var d ;
		var m ;
		var y ;
		var event;
	function addEvent(day,month,year)
	{d = day;
		m = month;
		y = year;
		
		
		$("#ui-dialog-title-addEvent").html('Add Event on '+d+'/'+m+'/'+y+'');
		$("#addEvent").dialog('open');
	}

	function disabledTD(obj){
		/*
		event = $(obj).parents('td').attr('onclick');
		$(obj).parents('td').attr('onclick','');	
		console.log(event );
		*/
	}
	
	function enabledTD(obj){
		/*
		$(obj).parents('td').onclick = event;	
		console.log(event );
		$(obj).parents('td').attr('disabled',false);	
*/
	}	

	function cancelPopupEvent()
	{	
		$('#title_error').html('');
		$('#email_error_div').css('display', 'none');	
		$('#title').attr('value','');
		$('#description').attr('value','');
		$('#email').attr('value','');
		$('#url').attr('value','');		
		$('#category nth-child(3)').attr('selected', 'selected');	
		$("#addEvent").dialog('close');
	}
	
	function addEventData() {
		data = {"title" : $('#title').val(), "description" : $('#description').val(), "email": $('#email').val(), "url" : $('#url').val(),"category" : $('#category option:selected').attr('name'),"month" : m,"day" : d,"year" : y};

		$.ajax({
		url: "modules/ajax/event4calendar.php",      		
		type: "POST",
		async: false,
		data: data,      			
		dataType: "html",
		success: 	function (response) 
					{   
						answerResult(response);
					}        		   			   	
		});
	}	

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
						isCorrect = checkEvents();
						//addEventData();
						if (isCorrect){
							addEventData();
							$(this).dialog('close');
						}
						
					}
				}	
			});
	});	

function mail (str) {return /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/.test(str);}

function checkEvents(){
	var title;
	var dutyField = "Please enter field ";
	var wrongField = "Incorrect value in ";
	var check = true;

	function checkError (field, str) 
	{	
		$('#'+field+'_error').html(str);
		$('#'+field+'_error_div').css('display', 'block');
		//document.getElementById("alert").innerHTML = str;
		check = false;
	}	
	
	if (check)
	{
		if ($('#title').val() == '') checkError('title', 'Value is empty!');
	}
								 
	if (check)
	{
		title = '"E-mail"';
		if ($('#email').val() != '' && !mail($('#email').val())) checkError('email', wrongField + title);
	}	
	//if (check)  {addEventData();}
							 
	return check;	
}


function answerResult(Response) {


	if (Response != 'false') {	
		console.log(Response);
		$('#title_error').html('');
		$('#email_error_div').css('display', 'none');	
		$('#title').attr('value','');
		$('#description').attr('value','');
		$('#email').attr('value','');
		$('#url').attr('value','');		
		$('#category nth-child(3)').attr('selected', 'selected');		
		
		$(location).attr('href','sales.php?action=browseCategory&category=calendar');

	}

}

function isArray(obj) {
    return obj.constructor == Array;
}
function saveEmailNotifications() {
	
	//	scan checked rules
	var notifyCount = 0;
	var notifyData="";
	var notifies = document.all.notifyID;
	for (i=0;i<notifies.length;i++) {
  		if (notifies[i].checked) {    		
    		notifyData+="notifyID_"+notifyCount+"="+notifies[i].value+"&";
    		notifyCount++;
    	}
  	}  	
	notifyData+="notifyCount="+notifyCount;
	 	
  	notifyData+="&userID="+document.getElementById('userID').value;
  	notifyData+="&category="+document.getElementById('categoryName').value;
  	notifyData+="&categoryID="+document.getElementById('categoryNameID').value;
	$.ajax({
      	url: "modules/ajax/saveEmailNotifications.php",      		
      	type: "POST",
      	async: false,
      	data: notifyData,      			
      	dataType: "html",
      	success: function (response) 
      		{   
      			$("#emailNotify").dialog('close');									
      		}        		   			   	
		});
}

$(function() {
	$("#emailNotify").dialog({
			width: 800,
			autoOpen: false,
			resizable: true,
			dragable: true,			
			modal: true,
			buttons: {				
				'Cancel': function() {					
					$(this).dialog('close');
				},
				'Save': function() {
					saveEmailNotifications();
				}
			}	
		});
});
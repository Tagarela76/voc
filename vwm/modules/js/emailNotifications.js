function saveEmailNotifications() {
	
	//	scan checked rules
	var notifyCount = 0;
	var notifyData="";
	var notifies = document.forms[1].notifyID;
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
      			Popup.hide('emailNotifyModal');										
      		}        		   			   	
		});
}
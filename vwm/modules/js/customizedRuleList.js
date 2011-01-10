function saveCustomizedRuleList() {
	
	//	scan checked rules
	var ruleCount = 0;
	var ruleData="";
	var rules = document.forms[0].ruleID;
	for (i=0;i<rules.length;i++) {
  		if (rules[i].checked) {    		
    		ruleData+="ruleID_"+ruleCount+"="+rules[i].value+"&";
    		ruleCount++;
    	}
  	}  	
	ruleData+="ruleCount="+ruleCount;
	
  	//	scan selected role
  	var role = document.forms[0].role;
  	for (i=0;i<role.length;i++) {
  		if (role[i].checked) 
  		{  			
  			ruleData+="&role="+role[i].value;
 			roleValue = role[i].value;
 		}
 	}
  	
  	//	get id
  	switch (roleValue) {
  		case 'user':
  			roleID = document.getElementById('userID').value;
  			break;
  		default:
  			roleID = document.getElementById('categoryNameID').value;
  			break;
  	}  	
  	ruleData+="&roleID="+roleID;
	$.ajax({
      	url: "modules/ajax/saveCustomizedRuleList.php",      		
      	type: "POST",
      	async: false,
      	data: ruleData,      			
      	dataType: "html",
      	success: function (response) 
      		{   
      			Popup.hide('ruleListModal');										
      		}        		   			   	
		});
}
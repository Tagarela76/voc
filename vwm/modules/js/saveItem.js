function saveDepartmentDetails() 
{	
	Popup.showModal('pleaseWait');	//	Show popup wait
	
	var dep_name=$('#departmentName').attr('value');
	var dep_voc_limit=$('#departmentLimit').attr('value');
	var dep_annual_limit=$('#departmentAnnualLimit').attr('value');
	var dep_action=$('input:hidden[name=action]').attr('value');
	if (dep_action == 'addItem') 
	{		
		var id=$('input:hidden[name=facility_id]').attr('value');
	} 
	if (dep_action == 'edit') 
	{		
		var id=$('input:hidden[name=id]').attr('value');
	} 		
	$.ajax({
      	url: "modules/ajax/saveDepartment.php",      		
      	type: "POST",
      	async: false,
      	data: { "name":dep_name,"voc_limit":dep_voc_limit,"voc_annual_limit":dep_annual_limit,"action":dep_action,"id":id},      			
      	dataType: "html",
      	success: function (response) 
      		{   
      			jsonResponse=eval("("+response+")");		      																
      			answer(jsonResponse);										
      		}        		   			   	
		});
}


function saveEquipmentDetails()
{
	var equip_desc=document.addEquipment.equip_desc.value;	
	var permit=document.addEquipment.permit.value;
	var expire_date=document.addEquipment.expire_date.value;
	var daily=document.addEquipment.daily.value;
	var dept_track=document.addEquipment.dept_track.value;
	var facility_track=document.addEquipment.facility_track.value;	
	var action=document.addEquipment.action.value;
	
	var equipmentData="equip_desc="+equip_desc+"&permit="+permit+"&expire_date="+expire_date+"&daily="+daily+"&dept_track="+
			dept_track+"&facility_track="+facility_track+"&action="+action;
	
	//	inventory is module
	if(document.addEquipment.selectInventoryID !== undefined) {
		var selectInventoryID=document.addEquipment.selectInventoryID.value;
		var inventoryDescription=document.addEquipment.inventoryDescription.value;
		equipmentData+="&selectInventoryID="+selectInventoryID+"&inventoryDescription="+inventoryDescription;	
	}	
	
	
	if (document.addEquipment.action.value == 'addItem') {
		var department_id=document.addEquipment.department_id.value;
		equipmentData+="&department_id="+department_id;
	} 	
	if (document.addEquipment.action.value == 'edit') {
		var id=document.addEquipment.id.value;
		var department_id=document.addEquipment.department_id.value;
		equipmentData+="&id="+id+"&department_id="+department_id;
	} 
	
	$.ajax({
      	url: "modules/ajax/saveEquipment.php",      		
      	type: "POST",
      	async: false,
      	data: equipmentData,      			
      	dataType: "html",
      	success: function (response) 
      		{   
      			jsonResponse=eval("("+response+")");		      																
      			answer(jsonResponse);										
      		}        		   			   	
		});
}

function saveFacilityDetails() 
{	
	var epa=document.addFacility.epa.value;
	var voc_limit=document.addFacility.voc_limit.value;
	var monthly_nox_limit=document.addFacility.monthly_nox_limit.value;
	var voc_annual_limit=document.addFacility.voc_annual_limit.value;
	var fac_name=document.addFacility.name.value;
	var address=document.addFacility.address.value;
	var city=document.addFacility.city.value;
	var county=document.addFacility.county.value;
	var selectState=document.addFacility.selectState.value;
	var textState=document.addFacility.textState.value;
	var zip=document.addFacility.zip.value;
	var country=document.addFacility.country.value;
	var phone=document.addFacility.phone.value;
	var fax=document.addFacility.fax.value;
	var email=document.addFacility.email.value;
	var contact=document.addFacility.contact.value;
	var title=document.addFacility.title.value;
	var action=document.addFacility.action.value;
		
	if (action == 'addItem') {		
		var id=document.addFacility.company_id.value;
	} 	
	if (action == 'edit') {
		var id=document.addFacility.id.value;
	}
	var jobberList = $('#jobber_data > input');
	var jobber = [];
	for(var i = 0; i < jobberList.length; i++){
		jobber[i]= jobberList[i].value;
	}


	$.ajax({
      url: "modules/ajax/saveFacility.php",      		
      type: "POST",
      async: false,
      data: { "epa":epa,"voc_limit":voc_limit,"voc_annual_limit":voc_annual_limit, "monthly_nox_limit":monthly_nox_limit,"name":fac_name,"address":address,"city":city,
      		"county":county,"selectState":selectState,"textState":textState,"zip":zip,"country":country,"phone":phone,
      		"fax":fax,"email":email,"contact":contact,"title":title,"jobber[]":jobber,"action":action,"id":id},      			
      dataType: "html",
      success: 	function (response) 
     			{   
      				jsonResponse=eval("("+response+")");
					answer(jsonResponse);										
      			}        		   			   	
	});
	 
}

function answer(jsonResponse) {	
	Popup.hide('pleaseWait');
	var notify;
	
	if (jsonResponse.summary == 'true') {	//	E-e-e! Successfully saved		
		notify = generateNotify('Saved', 'green');
		
		//	department
		if (document.addDepartment != null) {			
			if (document.addDepartment.action.value == 'addItem') {
				//	go to facility list			
				location.href = '?action=browseCategory&category=facility&id='+document.addDepartment.facility_id.value+'&bookmark=department';
			}
		}
		 		 
		//	equipment 		
		if (document.addEquipment != null) {			
			if (document.addEquipment.action.value == 'addItem') {		
				//	go to equipment list			
				location.href = '?action=browseCategory&category=department&id='+document.addEquipment.department_id.value+'&bookmark=equipment';
			}
		} 
		
		//	facility 		
		if (document.addFacility != null) {			
			if (document.addFacility.action.value == 'addItem') {		
				//	go to facility list			
				location.href = '?action=browseCategory&category=company&id='+document.addFacility.company_id.value;
			}
		} 
	} else {	//	Oops, imbecile input				
		notify = generateNotify('Errors on form', 'red');
	}
	
	removeAllChild(document.getElementById('notifyContainer'));	
	document.getElementById('notifyContainer').appendChild(notify);
	
	for (var property in jsonResponse) {
    	if (jsonResponse.hasOwnProperty(property)) {    		
    		var errorElementName = "error_"+property;
    			
    		//	show error labels 
    		if (property != 'summary' && jsonResponse[property] == 'failed') {    				   				
    			document.getElementById(errorElementName).style.display = "block";    				
    			
    		//	item already exist	    				
    		} else if (property != 'summary' && jsonResponse[property] == 'alredyExist') { 
    			errorElementName = errorElementName+"_alredyExist";     				 			
    			document.getElementById(errorElementName).style.display = "block";

    		//	hide error labels 
    		} else if (property != 'summary' && jsonResponse[property] == 'success') {    				
    			document.getElementById(errorElementName).style.display = "none";
    			if (document.getElementById(errorElementName+"_alredyExist") != null) {
    				document.getElementById(errorElementName+"_alredyExist").style.display = "none";
    			}    			
    		}       			
    	}
	}
}
	


	
function generateNotify(text, color) {
	var colorPrefix;
	var colorPrefixTail;
	
	//	generate prefix by color
	switch (color) {
		case 'red':
			colorPrefix = 'o';	//	orange
			colorPrefixTail = 'orange';
			break;
		case 'green':
			colorPrefix = 'gr';	//	green
			colorPrefixTail = 'green';
			break;
		default:
			colorPrefix = 'r';	//	blue
			colorPrefixTail = 'blue';
	}
		
	//	create table
	var table = document.createElement('TABLE');
	table.align = 'center';
	table.cellPadding = '0';
	table.cellSpacing = '0';
	table.className = 'pop_up';
	var tbody = document.createElement('TBODY');	//	TBODY is needed for IE
		
	//	create first row
	var row1 = document.createElement('TR');
	var data1 = document.createElement('TD');
	var divOut = document.createElement('DIV');
	divOut.className = 'bl_'+colorPrefix;
	var divMiddle = document.createElement('DIV');
	divMiddle.className = 'br_'+colorPrefix;
	var divIn = document.createElement('DIV');
	divIn.className = 'tl_'+colorPrefix;
	var divText = document.createElement('DIV');
	divText.className = 'tr_'+colorPrefix;
		
	//	create seond row
	var row2 = document.createElement('TR');
	var data2 = document.createElement('TD');
	data2.className = 'tail_'+colorPrefixTail;
	
	//	build model
	divText.appendChild(document.createTextNode(text));
	divIn.appendChild(divText);
	divMiddle.appendChild(divIn);
	divOut.appendChild(divMiddle);
	data1.appendChild(divOut);
	row1.appendChild(data1);
	row2.appendChild(data2);
		
	tbody.appendChild(row1);
	tbody.appendChild(row2);
	
	table.appendChild(tbody);		
		
	return table;
} 		
	
	
	
	
function removeAllChild(element) {
	if (element.hasChildNodes()) {
   		while (element.childNodes.length >= 1) {
   	    	element.removeChild(element.firstChild);       
   		} 
	}		
}
	



function saveDepartmentDetails() 
{	
	Popup.showModal('pleaseWait');	//	Show popup wait
	
	var dep_name=$('#departmentName').attr('value');
	var dep_voc_limit=$('#departmentLimit').attr('value');
	var dep_annual_limit=$('#departmentAnnualLimit').attr('value');
	var dep_share_wo=$('#share_wo').attr('checked');	
	var dep_action=$('input:hidden[name=action]').attr('value');
	var unittype = $('#unittype').val();
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
		data: {
			"name":dep_name,
			"voc_limit":dep_voc_limit,
			"voc_annual_limit":dep_annual_limit,
			"action":dep_action,
			"share_wo": dep_share_wo ? 1 : 0,
			"id":id,
			"unittype": unittype
		},      			
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
	var model_number=$('#model_number').val();
	var serial_number=$('#serial_number').val();
	equipmentData += "&model_number="+model_number+"&serial_number="+serial_number;
	
	// prepare new filter data
	var equipment_filter_id = "";
	var equipment_filter_type = "";
	var equipment_filter_name = "";
	var equipment_height_size = "";
	var equipment_width_size = "";
	var equipment_length_size = "";
	var equipment_filter_quantity = "";
	checkboxes = $("#filterContentDiv").find("input[type='checkbox']");

	checkboxes.each(function(i){
		id = this.value; 
		
		equipment_filter_id += id + ",";
		;
		equipment_filter_name+= $("#equipment_filter_name_"+id).val()+",";
		equipment_height_size+= $("#equipment_height_size_"+id).val()+",";
		equipment_width_size+= $("#equipment_width_size_"+id).val()+",";
		equipment_length_size+= $("#equipment_length_size_"+id).val()+",";
		equipment_filter_quantity+= $("#equipment_filter_quantity_"+id).val()+",";
		equipment_filter_type += $("#selectFilterType_"+id+" option:selected").val()+",";
	});
 
	equipmentData += "&equipment_filter_name="+equipment_filter_name;
	equipmentData += "&equipment_height_size="+equipment_height_size;
	equipmentData += "&equipment_width_size="+equipment_width_size;
	equipmentData += "&equipment_length_size="+equipment_length_size;
	equipmentData += "&equipment_filter_quantity="+equipment_filter_quantity;
	equipmentData += "&equipment_filter_type="+equipment_filter_type;
	equipmentData += "&equipment_filter_id="+equipment_filter_id;
	
	var equipment_lighting_id = "";
	var equipment_lighting_name = "";
	var equipment_lighting_size = "";
	var equipment_lighting_voltage = "";
	var equipment_lighting_wattage = "";
	var equipment_lighting_bulb_type = "";
	var equipment_lighting_color = "";
	var equipment_lighting_quantity = "";
        
	checkboxes = $("#lightingContentDiv").find("input[type='checkbox']");

	checkboxes.each(function(i){
		id = this.value;
		equipment_lighting_id += id + ",";
		equipment_lighting_name += $("#equipment_lighting_name_"+id).val()+",";
		equipment_lighting_size += $("#equipment_lighting_size_"+id).val()+",";
		equipment_lighting_voltage += $("#equipment_lighting_voltage_"+id).val()+",";
		equipment_lighting_wattage += $("#equipment_lighting_wattage_"+id).val()+",";
		equipment_lighting_bulb_type += $("#selectBulbType_"+id+" option:selected").val()+",";
		equipment_lighting_color += $("#selectLightingColor_"+id+" option:selected").val()+",";
		equipment_lighting_quantity += $("#equipment_lighting_quantity_"+id).val()+",";
	});

	equipmentData += "&equipment_lighting_name="+equipment_lighting_name;
	equipmentData += "&equipment_lighting_size="+equipment_lighting_size;
	equipmentData += "&equipment_lighting_voltage="+equipment_lighting_voltage;
	equipmentData += "&equipment_lighting_wattage="+equipment_lighting_wattage;
	equipmentData += "&equipment_lighting_bulb_type="+equipment_lighting_bulb_type;
	equipmentData += "&equipment_lighting_color="+equipment_lighting_color;
	equipmentData += "&equipment_lighting_id="+equipment_lighting_id;
	equipmentData += "&equipment_lighting_quantity="+equipment_lighting_quantity;
	
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
	var fac_name=$('#addEditFacilityForm input[name="name"]').val();
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
	var client_facility_id = $('#addEditFacilityForm input[name="client_facility_id"]').val();
	var action=document.addFacility.action.value;
	var unittype = $('#unittype').val();
	
	
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

	$(".error_img").hide();
	
	$.ajax({
		url: "modules/ajax/saveFacility.php",      		
		type: "POST",
		async: false,
		data: {
			"epa":epa,
			"voc_limit":voc_limit,
			"voc_annual_limit":voc_annual_limit, 
			"monthly_nox_limit":monthly_nox_limit,
			"name":fac_name,
			"address":address,
			"city":city,
			"county":county,
			"selectState":selectState,
			"textState":textState,
			"zip":zip,
			"country":country,
			"phone":phone,
			"fax":fax,
			"email":email,
			"contact":contact,
			"title":title,
			"client_facility_id":client_facility_id,
			"jobber[]":jobber,
			"action":action,
			"id":id,
			"unittype":unittype
		},      			
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
			if (document.addEquipment.action.value == 'addItem' || document.addEquipment.action.value == 'edit') {		
				//	go to equipment list			
				location.href = '?action=browseCategory&category=department&id='+document.addEquipment.department_id.value+'&bookmark=equipment';
			}
		} 
	
		//	work order 		
		if (document.addRepairOrder != null) {
			if (document.addRepairOrder.action.value == 'addItem') {		
				//	go to work order list	

				location.href = '?action=browseCategory&category=facility&id='+document.addRepairOrder.facility_id.value+'&bookmark=repairOrder';
			}
			if (document.addRepairOrder.action.value == 'edit') {		
				//	go to work order details	
				location.href = '?action=viewDetails&category=repairOrder&id='+document.addRepairOrder.work_order_id.value+'&facilityID='+document.addRepairOrder.id.value;
			}
		}
		
        //	pfp types		
		if (document.addPfpType != null) {		
            location.href = '?action=browseCategory&category=facility&id='+document.addPfpType.facility_id.value+'&bookmark=pfpTypes';
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
var filter_index = 0;
function addFilter2List() {

	//filter_index = $("#count_equipment_filter").val() + filter_index;
	var equipmentFilterName = $("#equipment_filter_name").val();
	var equipmentHeightSize = $("#equipment_height_size").val();
	var equipmentWidthSize = $("#equipment_width_size").val(); 
	var equipmentLengthSize = $("#equipment_length_size").val();
	var equipmentFilterType = $("#selectFilterType option:selected").val(); 
	var equipmentFilterQuantity = $("#equipment_filter_quantity").val();
		
	var equipment_filter_error = false;
	//checking
	if(equipmentFilterName == "") {
		$("#error_equipment_filter_name .error_text").text("Type filter name!");
		$("#error_equipment_filter_name ").css('display','inline');
		equipment_filter_error = true;
	} else {
		$("#error_equipment_filter_name ").css('display','none');
			
	}	
	if(equipmentHeightSize == "") {
		$("#error_equipment_height_size .error_text").text("Type Height Size!");
		$("#error_equipment_height_size").css('display','inline');
		equipment_filter_error = true;
	} else {
		$("#error_equipment_height_size").css('display','none');
			
	}
	if(/^\d+$/.test(equipmentHeightSize)) {
		$("#error_equipment_height_size").css('display','none');
	} else {
		$("#error_equipment_height_size .error_text").text("Height Size should be an integer!");
		$("#error_equipment_height_size").css('display','inline');
		equipment_filter_error = true;
	}
	if(equipmentWidthSize == "") {
		$("#error_equipment_width_size .error_text").text("Type Width Size!");
		$("#error_equipment_width_size").css('display','inline');
		equipment_filter_error = true;
	} else {
		$("#error_equipment_width_size").css('display','none');
	}
	if(/^\d+$/.test(equipmentWidthSize)) {
		$("#error_equipment_width_size").css('display','none');
	} else {
		$("#error_equipment_width_size .error_text").text("Width Size should be an integer!");
		$("#error_equipment_width_size").css('display','inline');
		equipment_filter_error = true;
	}
	if(equipmentLengthSize == "") {
		$("#error_equipment_length_size .error_text").text("Type Length Size!");
		$("#error_equipment_length_size").css('display','inline');
		equipment_filter_error = true;
	} else {
		$("#error_equipment_length_size").css('display','none');
	}
	if(/^\d+$/.test(equipmentLengthSize)) {
		$("#error_equipment_length_size").css('display','none');
	} else {
		$("#error_equipment_length_size .error_text").text("Length Size should be an integer!");
		$("#error_equipment_length_size").css('display','inline');
		equipment_filter_error = true;
	}
	if(equipmentFilterQuantity == "") {
		$("#error_equipment_filter_quantity .error_text").text("Type Quantity!");
		$("#error_equipment_filter_quantity").css('display','inline');
		equipment_filter_error = true;
	} else {
		$("#error_equipment_filter_quantity").css('display','none');
	}
	if(/^\d+$/.test(equipmentFilterQuantity)) {
		$("#error_equipment_filter_quantity").css('display','none');
	} else {
		$("#error_equipment_filter_quantity .error_text").text("Quantity should be an integer!");
		$("#error_equipment_filter_quantity").css('display','inline');
		equipment_filter_error = true;
	}
	if (equipment_filter_error) {
		return;
	}

	$('#filterContent').append("<tr id=filter_row_temp"+filter_index+" class=border_users_l border_users_b><td  class=border_users_l   width=10% >\n\
                <input type=checkbox id='check_filter_temp"+filter_index+"' value=temp"+filter_index+"></td> \n\
		<td><input type='text' id='equipment_filter_name_temp"+filter_index+"' name='equipment_filter_name_temp"+filter_index+"' value='" +equipmentFilterName+ "'>\n\
			<div id=error_eq_filter_name_temp"+filter_index+" style='width:80px;margin:2px 0px 0px 5px; display:none;' align=left>\n\
				<img src='design/user/img/alert1.gif' height=16  style=float:left;><font style=float:left;vertical-align:bottom;color:red;margin:1px 0px 0px 5px;>Error!</font>\n\
			</div></td>\n\
		<td><input type='text' size='4' id='equipment_height_size_temp"+filter_index+"' name='equipment_height_size_temp"+filter_index+"' value='" +equipmentHeightSize+ "'>\n\
		<div id=error_eq_filter_height_size_temp"+filter_index+" style='width:80px;margin:2px 0px 0px 5px; display:none;' align=left>\n\
				<img src='design/user/img/alert1.gif' height=16  style=float:left;><font style=float:left;vertical-align:bottom;color:red;margin:1px 0px 0px 5px;>Error!</font>\n\
			</div></td>\n\
		<td> <input type='text' size='4' id='equipment_width_size_temp"+filter_index+"' name='equipment_width_size_temp"+filter_index+"' value='" +equipmentWidthSize+ "'>\n\
		<div id=error_eq_filter_width_size_temp"+filter_index+" style='width:80px;margin:2px 0px 0px 5px; display:none;' align=left>\n\
				<img src='design/user/img/alert1.gif' height=16  style=float:left;><font style=float:left;vertical-align:bottom;color:red;margin:1px 0px 0px 5px;>Error!</font>\n\
			</div></td>\n\
		<td><input type='text' size='4' id='equipment_length_size_temp"+filter_index+"' name='equipment_length_size_temp"+filter_index+"' value='" +equipmentLengthSize+ "'>\n\
		<div id=error_eq_filter_length_size_temp"+filter_index+" style='width:80px;margin:2px 0px 0px 5px; display:none;' align=left>\n\
				<img src='design/user/img/alert1.gif' height=16  style=float:left;><font style=float:left;vertical-align:bottom;color:red;margin:1px 0px 0px 5px;>Error!</font>\n\
			</div></td>\n\
		\n\
		<td id=equipmentFilterType_td_temp"+filter_index+"></td>\n\
		<td class=border_users_r><input type='text' size='4' id='equipment_filter_quantity_temp"+filter_index+"' name='equipment_filter_quantity_temp"+filter_index+"' value='" +equipmentFilterQuantity+ "'>\n\
		<div id=error_eq_filter_quantity_temp"+filter_index+" style='width:80px;margin:2px 0px 0px 5px; display:none;' align=left>\n\
				<img src='design/user/img/alert1.gif' height=16  style=float:left;><font style=float:left;vertical-align:bottom;color:red;margin:1px 0px 0px 5px;>Error!</font>\n\
			</div></td>\n\
		\n\
		\n\
</tr>\n\
");
		
	$("#selectFilterType").clone().prependTo("#equipmentFilterType_td_temp"+filter_index);
	$("#equipmentFilterType_td_temp"+filter_index+ " select").attr("id", "selectFilterType_temp"+filter_index);
		
	$("#selectFilterType_temp"+filter_index+" option[value='"+equipmentFilterType+"']").attr('selected', 'selected');
	// clear input fields
	$("#equipment_filter_name").val("");
	$("#equipment_height_size").val("");
	$("#equipment_width_size").val("");
	$("#equipment_length_size").val("");
	$("#equipment_filter_quantity").val("");
		
	$('#filterContentDiv').css('display','table');
	filter_index ++;
}
	
var lighting_index = 0;
function addLighting2List() {
		
	var equipmentLightingName = $("#equipment_lighting_name").val();
	var equipmentBulbType = $("#selectBulbType option:selected").val();
	var equipmentLightingSize = $("#equipment_lighting_size").val(); 
	var equipmentLightingVoltage = $("#equipment_lighting_voltage").val();
	var equipmentLightingWattage = $("#equipment_lighting_wattage").val();
	var equipmentLightingColor = $("#selectLightingColor option:selected").val();
	var equipmentLightingQuantity = $("#equipment_lighting_quantity").val();
	
	var equipment_lighting_error = false;
	//checking
	if(equipmentLightingName == "") {
		$("#error_equipment_lighting_name .error_text").text("Type lighting name!");
		$("#error_equipment_lighting_name ").css('display','inline');
		equipment_lighting_error = true;
	} else {
		$("#error_equipment_lighting_name ").css('display','none');
	} 	
	if(equipmentLightingSize == "") {
		$("#error_equipment_lighting_size .error_text").text("Type Lighting Size!");
		$("#error_equipment_lighting_size").css('display','inline');
		equipment_lighting_error = true;
	} else {
		$("#error_equipment_lighting_size").css('display','none');
	}
		
	if(equipmentLightingVoltage == "") {
		$("#error_equipment_lighting_voltage .error_text").text("Type Lighting Voltage!");
		$("#error_equipment_lighting_voltage").css('display','inline');
		equipment_lighting_error = true;
	} else {
		$("#error_equipment_lighting_voltage").css('display','none');
	}
	if(equipmentLightingWattage == "") {
		$("#error_equipment_lighting_wattage .error_text").text("Type Lighting Wattage!");
		$("#error_equipment_lighting_wattage").css('display','inline');
		equipment_lighting_error = true;
	} else {
		$("#error_equipment_lighting_wattage").css('display','none');
	}
		
	if(/^\d+$/.test(equipmentLightingWattage)) {
		$("#error_equipment_lighting_wattage").css('display','none');
	} else {
		$("#error_equipment_lighting_wattage .error_text").text("Wattage should be an integer");
		$("#error_equipment_lighting_wattage").css('display','inline');
		equipment_lighting_error = true;
	}
	
	if(equipmentLightingQuantity == "") {
		$("#error_equipment_lighting_quantity .error_text").text("Type Lighting Quantity!");
		$("#error_equipment_lighting_quantity").css('display','inline');
		equipment_lighting_error = true;
	} else {
		$("#error_equipment_lighting_quantity").css('display','none');
	}
		
	if(/^\d+$/.test(equipmentLightingQuantity)) {
		$("#error_equipment_lighting_quantity").css('display','none');
	} else {
		$("#error_equipment_lighting_quantity .error_text").text("Quantity should be an integer");
		$("#error_equipment_lighting_quantity").css('display','inline');
		equipment_lighting_error = true;
	}
		
	if (equipment_lighting_error) {
		return;
	}
		
	$('#lightingContent').append("<tr id=lighting_row_temp"+lighting_index+" class=border_users_l border_users_b><td  class=border_users_l  width=10% >\n\
<input type=checkbox id='check_lighting_temp"+lighting_index+"' value=temp"+lighting_index+"></td>\n\
 <td><input type='text' id='equipment_lighting_name_temp"+lighting_index+"' name='equipment_lighting_name_temp"+lighting_index+"' value='" +equipmentLightingName+ "'>\n\
<div id=error_eq_lighting_name_temp"+lighting_index+" style='width:80px;margin:2px 0px 0px 5px; display:none;' align=left>\n\
	<img src='design/user/img/alert1.gif' height=16  style=float:left;><font style=float:left;vertical-align:bottom;color:red;margin:1px 0px 0px 5px;>Error!</font>\n\
</div></td>\n\
 <td id=equipmentBulbType_td_temp"+lighting_index+"></td>\n\
 <td> <input type='text' size='4' id='equipment_lighting_size_temp"+lighting_index+"' name='equipment_lighting_size_temp"+lighting_index+"' value='" +equipmentLightingSize+ "'>\n\
<div id=error_eq_lighting_size_temp"+lighting_index+" style='width:80px;margin:2px 0px 0px 5px; display:none;' align=left>\n\
	<img src='design/user/img/alert1.gif' height=16  style=float:left;><font style=float:left;vertical-align:bottom;color:red;margin:1px 0px 0px 5px;>Error!</font>\n\
</div></td>\n\
 <td><input type='text' size='4' id='equipment_lighting_voltage_temp"+lighting_index+"' name='equipment_lighting_voltage_temp"+lighting_index+"' value='" +equipmentLightingVoltage+ "'>\n\
<div id=error_eq_lighting_voltage_temp"+lighting_index+" style='width:80px;margin:2px 0px 0px 5px; display:none;' align=left>\n\
	<img src='design/user/img/alert1.gif' height=16  style=float:left;><font style=float:left;vertical-align:bottom;color:red;margin:1px 0px 0px 5px;>Error!</font>\n\
</div></td>\n\
 <td> <input type='text' size='4' id='equipment_lighting_wattage_temp"+lighting_index+"' name='equipment_lighting_wattage_temp"+lighting_index+"' value='" +equipmentLightingWattage+ "'>\n\
<div id=error_eq_lighting_wattage_temp"+lighting_index+" style='width:80px;margin:2px 0px 0px 5px; display:none;' align=left>\n\
	<img src='design/user/img/alert1.gif' height=16  style=float:left;><font style=float:left;vertical-align:bottom;color:red;margin:1px 0px 0px 5px;>Error!</font>\n\
</div></td>\n\
 <td class=border_users_r id=equipmentLightingColor_td_temp"+lighting_index+"></td>\n\
\n\
<td> <input type='text' size='4' id='equipment_lighting_quantity_temp"+lighting_index+"' name='equipment_lighting_quantity_temp"+lighting_index+"' value='" +equipmentLightingQuantity+ "'>\n\
<div id=error_eq_lighting_quantity_temp"+lighting_index+" style='width:80px;margin:2px 0px 0px 5px; display:none;' align=left>\n\
	<img src='design/user/img/alert1.gif' height=16  style=float:left;><font style=float:left;vertical-align:bottom;color:red;margin:1px 0px 0px 5px;>Error!</font>\n\
</div></td>\n\
</tr>");
	$("#selectBulbType").clone().prependTo("#equipmentBulbType_td_temp"+lighting_index);
	$("#equipmentBulbType_td_temp"+lighting_index+ " select").attr("id", "selectBulbType_temp"+lighting_index);
		
	$("#selectBulbType_temp"+lighting_index+" option[value='"+equipmentBulbType+"']").attr('selected', 'selected');
		
	// lighting color
	$("#selectLightingColor").clone().prependTo("#equipmentLightingColor_td_temp"+lighting_index);
	$("#equipmentLightingColor_td_temp"+lighting_index+ " select").attr("id", "selectLightingColor_temp"+lighting_index);
		
	$("#selectLightingColor_temp"+lighting_index+" option[value='"+equipmentLightingColor+"']").attr('selected', 'selected');
		
	// clear input fields
	$("#equipment_lighting_name").val("");
	$("#equipment_lighting_size").val("");
	$("#equipment_lighting_voltage").val("");
	$("#equipment_lighting_wattage").val("");
	$("#equipment_lighting_quantity").val("");
	
	$('#lightingContentDiv').css('display','table');
	lighting_index ++;

}
        
function selectAllLightings(select) {

	checkboxes = $("#lightingContentDiv").find("input[type='checkbox']");
	checkboxes.each(function(i){
		this.checked = select;
	});
}
        
function selectAllFilters(select) {

	checkboxes = $("#filterContentDiv").find("input[type='checkbox']");
	checkboxes.each(function(i){
		this.checked = select;
	});
}
        
function clearSelectedFilters() { 

	checkboxes = $("#filterContentDiv").find("input[type='checkbox']");
	var rowsToRemove = new Array();

	checkboxes.each(function(i){
		id = this.value;
		if(this.checked) {
			rowsToRemove.push(id);
            $("#filter_row_" + id).remove();
		}
	});
 
	$.ajax({
		url: "modules/ajax/removeEquipmentProperties.php",      		
		type: "POST",
		async: false,
		data: {
			"property": "filter", 
			"rowsToRemove":rowsToRemove
		},      			
		dataType: "html"        		   			   	
	});
}
        
function clearSelectedLightings() {

	var keyVar, checkboxes;
	checkboxes = $("#lightingContentDiv").find("input[type='checkbox']");
	var rowsToRemove = new Array();

	checkboxes.each(function(i){
		var id = this.value;
		if(this.checked) {
			rowsToRemove.push(id);
            $("#lighting_row_" + id).remove();
		}
	});

	$.ajax({
		url: "modules/ajax/removeEquipmentProperties.php",      		
		type: "POST",
		async: false,
		data: {
			"property": "lighting", 
			"rowsToRemove":rowsToRemove
		},      			
		dataType: "html"        		   			   	
	});
}

function saveRepairOrderDetails() 
{	
	Popup.showModal('pleaseWait');	//	Show popup wait
	
	var work_order_number=$('#repairOrderNumber').attr('value');
	var work_order_description=$('#repairOrderDescription').val();
	var work_order_customer_name=$('#repairOrderCustomerName').attr('value');
	var work_order_action=$('input:hidden[name=action]').attr('value');
	var work_order_status=$('#repairOrderStatus').attr('value');
	var work_order_id = $('#work_order_id').attr('value'); 
	var work_order_vin=$('#repairOrderVin').attr('value'); 
    var woDepartments_id=$('#woDepartments_id').attr('value'); 
	if (work_order_action == 'addItem') 
	{		
		var id=$('input:hidden[name=facility_id]').attr('value');
	} 
	if (work_order_action == 'edit') 
	{		
		var id=$('input:hidden[name=id]').attr('value');
	} 
	
	
	$.ajax({
		url: "modules/ajax/saveRepairOrder.php",      		
		type: "POST",
		async: false,
		data: {
			"work_order_number":work_order_number,
			"work_order_description":work_order_description,
			"work_order_customer_name":work_order_customer_name,
			"id":id,
			"work_order_status":work_order_status,
			"action":work_order_action,
			"work_order_id":work_order_id,
			"work_order_vin":work_order_vin,
            "woDepartments_id":woDepartments_id
		},      			
		dataType: "html",
		success: function (response) 
		{   
			jsonResponse=eval("("+response+")");		      																
			answer(jsonResponse);										
		}        		   			   	
	});
}

function savePfpTypesDetails() {	
	Popup.showModal('pleaseWait');	//	Show popup wait
	var pfpTypeName = $('#pfpTypeName').attr('value'); 
    var id=$('input:hidden[name=facility_id]').attr('value');
	var departmentsId = $('#pfpDepartments_id').val();
	var pfpId = $('#pfpId').val();

	$.ajax({
		url: "modules/ajax/savePfpTypes.php",      		
		type: "POST",
		async: false,
		data: {
			"pfpTypeName":pfpTypeName,
			"id":id,
			"departmentsId":departmentsId,
			'pfpId':pfpId
		},      			
		dataType: "html",
		success: function (response) 
		{   
			jsonResponse=eval("("+response+")");
			answer(jsonResponse);										
		}        		   			   	
	});
}
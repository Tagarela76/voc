var countWasteStreams=0;


/*$.ajax({
      		url: "modules/ajax/wasteStreams.php",      		
      		type: "POST",
      		data: { "action":"selectWasteStreams"},      			
      		dataType: "html",      
      		async:false,		
      		success: function (wasteStreamList) {   
      			wasteStreams=eval("("+wasteStreamList+")");      			     			     																				
      		}      		   			   	
		});	
		
$.ajax({
      		url: "modules/ajax/wasteStreams.php",      		
      		type: "POST",      		
      		data: { "action":"WasteStreamToPollutionList"},      			
      		dataType: "html",
      		success: function (wasteStreamToPollutionList) {   
      			pollutionList=eval("("+wasteStreamToPollutionList+")");   		      												
				
				////	hide preloader
				//$("#preloader").css("display", "none");											
      		}        		   			   	
		});*/

var wasteStreamsCollection = new CWasteStreamCollection();


		
function unitTypeListWithoutPollutions(id)
{	
	
	var unittypeList= new Array();
	
	var selectedClassValue=$('select[name=selectWasteUnittypeClassWithoutPollutions_'+id+'] option:selected').attr('value');
		
	$.ajax({
      		url: "modules/ajax/wasteStreams.php",      		
      		type: "POST",
      		async: false,
      		data: {"action":"unittypeList","companyId":companyId,"companyEx":companyEx ,"selectedClassValue":selectedClassValue},      			
      		dataType: "html",
      		success: function (UTList) {      		
      			
      			unittypeList=eval("("+UTList+")");   		      												
				$('select[name=selectWasteUnittypeWithoutPollutions_'+id+'] option').remove();
				
				for (var i=0;i<unittypeList.length;i++)
				{
					$('select[name=selectWasteUnittypeWithoutPollutions_'+id+']').append(
					"<option value='"+unittypeList[i]['unittype_id']+"'>"+unittypeList[i]['name']+"</option>");					
				}
				////	hide preloader
				//$("#preloader").css("display","none");	 main_block										
      		}        		   			   	
		});
}

function unitTypeList(id,idPollution)
{	
	var list= new Array();
	
	var selectedClassValue=$('select[name=selectWasteUnittypeClass_'+id+'_'+idPollution+'] option:selected').attr('value');
				
	$.ajax({
      		url: "modules/ajax/wasteStreams.php",      		
      		type: "POST",  
      		async: false,    		
      		data: {"action":"unittypeList","companyId":companyId,"companyEx":companyEx ,"selectedClassValue":selectedClassValue},      			
      		dataType: "html",
      		success: function (UTListForPollutions) {   
      			list=eval("("+UTListForPollutions+")");   		      												
				$('select[name=selectWasteUnittype_'+id+'_'+idPollution+'] option').remove();
				
				for (var i=0;i<list.length;i++)
				{
					$('select[name=selectWasteUnittype_'+id+'_'+idPollution+']').append(
					"<option value='"+list[i]['unittype_id']+"'>"+list[i]['name']+"</option>");					
				}	
      		}        		   			   	
		});
}

function updatePollutionsUnittype(wasteId, pollutionId) {
	
	val = $("#selectWasteUnittype_"+wasteId+"_"+pollutionId+" option:selected").val();
	
	waste = wasteStreamsCollection.getWaste(wasteId);
	
	if(waste != false) {
		
		pollution = waste.getPollution(pollutionId);
		
		if(pollution != false) {
			pollution.setUnittypeId(val);
		}
	}
}

function removePollution(wasteId, pollutionId) {
	
}
	
	
function selectOptions2UnitTypeClasses()
{
	var strOptions;/*="<option value='%'>%</option>";*/
	
	for (var i=0;i<unitTypeClasses.length;i++)
		{
			var className;			
			
			switch(unitTypeClasses[i])
			{
				case 'USALiquid':
					className='USA liquid';
					break;
				case 'USADry':
					className='USA dry';
					break;
				case 'USAWght':
					className='USA weight';
					break;
				case 'MetricVlm':
					className='Metric volume';
					break;
				case 'MetricWght':
					className='Metric weight';
					break;
			}
			strOptions+=
				"<option value='"+unitTypeClasses[i]+"'>"+className+"</option>";			
		}	
		return strOptions;
}	
		
function viewWasteStreams()
{		
	
	//alert(wasteStreamsCollection);
	countWasteStreams++;
	var lengthOfwasteStrims=0;
	var isHiddenAddPollution=false;
	
	//я в ужасе wasteStreamsWithPollutions.length не работает
	for (var key in wasteStreamsWithPollutions)
	{
		lengthOfwasteStrims++;
	}
	
	if (countWasteStreams==lengthOfwasteStrims)
	{
		$('a[id=addWasteStream]').css('display','none');
	}		
		
		var wsPosition = document.getElementById("wasteStreamCount").value;	
		//var wsPosition2 = $("#wasteStreamCount").val();
		//alert(wsPosition + " and " + wsPosition2);
		var strNewWasteStreams=
		"<table  class='users' cellpadding='0' cellspacing='0' align='center' id='wasteStreamTable_"+wsPosition+"'>"+		
		"<input type='hidden'  name='pollutionCount_"+wsPosition+"' value=0>"+
		"<tr>"+		
		"<td class='border_users_l border_users_b border_users_r' width='30%' height='20'>" +
		"Waste: <a href='#' id='deleteWasteStream_"+wsPosition+"' onCl='"+wsPosition+"'>Delete</a> <div id='divLine_"+wsPosition+"' style='display:inline; zoom:1;'>/</div> " +
				"<a id='addPollutions_"+wsPosition+"' onCl='"+wsPosition+"'>add Pollution</a>"+
		"</td>"+
		"<td id='wasteStreamTd_"+wsPosition+"' class='border_users_r border_users_b'>"+
		"<input type='hidden' id='selectedValue_"+wsPosition+"' value='noneS' txt='noneS'>"+
		"	<select  name='wasteStreamSelect_"+wsPosition+"' id='wasteStreamSelect_"+wsPosition+"' onCh='"+wsPosition+"' >";			
		
		var firstKey;
		var firstStep=true;
		for (var key in wasteStreamsWithPollutions)
		{				
			var checkValue=true;
			   $('select[name^=wasteStreamSelect_]').each(function(el)
		 		{	
		 			if($(this).attr('value')==key)
		 			checkValue=false;
		 		});		 		
		 		if (checkValue==true)
		 		{		 			
					strNewWasteStreams+=
					"<option value='"+key+"'>"+wasteStreamsWithPollutions[key]['name']+"</option>";					
					
					//Remove the same current option of all wastesStreams
					if (firstStep==true)
					{
						firstKey=key;
						$('select[name^=wasteStreamSelect_]').each(function(el)
						{																								
							$(this).find('option[value='+key+']').remove();										
						});
						
						if (wasteStreamsWithPollutions[key][0]=='false')
						{
							isHiddenAddPollution=true;							
						}
					}
					firstStep=false;		
		 		}	
		 		
		}		
		
		strNewWasteStreams+=																		
		"	</select>"+
		" <div id =quantityText_"+wsPosition+" style='display:none;' >Quantity:</div><input type='text' name = 'quantityWithoutPollutions_"+wsPosition+"' disabled ='true' style='display:none;' onblur='wasteSetQuantity(this);calculateVOC();'>"+
		"	<select name='selectWasteUnittypeClassWithoutPollutions_"+wsPosition+"'onchange='wasteSetUnittypeClass($(this).get());calculateVOC();'  onCh='"+wsPosition+"' disabled='true' style='display:none;'  >";
		
		strNewWasteStreams+=selectOptions2UnitTypeClasses();
		
		strNewWasteStreams+=																				
		"	</select>"+		
		"	<select name='selectWasteUnittypeWithoutPollutions_"+wsPosition+"' onCh='"+wsPosition+"' disabled='true'  style='display:none;' onchange='wasteSetUnittypeId($(this).get()); calculateVOC();' >"+																				
		"	</select>"+		
		"	<span style='display:inline-block'>Storage:<select name='selectStorage_"+wsPosition+"' id='selectStorage_"+wsPosition+"'  onCh='"+wsPosition+"'  class='addInventory'></span>"+																				
		"	</select>"+																					
		"</td>"+				
		"</tr>"+		
		"</table>";
		
		$('div[id=wasteStreamDiv]').append(								
			strNewWasteStreams
		);		
		unitTypeListWithoutPollutions(wsPosition);
		
		wasteObj = new wasteStreamsObj();
		/*Current selected waste's wasteStreamId*/
		var curWasteStreamId = $("#wasteStreamSelect_"+wsPosition + " option:selected").val();
		wasteObj.setWasteStreamId(curWasteStreamId);
		
		
		$('#selectedValue_'+wsPosition).attr('value',firstKey);
		$('#selectedValue_'+wsPosition).attr('txt',wasteStreamsWithPollutions[firstKey]['name']);
		
		if (isHiddenAddPollution)
		{
			$("#quantityText_"+wsPosition).css('display','inline').css('zoom',1);		
			$("input:text[name=quantityWithoutPollutions_"+wsPosition+"]").css('display','inline').css('zoom',1).attr('disabled',false).attr('id',"quantityWithoutPollutions_"+wsPosition).attr("onCh",wsPosition);
			$("select[name=selectWasteUnittypeClassWithoutPollutions_"+wsPosition+"]").css('display','inline').css('zoom',1).attr('disabled',false);//.attr('id',"selectWasteUnittypeClassWithoutPollutions_"+wsPosition).attr("onCh",wsPosition);
			$("select[name=selectWasteUnittypeWithoutPollutions_"+wsPosition+"]").css('display','inline').css('zoom',1).attr('disabled',false).attr('id',"selectWasteUnittypeWithoutPollutions_"+wsPosition).attr("onCh",wsPosition);			
			$('a[id=addPollutions_'+wsPosition+']').css('display','none');
			$('#divLine_'+wsPosition).css('display','none');
			
			$("#quantityWithoutPollutions_"+wsPosition).numeric();
			wasteObj.setQuantity(0);
			wasteObj.setUnittypeId($("select[name=selectWasteUnittypeWithoutPollutions_"+wsPosition+"] option:selected").val());
			
			wasteObj.setPollutionsDisabled(true);
		}
		
		setStorages(wsPosition);
		
		
		
		var curStorageId = $("#selectStorage_"+wsPosition + " option:selected").val();
		wasteObj.setStorageId(curStorageId);
		//alert("ololo! " + wasteStreamsCollection);
		wasteStreamsCollection.addWasteStream(wasteObj);
		//alert("ololo!");
		//wasteStreamsCollection.toJson();
		
		$('#deleteWasteStream_'+wsPosition).click(function (){
			var val = parseInt ($(this).attr('onCl'));			
			delWasteStream(val);
			calculateVOC();
			return false;
		});
		$('#addPollutions_'+wsPosition).click(function(){
			
			var val = parseInt($(this).attr('onCl'));
			
			addPollution (val);
			return false;
		});
		$('select[name=wasteStreamSelect_'+wsPosition+']').change(function(){
			var val = parseInt($(this).attr('onCh'));
			viewRowsBySelectWaste(val);
			setStorages(val);
			
			
			var value =  $(this).val();
			
			wasteStreamsCollection.setWasteStreamId(value, val);
			
			var storageId = $("#selectStorage_"+val+" option:selected").val();
			wasteStreamsCollection.setStorageId(storageId, val);
			
			
			if(isWasteWithPollutions(val)) {
				
				//clean pollution
				wasteStreamsCollection.setUnittypeId(undefined, val);
				//$("#quantityWithoutPollutions_"+wsPosition).numeric();
				//alert("with");
				wasteStreamsCollection.getWaste(val).setPollutionsDisabled(false);
				
			} else {
				wasteStreamsCollection.getWaste(val).setPollutionsDisabled(true);
				//clean pollutions, init one pollution
				id = "input:text[name=quantityWithoutPollutions_"+val+"]";
				
				//Quantity textbox
				$("input:text[name=quantityWithoutPollutions_"+val+"]").numeric();
				$("input:text[name=quantityWithoutPollutions_"+val+"]").attr("onCh",val);
				
				//$("input:text[name=quantityWithoutPollutions_"+val+"]").attr("onchange","wasteSetQuantity(this)");
				$("input:text[name=quantityWithoutPollutions_"+val+"]").change( function() {
					wasteSetQuantity($(this).get());
				});
				//unittype select
				
				/*$("#selectStorage_"+wsPosition).change( { "index" : index} ,function(eventObject) {
					wasteSetStorage(eventObject.data.index);
				});
				
				//$("#selectWasteUnittypeWithoutPollutions_"+wsPosition).attr("onchange","wasteSetUnittypeId(this);");
				$("#selectWasteUnittypeWithoutPollutions_"+wsPosition).change( function() {

					wasteSetUnittypeId($(this).get());
				});*/
				
				//$('select[name=selectWasteUnittypeClassWithoutPollutions_'+val+']').attr("onchange","wasteSetUnittypeClass(this);");
				
				//Init unittype
				wasteSetUnittypeClass($('select[name=selectWasteUnittypeClassWithoutPollutions_'+val+']').get());
				$('select[name=selectWasteUnittypeClassWithoutPollutions_'+val+']').change( function() {

					wasteSetUnittypeClass($(this).get());
				});
				$("#selectWasteUnittypeWithoutPollutions_"+val).attr("onCh",val);
				$("#selectWasteUnittypeWithoutPollutions_"+val).change( function() {

					wasteSetUnittypeId($(this).get());
					
				});
				
				/*$('select[name=selectWasteUnittypeClassWithoutPollutions_'+val+']').change(function(){
					var onCh = parseInt($(this).attr('onCh'));
					unitTypeListWithoutPollutions(onCh);
					
					var value = $("#selectWasteUnittypeWithoutPollutions_"+onCh+" option:selected").val();
					
					wasteStreamsCollection.setUnittypeId(  value, onCh);
					//wasteStreamsCollection.toJson();
					//alert('change unittype ' + value + " to " + onCh);
				});*/
			}
			
			//wasteStreamsCollection.toJson();
			
			});
		//$('select[name=selectWasteUnittypeClassWithoutPollutions_'+wsPosition+']').attr("onchange","wasteSetUnittypeClass(this);");
		$('select[name=selectWasteUnittypeClassWithoutPollutions_'+wsPosition+']').change( function() {

			wasteSetUnittypeClass($(this).get());
		});
		/*$('select[name=selectWasteUnittypeClassWithoutPollutions_'+wsPosition+']').change(function(){
			var val = parseInt($(this).attr('onCh'));
			unitTypeListWithoutPollutions(val);
			
			var value = $("#selectWasteUnittypeWithoutPollutions_"+val+" option:selected").val();
			
			wasteStreamsCollection.setUnittypeId(value, val);
			//wasteStreamsCollection.toJson();
			});*/
		
		index = $("#selectStorage_"+wsPosition).attr('onCh');
		//$("#selectStorage_"+wsPosition).attr("onchange","wasteSetStorage("+index+");");
		
		$("#selectStorage_"+wsPosition).change( {"index" : index} ,function(eventObject) {
			wasteSetStorage(eventObject.data.index);
		});
		
		//$("#selectWasteUnittypeWithoutPollutions_"+wsPosition).attr("onchange","wasteSetUnittypeId(this);");
	    $("#selectWasteUnittypeWithoutPollutions_"+wsPosition).change( function() {

			wasteSetUnittypeId($(this).get());
		});

		
		//$("#quantityWithoutPollutions_"+wsPosition).attr("onchange","wasteSetQuantity(this);");		
		$("#quantityWithoutPollutions_"+wsPosition).change( function() {

			wasteSetQuantity($(this).get());
	
		});
		
		document.getElementById("wasteStreamCount").value++;		
}

function wasteSetUnittypeId(element) {

	var index = parseInt($(element).attr('onCh'));
	var value = $(element).attr('value');
		
	wasteStreamsCollection.setUnittypeId(value, index);
}
function wasteSetUnittypeClass(element) {
	
	var onCh = parseInt($(element).attr('onCh'));
	unitTypeListWithoutPollutions(onCh);
	
	var value = $("#selectWasteUnittypeWithoutPollutions_"+onCh+" option:selected").val();
	
	wasteStreamsCollection.setUnittypeId(value, onCh);
}

function wasteSetQuantity(element) {	
	var index = parseInt($(element).attr('onCh'));
	var value = $(element).val();
	//alert("index: " + index + " value:" + value);
	wasteStreamsCollection.setQuantity(value, index);
}

function wasteSetStorage(index) {
	
	var value = $("#selectStorage_"+index+" option:selected").val();
	wasteStreamsCollection.setStorageId(value, index);
}

$(function()
{			
	var overflow = 	new Array();
	if (storageOverflow!='false')
	{	
		overflow=eval("("+storageOverflow+")");
	}
		
	var elemsArray = new Array();	
	if (review!='false')
	{			
		elemsArray=eval("("+review+")");				
	}
	
	var deletedStorageValidation=new Array();
	if(deletedStorageValidationString!='false')
	{
		deletedStorageValidation=eval ("("+deletedStorageValidationString+")");
	}
	
	
	
	for (var key in elemsArray)
	{					
		viewWasteStreams();
			
		var value=elemsArray[key]['id'];
		
		$('select[name = wasteStreamSelect_'+key+'] option[value ='+value+']').attr('selected',true);
		setStorages(key);				
		$('select[name = selectStorage_'+key+'] option[value ='+elemsArray[key]['storage_id']+']').attr('selected',true);
		
		wasteStreamsCollection.getWaste(key).setStorageId(elemsArray[key]['storage_id']);
		
		if (elemsArray[key]['unittypeClass']!=null)
		{
		
			$('input[name=quantityWithoutPollutions_'+key+']').attr('value',elemsArray[key]["value"]).attr("onchange","wasteSetQuantity(this)").attr("onch",key);
			$('select[name = selectWasteUnittypeClassWithoutPollutions_'+key+'] option[value ='+elemsArray[key]["unittypeClass"]+']').attr('selected',true);
			unitTypeListWithoutPollutions(key);
			$('select[name = selectWasteUnittypeWithoutPollutions_'+key+'] option[value ='+elemsArray[key]["unittypeID"]+']').attr('selected',true);
			
			waste = wasteStreamsCollection.getWaste(key);
			waste.setQuantity(elemsArray[key]["value"]);
			waste.setUnittypeId(elemsArray[key]["unittypeID"]);
			waste.setWasteStreamId(value);
		}		
		viewRowsBySelectWaste(key);		
		
		if (elemsArray[key]["count"]!=null)
		{
			for (var i=0;i<elemsArray[key]["count"];i++)
			{
				addPollution(key);
				$('select[name = selectPollution_'+key+'_'+i+'] option[value ='+elemsArray[key][i]["id"]+']').attr('selected',true);		
				
				$('select[name=selectWasteUnittypeClass_'+key+'_'+i+'] option[value ='+elemsArray[key][i]["unittypeClass"]+']').attr('selected',true);
				unitTypeList(key,i);
				$('select[name=selectWasteUnittype_'+key+'_'+i+'] option[value ='+elemsArray[key][i]["unittypeID"]+']').attr('selected',true);
				$('input[name=quantity_'+key+'_'+i+']').attr('value',elemsArray[key][i]["value"]);
				if(elemsArray[key][i]['validation']!='success' && elemsArray[key][i]['validation']!=null)
				{
					$('#pollution_'+key+"_"+i+' td[id=pollutionValue]').append('<p style="color:red">'+elemsArray[key][i]["validation"]+'</p>');
				}
				
				pol = wasteStreamsCollection.getWaste(key).getPollution(i);
				pol.setQuantity(elemsArray[key][i]["value"]);
				pol.setPollutionId(elemsArray[key][i]["id"]);
				pol.setUnittypeId(elemsArray[key][i]["unittypeID"]);
			}
		}
		if(elemsArray[key]['validation']!='success' && elemsArray[key]['validation']!=null)
		{
			$('#wasteStreamTd_'+key).append('<p style="color:red">'+elemsArray[key]["validation"]+'</p>');
		}	
		if(deletedStorageValidationString!='false')
		{	
			if (deletedStorageValidation[key]!="false")
			{
				$('#wasteStreamTd_'+key).append('<p style="color:red">'+deletedStorageValidation[key]+'</p>');
			}
		}
	}		
	//$('a[id=addWasteStream]').bind('click',function(){viewWasteStreams();});
	//$('a[id=addWasteStream]').live('click',function(){viewWasteStreams();});
	
	$('select[name^=selectStorage]').each(function(el)
	{
		for (key in overflow)
		{
			if ($(this).attr('value')==overflow[key])
				{
					$(this).after('<p style="color:red; display:inline-block"> Storage overflow!</p>');
				}
				 
		}	
	});		
})

function setStorages(id)
{

	var idSelectedWasteStream = $('select[name=wasteStreamSelect_'+id+']').attr('value');
	
	//SET STORAGES
	$("select[name=selectStorage_"+id+"]").empty();
	var strOptions= "<option value='-1'>none</option>";	
	
	for (key in storages[idSelectedWasteStream])
	{
		var unittype=storages[idSelectedWasteStream][key]['volume_unittype'];
		var empty=storages[idSelectedWasteStream][key]['capacity_volume']-storages[idSelectedWasteStream][key]['current_usage'];
		strOptions+="<option value="+storages[idSelectedWasteStream][key]['storage_id']+">"+storages[idSelectedWasteStream][key]['name']+" (empty: "+empty.toFixed(2)+" "+unittype+")</option>";
	}	
	$("select[name=selectStorage_"+id+"]").append(strOptions);	
}

function isWasteWithPollutions(index) {
	
	var idSelectedWasteStream = $('select[name=wasteStreamSelect_'+index+']').attr('value');	
	
	if (wasteStreamsWithPollutions[idSelectedWasteStream][0]=='false') {
		return false;
	} else {
		return true;
	}
}

function viewRowsBySelectWaste(id)
{		
	var idSelectedWasteStream = $('select[name=wasteStreamSelect_'+id+']').attr('value');	
	
	if(idSelectedWasteStream == undefined) {
		return;
	}
			
	if (wasteStreamsWithPollutions[idSelectedWasteStream][0]=='false')
	{		
		$("#quantityText_"+id).css('display','inline').css('zoom',1);		
		$("input:text[name=quantityWithoutPollutions_"+id+"]").css('display','inline').css('zoom',1).attr('disabled',false);
		$("select[name=selectWasteUnittypeClassWithoutPollutions_"+id+"]").css('display','inline').css('zoom',1).attr('disabled',false).attr('id',"selectWasteUnittypeClassWithoutPollutions_"+id);
		$("select[name=selectWasteUnittypeWithoutPollutions_"+id+"]").css('display','inline').css('zoom',1).attr('disabled',false).attr('id',"selectWasteUnittypeWithoutPollutions_"+id);			
		$('a[id=addPollutions_'+id+']').css('display','none');
		$('#divLine_'+id).css('display','none');	
	}
	else
	{
		$("#quantityText_"+id).css('display','none');
		$("input:text[name=quantityWithoutPollutions_"+id+"]").css('display','none').attr('disabled',true);
		$("select[name=selectWasteUnittypeClassWithoutPollutions_"+id+"]").css('display','none').attr('disabled',true);
		$("select[name=selectWasteUnittypeWithoutPollutions_"+id+"]").css('display','none').attr('disabled',true);							
		$('a[id=addPollutions_'+id+']').css('display','inline').css('zoom',1);	
		$('#divLine_'+id).css('display','inline').css('zoom',1);		
	}	
	
	$('tr[id^=pollution_'+id+' ]').each(function(el)
	{
		if ($(this).attr('idWasteStream')==idSelectedWasteStream)
		{
			var idPollution=parseInt($('input:hidden[name=pollutionCount_'+id+']').attr('value'));
			idPollution++;
			$('input:hidden[name=pollutionCount_'+id+']').attr('value',idPollution.toString());
			$(this).css("display", "table-row");
			$(this).attr('disabled',false);
		}
		else
		{
			var idPollution=parseInt($('input:hidden[name=pollutionCount_'+id+']').attr('value'));
			idPollution--;
			$('input:hidden[name=pollutionCount_'+id+']').attr('value',idPollution.toString());
			$(this).css("display", "none");
			$(this).attr('disabled',true);
		}
	});		
	
	
	var selectObject=$('select[name=wasteStreamSelect_'+id+']');
	var oldSelectedValueObj = $('#selectedValue_'+id);
	var oldSelectedValue=oldSelectedValueObj.attr('value');
	var oldSelectedText=oldSelectedValueObj.attr('txt');
	var selectedValue=selectObject.attr('value');
	
	if (selectedValue!=oldSelectedValue)
	{
		$('select[name^=wasteStreamSelect_]').each(function(el)
		{					
			if ($(this).attr('name')!='wasteStreamSelect_'+id)
			{													
				$(this).find('[value='+selectedValue+']').remove();										
				$(this).append('<option value='+oldSelectedValue+'>'+oldSelectedText+'</option>');					
			}
		});
	}	
	oldSelectedValueObj.attr('value',selectObject.attr('value'));
	oldSelectedValueObj.attr('txt',selectObject.find('option:selected').text());	
}

function renamePollutions(i,j,need)
{
	$('tr[id=pollution_'+i+'_'+j+']').attr('id','pollution_'+need+'_'+j);
	$('a[id=deletePollution_'+i+'_'+j+']').attr('onCl_ws',need);	
	$('a[id=deletePollution_'+i+'_'+j+']').attr('id','deletePollution_'+need+'_'+j);
	$('select[name=selectPollution_'+i+'_'+j+']').attr('onCh_ws',need);
	$('select[name=selectPollution_'+i+'_'+j+']').attr('name','selectPollution_'+need+'_'+j);	
	$('select[name=selectWasteUnittypeClass_'+i+'_'+j+']').attr('onCh_ws',need);
	$('select[name=selectWasteUnittypeClass_'+i+'_'+j+']').attr('name','selectWasteUnittypeClass_'+need+'_'+j);	
	$('select[name=selectWasteUnittype_'+i+'_'+j+']').attr('name','selectWasteUnittype_'+need+'_'+j);
	$('input[name=quantity_'+i+'_'+j+']').attr('name','quantity_'+need+'_'+j);	
	$('#pollutionSelectedValue_'+i+'_'+j).attr('id','pollutionSelectedValue_'+need+'_'+j);
}

function delWasteStream (id)
{			
	countWasteStreams--;	
	
	wasteStreamsCollection.removeByIndex(id);
	
	var selectedValueObj = $('#selectedValue_'+id);
	var selectedValue=selectedValueObj.attr('value');
	var selectedText=selectedValueObj.attr('txt');	
	
	$('select[name^=wasteStreamSelect_]').each(function(el)
	{			
		if (($(this).attr('name')!='wasteStreamSelect_'+id) )
		{								
			$(this).append('<option value='+selectedValue+'>'+selectedText+'</option>');	
		}				
	});			
	
	$('table[id=wasteStreamTable_'+id+']').remove();
	for (var i=parseInt(id+1);i<countWasteStreams+1;i++)
	{		
		var need=i-1;
		
		var pollutionCount=parseInt($('input:hidden[name=pollutionCount_'+i+']').attr('value'));
		for (var j=0;j<pollutionCount;j++)
		{
			renamePollutions(i,j,need);
		}	
		
		$('table[id=wasteStreamTable_'+i+']').attr('id','wasteStreamTable_'+need);			
		$('input:hidden[name=pollutionCount_'+i+']').attr('name','pollutionCount_'+need);
		$('a[id=deleteWasteStream_'+i+']').attr('onCl',need);	
		$('a[id=deleteWasteStream_'+i+']').attr('id','deleteWasteStream_'+need);			
		$('a[id=addPollutions_'+i+']').attr('onCl',need);
		$('a[id=addPollutions_'+i+']').attr('id','addPollutions_'+need);		
		
		//Waste stream select
		$('select[name = wasteStreamSelect_'+i+']').attr('onCh',need);
		$('select[name = wasteStreamSelect_'+i+']').attr('name','wasteStreamSelect_'+need).attr('id','wasteStreamSelect_'+need);
		
		//Quantity without pollution
		$("input:text[name=quantityWithoutPollutions_"+i+"]").attr('name','quantityWithoutPollutions_'+need).attr("id","quantityWithoutPollutions_"+need);
		$("input:text[name=quantityWithoutPollutions_"+need+"]").attr("onCh",need);
		
		//Unittype's checkboxes
		$("select[name=selectWasteUnittypeClassWithoutPollutions_"+i+"]").attr('onCh',need);
		$("select[name=selectWasteUnittypeClassWithoutPollutions_"+i+"]").attr('name','selectWasteUnittypeClassWithoutPollutions_'+need);		
		$("select[name=selectWasteUnittypeWithoutPollutions_"+i+"]").attr('name','selectWasteUnittypeWithoutPollutions_'+need).attr("id","selectWasteUnittypeWithoutPollutions_"+need).attr("onCh",need);
		
		//Select storage
		$("select[name=selectStorage_"+i+"]").attr('name','selectStorage_'+need).attr("id",'selectStorage_'+need).attr('onCh',need);
		
		$('#divLine_'+i).attr('id','divLine_'+need);
		$('#quantityText_'+i).attr('id','quantityText_'+need);
		$('#selectedValue_'+i).attr('id','selectedValue_'+need);
		$('#wasteStreamTd_'+i).attr('id','wasteStreamTd_'+need);						
	}	
	$('#wasteStreamCount').attr('value',countWasteStreams);	
	$('a[id=addWasteStream]').css('display','inline').css('zoom',1);	
	
}

function delPollution(id,idPollution)
{	
	// for to get rid of the error in addUsage.js line 1209
	var lastPollution=parseInt($('input:hidden[name=pollutionCount_'+id+']').attr('value'));
	if (lastPollution == 1){
		delWasteStream(id);
		//console.log(lastPollution);	
		return;
	}
	// end
	$('#addPollutions_'+id).css('display','inline').css('zoom',1);
	$('#divLine_'+id).css('display','inline').css('zoom',1);	
	
	var selectedValueObj = $('#pollutionSelectedValue_'+id+'_'+idPollution);
	var selectedValue=selectedValueObj.attr('value');
	var selectedText=selectedValueObj.attr('txt');	
	
		
	$('tr[id=pollution_'+id+'_'+idPollution+']').remove();
	
	$('select[name^=selectPollution_'+id+'_]').each(function(el)	
	{									
		$(this).append('<option value='+selectedValue+'>'+selectedText+'</option>');						
	});
	
	var pollutionCount=parseInt($('input:hidden[name=pollutionCount_'+id+']').attr('value'));
	
	for (var j=idPollution+1;j<pollutionCount;j++)
	{ 		
		need=j-1;
		$('tr[id=pollution_'+id+'_'+j+']').attr('id','pollution_'+id+'_'+need);		
		$('a[id=deletePollution_'+id+'_'+j+']').attr('onCl_pol',need);
		$('a[id=deletePollution_'+id+'_'+j+']').attr('id','deletePollution_'+id+'_'+need);
		$('select[name=selectPollution_'+id+'_'+j+']').attr('onCh_pol',need);
		$('select[name=selectPollution_'+id+'_'+j+']').attr('name','selectPollution_'+id+'_'+need);				
		$('select[name=selectWasteUnittypeClass_'+id+'_'+j+']').attr('onCh_pol',need);
		$('select[name=selectWasteUnittypeClass_'+id+'_'+j+']').attr('name','selectWasteUnittypeClass_'+id+'_'+need);		
		$('select[name=selectWasteUnittype_'+id+'_'+j+']').attr('name','selectWasteUnittype_'+id+'_'+need).attr('id','selectWasteUnittype_'+id+'_'+need).attr('onch_pol',need);
		$('input[name=quantity_'+id+'_'+j+']').attr('name','quantity_'+id+'_'+need).attr('id','quantity_'+id+'_'+need).attr("onch_ws",id).attr("onch_pol",need);
		$('#pollutionSelectedValue_'+id+'_'+j).attr('id','pollutionSelectedValue_'+id+'_'+need);
	}		
	
	pollutionCount--;
	$('input:hidden[name=pollutionCount_'+id+']').attr('value',pollutionCount)	
	
	//alert('remove pollution '+ idPollution  +' by wasteId ' + id);
	wasteStreamsCollection.getWaste(id).removePollution(idPollution);
	
	//return false;
}

function selectPollution(id,idPollution)
{		
	var selectObject=$('select[name=selectPollution_'+id+'_'+idPollution+']');
	var oldSelectedValueObj = $('#pollutionSelectedValue_'+id+'_'+idPollution);
	var oldSelectedValue=oldSelectedValueObj.attr('value');
	var oldSelectedText=oldSelectedValueObj.attr('txt');
	var selectedValue=selectObject.attr('value');
	$('select[name^=selectPollution_'+id+'_]').each(function(el)
	{			
		if ($(this).attr('name')!='selectPollution_'+id+'_'+idPollution)
		{							
			$(this).find('option[value='+selectedValue+']').remove();			
			$(this).append('<option value='+oldSelectedValue+'>'+oldSelectedText+'</option>');			
		}
	})	
	oldSelectedValueObj.attr('value',selectObject.attr('value'));
	oldSelectedValueObj.attr('txt',selectObject.find('option:selected').text());
}

function addPollution (id)
{					
		
	var idPollution=parseInt($('input:hidden[name=pollutionCount_'+id+']').attr('value'));
	var idSelectedWasteStream = $('select[name=wasteStreamSelect_'+id+']').attr('value');		
	
	var strNewPollution=	
	"<tr id='pollution_"+id+"_"+idPollution+"' idWasteStream='"+idSelectedWasteStream+"'>"+		
		"<td class='border_users_l border_users_b border_users_r waste' width='30%' height='20' style='padding-left:30px;'>" +
			"Contaminated by: <a href='#' id='deletePollution_"+id+"_"+idPollution+"'  onCl_ws='"+id+"' onCl_pol='"+idPollution+"' >Delete</a>" +
		"</td>"+
		"<td id='pollutionValue' class='border_users_r border_users_b contaminated_by'>"+				
		"	<select name='selectPollution_"+id+"_"+idPollution+"'  onCh_ws='"+id+"' onCh_pol='"+idPollution+"'>";
		var firstStep = true;
		var selVal;
		var countPoll = 0;
		for (key in wasteStreamsWithPollutions[idSelectedWasteStream])
		{			
			if (key!='name')
			{			
				var checkValue=true;
				$('select[name^=selectPollution_'+id+']').each(function(el)
			 	{
			 		
			 		if($(this).attr('value')==key)
			 			checkValue=false;
			 		
			 	})		 		
			 	if (checkValue==true)
			 	{	
			 		countPoll++;	 		
			 		if (firstStep==true)
					{
						firstStep=false;
						selVal=key;
					}		 		
					strNewPollution+=
						"<option value='"+key+"'>"+wasteStreamsWithPollutions[idSelectedWasteStream][key]+"</option>";
			 	}		
			}	
		}									
		if (countPoll==1)
		{
			$('#addPollutions_'+id).css('display','none');
			$('#divLine_'+id).css('display','none');
		}
			
		$('select[name^=selectPollution_'+id+']').each(function(el)
		 {	
		 	$(this).find('option[value='+selVal+']').remove();	 	
		 })		 			
		
		strNewPollution+=																			
		"	</select>"+	
		"	<input type='hidden' id='pollutionSelectedValue_"+id+"_"+idPollution+"' value='"+selVal+"' txt='"+wasteStreamsWithPollutions[idSelectedWasteStream][selVal]+"'>"+				
		" 	Quantity:<input type='text' name = 'quantity_"+id+"_"+idPollution+"' id='quantity_"+id+"_"+idPollution+"' onCh_ws='"+id+"' onCh_pol='"+idPollution+"' onblur='calculateVOC()'>"+
		"	<select name='selectWasteUnittypeClass_"+id+"_"+idPollution+"' onCh_ws='"+id+"' onCh_pol='"+idPollution+"' onchange='change_waste_pollution_unittypeClass($(this).get());calculateVOC();'>";
		
		strNewPollution+=selectOptions2UnitTypeClasses();		
		
		strNewPollution+=																		
		"	</select>"+		
		"	<select name='selectWasteUnittype_"+id+"_"+idPollution+"' id='selectWasteUnittype_"+id+"_"+idPollution+"' onCh_ws='"+id+"' onCh_pol='"+idPollution+"' onchange='change_waste_pollution_unittypeId($(this).get());calculateVOC();'>"+																				
		"	</select>"+																						
		"</td>"+
	"</tr>";	
		
	$('table[id=wasteStreamTable_'+id+']').append(strNewPollution);
	unitTypeList(id,idPollution);
	$('#deletePollution_'+id+'_'+idPollution).click(function (){
			var idWs = parseInt ($(this).attr('onCl_ws'));
			var idPol = parseInt ($(this).attr('onCl_pol'));			
			delPollution(idWs,idPol);
			calculateVOC();
			return false;
	});
	//$('select[name=selectPollution_'+id+'_'+idPollution+']').attr('onchange',"wasteSetPollutionId($(this).attr('onCh_ws'),$(this).attr('onCh_pol'),$(this).attr('value'));");
	$('select[name=selectPollution_'+id+'_'+idPollution+']').change(function(){
			var idWs = parseInt ($(this).attr('onCh_ws'));
			var idPol = parseInt ($(this).attr('onCh_pol'));
			selectPollution(idWs,idPol);
			value =  $("option:selected",this).val();
			//value =  $(this).children("[@selected]").val();
			//wasteStreamsCollection.getWaste(idWs).getPollution(idPol).setPollutionId(value);
			
			wasteSetPollutionId(idWs,idPol,value);
	});
			
	$('select[name=selectWasteUnittypeClass_'+id+'_'+idPollution+']').change(function(){
			//var idWs = parseInt ($(this).attr('onCh_ws'));
			//var idPol = parseInt ($(this).attr('onCh_pol'));
			//unitTypeList(idWs,idPol);
			change_waste_pollution_unittypeClass($(this).get());
			
			
	});
	
	//$("#selectWasteUnittype_"+id+"_"+idPollution).attr("onchange",'change_waste_pollution_unittypeId(this)');
	
	$("#selectWasteUnittype_"+id+"_"+idPollution).change(function(){
		//var idWs = parseInt ($(this).attr('onCh_ws'));
		//var idPol = parseInt ($(this).attr('onCh_pol'));
		//var val = $("#selectWasteUnittype_"+idWs+"_"+idPol +" option:selected").val();
		
		//wasteStreamsCollection.getWaste(idWs).getPollution(idPol).setUnittypeId(val);
		
		change_waste_pollution_unittypeId($(this).get());
	});
	
	/*Select Pollutions*/
	curPollutionId =  $("select[name=selectPollution_"+id+"_"+idPollution+"] option:selected").val();
	
	//alert("select[name=selectPollution_"+id+"_"+idPollution+"]"); 
	
	
	/*Unittype*/
	selectedUnittypeObj = $("#selectWasteUnittype_"+id+"_"+idPollution+" option:selected"); 
	curUnittypeId = selectedUnittypeObj.val();
	
	/*Quantity*/
	//console.log("#quantity_"+id+"_"+idPollution);
	qObj = $("#quantity_"+id+"_"+idPollution); 
	qObj.numeric();
	//qObj.css("color","red");
	
	//qObj.attr('onchange','change_pollution_quantity(this)');
	
	qObj.change(function(){
		//val = $(this).val();
		//wasteId = $(this).attr('onCh_ws');
		//pollutionId = $(this).attr('onCh_pol');
		//alert("onchange wasteId=" + wasteId + " pollutionId="+pollutionId + " value=" + val);
		//wasteStreamsCollection.getWaste(wasteId).getPollution(pollutionId).setQuantity(val);
		
		change_pollution_quantity($(this).get());
	});
	
	curQuantity = qObj.val();
	/*Init pollution Object*/
	pollution = new pollutionObj();
	
	pollution.setPollutionId(curPollutionId);
	pollution.setQuantity(curQuantity);
	pollution.setUnittypeId(curUnittypeId);
	
	//console.log("set new pollution:");
	//console.log(curPollutionId);
	//console.log(curQuantity);
	//console.log(curUnittypeId);
	
	wasteStreamsCollection.addPollutionToWaste(id, pollution);
	
	idPollution++;
	$('input:hidden[name=pollutionCount_'+id+']').attr('value',idPollution.toString());

}

function change_pollution_quantity(el) {
	val = $(el).val();
	wasteId = $(el).attr('onCh_ws');
	pollutionId = $(el).attr('onCh_pol');
	//alert("onchange wasteId=" + wasteId + " pollutionId="+pollutionId + " value=" + val);
	wasteStreamsCollection.getWaste(wasteId).getPollution(pollutionId).setQuantity(val);
}

function wasteSetPollutionId(idWS,idPOL,value) {
	
	selectPollution(idWS,idPOL);
	wasteStreamsCollection.getWaste(idWS).getPollution(idPOL).setPollutionId(value);
}

function change_waste_pollution_unittypeId(el) {
	
	wasteId = $(el).attr("onch_ws");
	pollutionId = $(el).attr("onch_pol");
	value = $("option:selected",el).val();
	
	wasteStreamsCollection.getWaste(wasteId).getPollution(pollutionId).setUnittypeId(value);
}

function change_waste_pollution_unittypeClass(el) {
	
	wasteId = $(el).attr("onch_ws");
	pollutionId = $(el).attr("onch_pol");
	
	unitTypeList(wasteId, pollutionId);
	
	updatePollutionsUnittype(wasteId, pollutionId);
}




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
		
function unitTypeListWithoutPollutions(id)
{	
	var unittypeList= new Array();
	
	var selectedClassValue=$('select[name=selectWasteUnittypeClassWithoutPollutions_'+id+'] option:selected').attr('value');
		
	$.ajax({
      		url: "modules/ajax/wasteStreams.php",      		
      		type: "POST",
      		async: false,
      		data: { "action":"unittypeList","companyId":companyId,"companyEx":companyEx ,"selectedClassValue":selectedClassValue },      			
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
				//$("#preloader").css("display","none");											
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
      		data: { "action":"unittypeList","companyId":companyId,"companyEx":companyEx ,"selectedClassValue":selectedClassValue },      			
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
		"	<select  name='wasteStreamSelect_"+wsPosition+"' onCh='"+wsPosition+"' >";			
		
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
		" <div id =quantityText_"+wsPosition+" style='display:none;' >Quantity:</div><input type='text' name = 'quantityWithoutPollutions_"+wsPosition+"' disabled ='true' style='display:none;'>"+
		"	<select name='selectWasteUnittypeClassWithoutPollutions_"+wsPosition+"' onCh='"+wsPosition+"' disabled='true' style='display:none;'  >";
		
		strNewWasteStreams+=selectOptions2UnitTypeClasses();
		
		strNewWasteStreams+=																				
		"	</select>"+		
		"	<select name='selectWasteUnittypeWithoutPollutions_"+wsPosition+"' disabled='true'  style='display:none;' >"+																				
		"	</select>"+		
		"	<span style='display:inline-block'>Storage:<select name='selectStorage_"+wsPosition+"'  class='addInventory'></span>"+																				
		"	</select>"+																					
		"</td>"+				
		"</tr>"+		
		"</table>";
		
		$('div[id=wasteStreamDiv]').append(								
			strNewWasteStreams
		);		
		unitTypeListWithoutPollutions(wsPosition);		
		
		$('#selectedValue_'+wsPosition).attr('value',firstKey);
		$('#selectedValue_'+wsPosition).attr('txt',wasteStreamsWithPollutions[firstKey]['name']);		
		
		if (isHiddenAddPollution)
		{
			$("#quantityText_"+wsPosition).css('display','inline').css('zoom',1);		
			$("input:text[name=quantityWithoutPollutions_"+wsPosition+"]").css('display','inline').css('zoom',1).attr('disabled',false);
			$("select[name=selectWasteUnittypeClassWithoutPollutions_"+wsPosition+"]").css('display','inline').css('zoom',1).attr('disabled',false);
			$("select[name=selectWasteUnittypeWithoutPollutions_"+wsPosition+"]").css('display','inline').css('zoom',1).attr('disabled',false);			
			$('a[id=addPollutions_'+wsPosition+']').css('display','none');
			$('#divLine_'+wsPosition).css('display','none');
		}
		
		setStorages(wsPosition);
		
		$('#deleteWasteStream_'+wsPosition).click(function (){
			var val = parseInt ($(this).attr('onCl'));			
			delWasteStream(val);});
		$('#addPollutions_'+wsPosition).click(function(){
			var val = parseInt($(this).attr('onCl'));
			addPollution (val);});
		$('select[name=wasteStreamSelect_'+wsPosition+']').change(function(){
			var val = parseInt($(this).attr('onCh'));
			viewRowsBySelectWaste(val);
			setStorages(val);});
		$('select[name=selectWasteUnittypeClassWithoutPollutions_'+wsPosition+']').change(function(){
			var val = parseInt($(this).attr('onCh'));
			unitTypeListWithoutPollutions(val);});
		document.getElementById("wasteStreamCount").value++;		
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
		
		if (elemsArray[key]['unittypeClass']!=null)
		{
			$('input[name=quantityWithoutPollutions_'+key+']').attr('value',elemsArray[key]["value"]);
			$('select[name = selectWasteUnittypeClassWithoutPollutions_'+key+'] option[value ='+elemsArray[key]["unittypeClass"]+']').attr('selected',true);
			unitTypeListWithoutPollutions(key);
			$('select[name = selectWasteUnittypeWithoutPollutions_'+key+'] option[value ='+elemsArray[key]["unittypeID"]+']').attr('selected',true);
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
	$('a[id=addWasteStream]').bind('click',function(){viewWasteStreams();});
	
	$('select[name^=selectStorage]').each(function(el)
	{
		for (key in overflow)
		{
			if ($(this).attr('value')==overflow[key])
				$(this).after('<p style="color:red; display:inline-block"> Storage overflow!</p>') 
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
		var unittype=storages[idSelectedWasteStream][key]['volume_unittype']
		var empty=storages[idSelectedWasteStream][key]['capacity_volume']-storages[idSelectedWasteStream][key]['current_usage'];				
		strOptions+="<option value="+storages[idSelectedWasteStream][key]['storage_id']+">"+storages[idSelectedWasteStream][key]['name']+" (empty: "+empty.toFixed(2)+" "+unittype+")</option>";
	}	
	$("select[name=selectStorage_"+id+"]").append(strOptions);	
}

function viewRowsBySelectWaste(id)
{		
	var idSelectedWasteStream = $('select[name=wasteStreamSelect_'+id+']').attr('value');	
			
	if (wasteStreamsWithPollutions[idSelectedWasteStream][0]=='false')
	{		
		$("#quantityText_"+id).css('display','inline').css('zoom',1);		
		$("input:text[name=quantityWithoutPollutions_"+id+"]").css('display','inline').css('zoom',1).attr('disabled',false);
		$("select[name=selectWasteUnittypeClassWithoutPollutions_"+id+"]").css('display','inline').css('zoom',1).attr('disabled',false);
		$("select[name=selectWasteUnittypeWithoutPollutions_"+id+"]").css('display','inline').css('zoom',1).attr('disabled',false);			
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
		$('select[name = wasteStreamSelect_'+i+']').attr('onCh',need);
		$('select[name = wasteStreamSelect_'+i+']').attr('name','wasteStreamSelect_'+need);
		$("input:text[name=quantityWithoutPollutions_"+i+"]").attr('name','quantityWithoutPollutions_'+need);		
		$("select[name=selectWasteUnittypeClassWithoutPollutions_"+i+"]").attr('onCh',need);
		$("select[name=selectWasteUnittypeClassWithoutPollutions_"+i+"]").attr('name','selectWasteUnittypeClassWithoutPollutions_'+need);		
		$("select[name=selectWasteUnittypeWithoutPollutions_"+i+"]").attr('name','selectWasteUnittypeWithoutPollutions_'+need);
		$("select[name=selectStorage_"+i+"]").attr('name','selectStorage_'+need);
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
		$('select[name=selectWasteUnittype_'+id+'_'+j+']').attr('name','selectWasteUnittype_'+id+'_'+need);
		$('input[name=quantity_'+id+'_'+j+']').attr('name','quantity_'+id+'_'+need);
		$('#pollutionSelectedValue_'+id+'_'+j).attr('id','pollutionSelectedValue_'+id+'_'+need);
	}		
	
	pollutionCount--;
	$('input:hidden[name=pollutionCount_'+id+']').attr('value',pollutionCount)	
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
		var firstStep=true;
		var selVal;
		var countPoll=0;
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
		" 	Quantity:<input type='text' name = 'quantity_"+id+"_"+idPollution+"'>"+
		"	<select name='selectWasteUnittypeClass_"+id+"_"+idPollution+"' onChange='unitTypeList("+id+","+idPollution+")' onCh_ws='"+id+"' onCh_pol='"+idPollution+"'>";
		
		strNewPollution+=selectOptions2UnitTypeClasses();		
		
		strNewPollution+=																		
		"	</select>"+		
		"	<select name='selectWasteUnittype_"+id+"_"+idPollution+"' >"+																				
		"	</select>"+																						
		"</td>"+
	"</tr>";	
		
	$('table[id=wasteStreamTable_'+id+']').append(strNewPollution);
	unitTypeList(id,idPollution);
	$('#deletePollution_'+id+'_'+idPollution).click(function (){
			var idWs = parseInt ($(this).attr('onCl_ws'));
			var idPol = parseInt ($(this).attr('onCl_pol'));			
			delPollution(idWs,idPol);});
			
	$('select[name=selectPollution_'+id+'_'+idPollution+']').change(function(){
			var idWs = parseInt ($(this).attr('onCh_ws'));
			var idPol = parseInt ($(this).attr('onCh_pol'));
			selectPollution(idWs,idPol);});
			
	$('select[name=selectWasteUnittypeClass_'+id+'_'+idPollution+']').change(function(){
			var idWs = parseInt ($(this).attr('onCh_ws'));
			var idPol = parseInt ($(this).attr('onCh_pol'));
			unitTypeList(idWs,idPol);});
	
	idPollution++;
	$('input:hidden[name=pollutionCount_'+id+']').attr('value',idPollution.toString());
}



$(function(){		
	UTList=eval("("+unittypeList+")");
	
	$('select[name=fuel] option[value='+selectedEmissionFactorId+']').attr('selected',true);
	getUnittypes($('#fuel').attr('value'));	
	$('select[name=unittype] option[value='+selectedUnittype+']').attr('selected',true);	
	
	$('select[name=fuel]').change(function(){
		getUnittypes($(this).attr('value'));
	});	
	
})
function getUnittypes(val)
{	
	$.ajax({
      		url: "modules/ajax/addDirectEmission.php",      		
      		type: "POST",
      		async: false,
      		data: { "fuel":val },      			
      		dataType: "html",
      		success: function (UTArray) {
      			unittypeArray=eval("("+UTArray+")");
      			var unittypeClass = unittypeArray['unitypeClass'];      		
      			//$('#defaultUnittype').html(unittypeArray['defaultUnittype']);
      			        				      												
				$('select[name=unittype] option').remove();		
						
				for (var i=0;i<UTList[unittypeClass].length;i++)
				{					
					$('select[name=unittype]').append(
					"<option value='"+UTList[unittypeClass][i]['id']+"'>"+UTList[unittypeClass][i]['name']+"</option>");										
				}																				
      		}        		   			   	
		});	
}

/*function getDefaultUnittype(val)
{	
	$.ajax({
      		url: "modules/ajax/addDirectEmission.php",      		
      		type: "POST",
      		async: false,
      		data: { "fuel":val },      			
      		dataType: "html",
      		success: function (default) {        				      												
						
						
				for (var i=0;i<UTList[UTClass].length;i++)
				{					
					$('select[name=unittype]').append(
					"<option value='"+UTList[UTClass][i]['id']+"'>"+UTList[UTClass][i]['name']+"</option>");					
				}																				
      		}        		   			   	
		});	
}*/